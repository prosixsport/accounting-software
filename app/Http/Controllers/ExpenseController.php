<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\ExpenseReceiptUpload;
use App\Models\ExpenseSubCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ExpenseController extends Controller
{
    /**
     * Display expenses with category and period filters.
     */
    public function index(Request $request)
    {
        $selectedCategory = $request->filled(
            'expense_category_id'
        )
            ? (int) $request->expense_category_id
            : null;

        $selectedSubCategory = $request->filled(
            'expense_sub_category_id'
        )
            ? (int) $request->expense_sub_category_id
            : null;

        $period = $request->get('period', 'month');

        if (!in_array($period, [
            'day',
            'week',
            'month',
            'year',
            'all',
        ], true)) {
            $period = 'month';
        }

        $selectedDate = $request->get(
            'date',
            now()->format('Y-m-d')
        );

        $selectedMonth = $request->get(
            'month',
            now()->format('Y-m')
        );

        $selectedYear = (int) $request->get(
            'year',
            now()->year
        );

        /*
        |--------------------------------------------------------------------------
        | Categories
        |--------------------------------------------------------------------------
        */

        $categories = ExpenseCategory::query()
            ->where('status', true)
            ->withCount([
                'activeSubCategories as sub_categories_count',
                'expenses',
            ])
            ->orderBy('name')
            ->get();

        $selectedCategoryModel = $selectedCategory
            ? $categories->firstWhere(
                'id',
                $selectedCategory
            )
            : null;

        $subCategories = collect();

        if ($selectedCategoryModel) {
            $subCategories = ExpenseSubCategory::query()
                ->where('status', true)
                ->where(
                    'expense_category_id',
                    $selectedCategoryModel->id
                )
                ->withCount('expenses')
                ->orderBy('name')
                ->get();
        }

        $selectedCategoryHasSubCategories =
            $selectedCategoryModel
            && (int) $selectedCategoryModel
                ->sub_categories_count > 0;

        /*
         * Invalid subcategory ko ignore karo.
         */
        if (
            $selectedSubCategory
            && !$subCategories->contains(
                'id',
                $selectedSubCategory
            )
        ) {
            $selectedSubCategory = null;
        }

        /*
        |--------------------------------------------------------------------------
        | Main expense query
        |--------------------------------------------------------------------------
        */

        $expenseQuery = Expense::query()
            ->with([
                'account',
                'category',
                'subCategory',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Category filtering
        |--------------------------------------------------------------------------
        */

        if ($selectedCategoryModel) {
            $expenseQuery->where(
                'expense_category_id',
                $selectedCategoryModel->id
            );

            /*
             * Category mein subcategories hain.
             */
            if ($selectedCategoryHasSubCategories) {
                if ($selectedSubCategory) {
                    $expenseQuery->where(
                        'expense_sub_category_id',
                        $selectedSubCategory
                    );
                }
            } else {
                /*
                 * Direct category:
                 * null subcategory expenses show honge.
                 */
                $expenseQuery->whereNull(
                    'expense_sub_category_id'
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Period filtering
        |--------------------------------------------------------------------------
        */

        $periodStart = null;
        $periodEnd = null;
        $periodLabel = '';

        switch ($period) {
            case 'day':
                $day = $this->safeDate(
                    $selectedDate,
                    now()
                );

                $selectedDate = $day->format('Y-m-d');

                $periodStart = $day
                    ->copy()
                    ->startOfDay();

                $periodEnd = $day
                    ->copy()
                    ->endOfDay();

                $periodLabel = $day->format(
                    'd M Y'
                );

                $expenseQuery->whereDate(
                    'expense_date',
                    $day->toDateString()
                );
                break;

            case 'week':
                /*
                 * Selected date se aglay 7 din.
                 *
                 * Example:
                 * 15 July select ho to
                 * 15 July se 21 July tak.
                 */
                $weekDate = $this->safeDate(
                    $selectedDate,
                    now()
                );

                $selectedDate = $weekDate->format(
                    'Y-m-d'
                );

                $periodStart = $weekDate
                    ->copy()
                    ->startOfDay();

                $periodEnd = $weekDate
                    ->copy()
                    ->addDays(6)
                    ->endOfDay();

                $periodLabel =
                    $periodStart->format('d M Y')
                    . ' - '
                    . $periodEnd->format('d M Y');

                $expenseQuery->whereBetween(
                    'expense_date',
                    [
                        $periodStart->toDateString(),
                        $periodEnd->toDateString(),
                    ]
                );
                break;

            case 'year':
                if (
                    $selectedYear < 2000
                    || $selectedYear > 2100
                ) {
                    $selectedYear = now()->year;
                }

                $periodStart = Carbon::create(
                    $selectedYear,
                    1,
                    1
                )->startOfYear();

                $periodEnd = $periodStart
                    ->copy()
                    ->endOfYear();

                $periodLabel = (string) $selectedYear;

                $expenseQuery->whereYear(
                    'expense_date',
                    $selectedYear
                );
                break;

            case 'all':
                $periodLabel = 'All Time';
                break;

            case 'month':
            default:
                $monthDate = $this->safeMonth(
                    $selectedMonth
                );

                $selectedMonth = $monthDate->format(
                    'Y-m'
                );

                $periodStart = $monthDate
                    ->copy()
                    ->startOfMonth();

                $periodEnd = $monthDate
                    ->copy()
                    ->endOfMonth();

                $periodLabel = $monthDate->format(
                    'F Y'
                );

                $expenseQuery
                    ->whereMonth(
                        'expense_date',
                        $monthDate->month
                    )
                    ->whereYear(
                        'expense_date',
                        $monthDate->year
                    );

                $period = 'month';
                break;
        }

        /*
        |--------------------------------------------------------------------------
        | Load records
        |--------------------------------------------------------------------------
        */

        $expenses = $expenseQuery
            ->orderByDesc('expense_date')
            ->orderByDesc('id')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Summary calculation
        |--------------------------------------------------------------------------
        */

        $totalExpense = (float) $expenses
            ->sum('amount');

        $cashTotal = (float) $expenses
            ->where('payment_method', 'cash')
            ->sum('amount');

        $bankTotal = (float) $expenses
            ->whereIn('payment_method', [
                'bank',
                'online',
            ])
            ->sum('amount');

        $chequeTotal = (float) $expenses
            ->where('payment_method', 'cheque')
            ->sum('amount');

        $expenseCount = $expenses->count();

        $averageExpense = $expenseCount > 0
            ? $totalExpense / $expenseCount
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Daily grouped totals
        |--------------------------------------------------------------------------
        */

        $dailyTotals = $expenses
            ->groupBy(function (Expense $expense) {
                return Carbon::parse(
                    $expense->expense_date
                )->format('Y-m-d');
            })
            ->map(function ($items, $date) {
                return [
                    'date' => $date,
                    'label' => Carbon::parse($date)
                        ->format('d M'),
                    'total' => (float) $items
                        ->sum('amount'),
                    'count' => $items->count(),
                ];
            })
            ->sortBy('date')
            ->values();

        return view('expenses.index', compact(
            'expenses',
            'categories',
            'subCategories',
            'selectedCategory',
            'selectedSubCategory',
            'selectedCategoryModel',
            'selectedCategoryHasSubCategories',
            'period',
            'selectedDate',
            'selectedMonth',
            'selectedYear',
            'periodStart',
            'periodEnd',
            'periodLabel',
            'totalExpense',
            'cashTotal',
            'bankTotal',
            'chequeTotal',
            'expenseCount',
            'averageExpense',
            'dailyTotals'
        ));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $accounts = Account::query()
            ->where('type', 'expense')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $categories = ExpenseCategory::query()
            ->where('status', true)
            ->with([
                'activeSubCategories',
            ])
            ->orderBy('name')
            ->get();

        /*
         * Blade existing sub_categories key use karta hai.
         */
        $categories->each(function (
            ExpenseCategory $category
        ) {
            $category->setRelation(
                'subCategories',
                $category->activeSubCategories
            );
        });

        return view(
            'expenses.create',
            compact(
                'accounts',
                'categories'
            )
        );
    }

    /**
     * Store new expense.
     */
    public function store(Request $request)
    {
        $validated = $this->validateExpense(
            $request
        );

        $category = ExpenseCategory::query()
            ->where('status', true)
            ->with('activeSubCategories')
            ->findOrFail(
                $validated['expense_category_id']
            );

        $validated['expense_sub_category_id'] =
            $this->resolveSubCategory(
                $category,
                $validated[
                    'expense_sub_category_id'
                ] ?? null
            );

        $receiptPath = null;

        DB::transaction(function () use (
            $request,
            $validated,
            $category,
            &$receiptPath
        ) {
            $receiptPath = $this->storeReceipt(
                $request,
                $validated[
                    'mobile_receipt_token'
                ] ?? null
            );

            Expense::create([
                'expense_no' =>
                    $this->generateExpenseNumber(),

                'expense_date' =>
                    $validated['expense_date'],

                'expense_category_id' =>
                    $validated[
                        'expense_category_id'
                    ],

                'expense_sub_category_id' =>
                    $validated[
                        'expense_sub_category_id'
                    ],

                'category' => $category->name,

                'account_id' =>
                    $validated['account_id']
                    ?? null,

                'vendor_name' =>
                    $validated['vendor_name']
                    ?? null,

                'paid_by' =>
                    $validated['paid_by']
                    ?? null,

                'amount' => $validated['amount'],

                'receipt' => $receiptPath,

                'description' =>
                    $validated['description']
                    ?? null,

                'payment_method' =>
                    $validated[
                        'payment_method'
                    ],
            ]);
        });

        return redirect()
            ->route('expenses.index', [
                'expense_category_id' =>
                    $validated[
                        'expense_category_id'
                    ],

                'expense_sub_category_id' =>
                    $validated[
                        'expense_sub_category_id'
                    ],

                'period' => 'month',

                'month' => Carbon::parse(
                    $validated['expense_date']
                )->format('Y-m'),
            ])
            ->with(
                'success',
                'Expense added successfully.'
            );
    }

    /**
     * Show expense slip.
     */
    public function show(Expense $expense)
    {
        $expense->load([
            'account',
            'category',
            'subCategory',
        ]);

        return view(
            'expenses.show',
            compact('expense')
        );
    }

    /**
     * Show edit form.
     */
    public function edit(Expense $expense)
    {
        $accounts = Account::query()
            ->where('type', 'expense')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $categories = ExpenseCategory::query()
            ->where('status', true)
            ->with([
                'activeSubCategories',
            ])
            ->orderBy('name')
            ->get();

        $categories->each(function (
            ExpenseCategory $category
        ) {
            $category->setRelation(
                'subCategories',
                $category->activeSubCategories
            );
        });

        return view(
            'expenses.edit',
            compact(
                'expense',
                'accounts',
                'categories'
            )
        );
    }

    /**
     * Update expense.
     */
    public function update(
        Request $request,
        Expense $expense
    ) {
        $validated = $this->validateExpense(
            $request
        );

        $category = ExpenseCategory::query()
            ->where('status', true)
            ->with('activeSubCategories')
            ->findOrFail(
                $validated['expense_category_id']
            );

        $validated['expense_sub_category_id'] =
            $this->resolveSubCategory(
                $category,
                $validated[
                    'expense_sub_category_id'
                ] ?? null
            );

        $receiptPath = $expense->receipt;

        if ($request->hasFile('receipt')) {
            $this->deleteReceipt(
                $expense->receipt
            );

            $receiptPath = $request
                ->file('receipt')
                ->store(
                    'receipts',
                    'public'
                );
        }

        $expense->update([
            'expense_date' =>
                $validated['expense_date'],

            'expense_category_id' =>
                $validated[
                    'expense_category_id'
                ],

            'expense_sub_category_id' =>
                $validated[
                    'expense_sub_category_id'
                ],

            'category' => $category->name,

            'account_id' =>
                $validated['account_id']
                ?? null,

            'vendor_name' =>
                $validated['vendor_name']
                ?? null,

            'paid_by' =>
                $validated['paid_by']
                ?? null,

            'amount' => $validated['amount'],

            'receipt' => $receiptPath,

            'description' =>
                $validated['description']
                ?? null,

            'payment_method' =>
                $validated['payment_method'],
        ]);

        return redirect()
            ->route('expenses.index', [
                'expense_category_id' =>
                    $expense->expense_category_id,

                'expense_sub_category_id' =>
                    $expense
                        ->expense_sub_category_id,

                'period' => 'month',

                'month' => Carbon::parse(
                    $expense->expense_date
                )->format('Y-m'),
            ])
            ->with(
                'success',
                'Expense updated successfully.'
            );
    }

    /**
     * Delete expense.
     */
    public function destroy(Expense $expense)
    {
        $categoryId =
            $expense->expense_category_id;

        $subCategoryId =
            $expense->expense_sub_category_id;

        $expenseMonth = Carbon::parse(
            $expense->expense_date
        )->format('Y-m');

        $this->deleteReceipt(
            $expense->receipt
        );

        $expense->delete();

        return redirect()
            ->route('expenses.index', [
                'expense_category_id' =>
                    $categoryId,

                'expense_sub_category_id' =>
                    $subCategoryId,

                'period' => 'month',

                'month' => $expenseMonth,
            ])
            ->with(
                'success',
                'Expense deleted successfully.'
            );
    }

    /**
     * Common expense validation.
     */
    private function validateExpense(
        Request $request
    ): array {
        return $request->validate([
            'expense_date' => [
                'required',
                'date',
            ],

            'expense_category_id' => [
                'required',
                'integer',
                Rule::exists(
                    'expense_categories',
                    'id'
                )->where(function ($query) {
                    return $query->where(
                        'status',
                        true
                    );
                }),
            ],

            'expense_sub_category_id' => [
                'nullable',
                'integer',
                'exists:expense_sub_categories,id',
            ],

            'account_id' => [
                'nullable',
                'integer',
                'exists:accounts,id',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'payment_method' => [
                'required',
                Rule::in([
                    'cash',
                    'bank',
                    'cheque',
                    'online',
                ]),
            ],

            'paid_by' => [
                'nullable',
                'string',
                'max:255',
            ],

            'vendor_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'receipt' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp,pdf',
                'max:102400',
            ],

            'mobile_receipt_token' => [
                'nullable',
                'string',
                'max:100',
            ],
        ]);
    }

    /**
     * Validate selected subcategory.
     */
    private function resolveSubCategory(
        ExpenseCategory $category,
        mixed $subCategoryId
    ): ?int {
        /*
         * Category ki koi active subcategory nahi.
         */
        if (
            $category
                ->activeSubCategories
                ->isEmpty()
        ) {
            return null;
        }

        if (!$subCategoryId) {
            abort(
                back()
                    ->withErrors([
                        'expense_sub_category_id' =>
                            'Please select a sub category.',
                    ])
                    ->withInput()
            );
        }

        $subCategory = $category
            ->activeSubCategories
            ->firstWhere(
                'id',
                (int) $subCategoryId
            );

        if (!$subCategory) {
            abort(
                back()
                    ->withErrors([
                        'expense_sub_category_id' =>
                            'Selected sub category does not belong to this category.',
                    ])
                    ->withInput()
            );
        }

        return (int) $subCategory->id;
    }

    /**
     * Store computer or mobile receipt.
     */
    private function storeReceipt(
        Request $request,
        ?string $mobileReceiptToken
    ): ?string {
        /*
         * Computer upload takes priority.
         */
        if ($request->hasFile('receipt')) {
            return $request
                ->file('receipt')
                ->store(
                    'receipts',
                    'public'
                );
        }

        if (!$mobileReceiptToken) {
            return null;
        }

        $mobileUpload = ExpenseReceiptUpload::query()
            ->where(
                'token',
                $mobileReceiptToken
            )
            ->where('status', 'uploaded')
            ->first();

        if (
            !$mobileUpload
            || !$mobileUpload->file_path
            || !Storage::disk('public')->exists(
                $mobileUpload->file_path
            )
        ) {
            return null;
        }

        $extension = strtolower(
            pathinfo(
                $mobileUpload->file_path,
                PATHINFO_EXTENSION
            )
        );

        $newPath =
            'receipts/'
            . now()->format('YmdHis')
            . '_'
            . uniqid('', true)
            . '.'
            . $extension;

        Storage::disk('public')->move(
            $mobileUpload->file_path,
            $newPath
        );

        $mobileUpload->update([
            'file_path' => $newPath,
            'status' => 'completed',
        ]);

        return $newPath;
    }

    /**
     * Delete receipt from public storage.
     */
    private function deleteReceipt(
        ?string $path
    ): void {
        if (
            $path
            && Storage::disk('public')->exists(
                $path
            )
        ) {
            Storage::disk('public')->delete(
                $path
            );
        }
    }

    /**
     * Generate unique expense number.
     */
    private function generateExpenseNumber(): string
    {
        $lastExpense = Expense::query()
            ->lockForUpdate()
            ->latest('id')
            ->first();

        $nextNumber = $lastExpense
            ? $lastExpense->id + 1
            : 1;

        return 'EXP-'
            . str_pad(
                $nextNumber,
                6,
                '0',
                STR_PAD_LEFT
            );
    }

    /**
     * Safely parse date.
     */
    private function safeDate(
        ?string $date,
        Carbon $fallback
    ): Carbon {
        try {
            return Carbon::parse($date);
        } catch (\Throwable $exception) {
            return $fallback->copy();
        }
    }

    /**
     * Safely parse YYYY-MM month.
     */
    private function safeMonth(
        ?string $month
    ): Carbon {
        try {
            return Carbon::createFromFormat(
                'Y-m',
                (string) $month
            )->startOfMonth();
        } catch (\Throwable $exception) {
            return now()->startOfMonth();
        }
    }
}
