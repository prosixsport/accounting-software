<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Account;
use App\Models\ExpenseCategory;
use App\Models\ExpenseSubCategory;
use App\Models\ExpenseReceiptUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $selectedCategory = $request->expense_category_id;
        $selectedSubCategory = $request->expense_sub_category_id;

        $categories = ExpenseCategory::where('status', 1)
            ->orderBy('name')
            ->get();

        $subCategories = collect();

        if ($selectedCategory) {
            $subCategories = ExpenseSubCategory::where('status', 1)
                ->where('expense_category_id', $selectedCategory)
                ->orderBy('name')
                ->get();
        }

        $expenses = collect();

        if ($selectedSubCategory) {
            $expenses = Expense::with([
                'account',
                'category',
                'subCategory',
            ])
                ->where(
                    'expense_sub_category_id',
                    $selectedSubCategory
                )
                ->latest()
                ->get();
        }

        $totalExpense = $expenses->sum('amount');

        return view('expenses.index', compact(
            'expenses',
            'categories',
            'subCategories',
            'selectedCategory',
            'selectedSubCategory',
            'totalExpense'
        ));
    }

    public function create()
    {
        $accounts = Account::where('type', 'expense')
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        $categories = ExpenseCategory::where('status', 1)
            ->with([
                'subCategories' => function ($query) {
                    $query
                        ->where('status', 1)
                        ->orderBy('name');
                },
            ])
            ->orderBy('name')
            ->get();

        return view('expenses.create', compact(
            'accounts',
            'categories'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_date' => [
                'required',
                'date',
            ],

            'expense_category_id' => [
                'required',
                'exists:expense_categories,id',
            ],

            'expense_sub_category_id' => [
                'nullable',
                'exists:expense_sub_categories,id',
            ],

            'account_id' => [
                'nullable',
                'exists:accounts,id',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:0',
            ],

            'payment_method' => [
                'required',
                'in:cash,bank,cheque,online',
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
            ],

            'receipt' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp,pdf',
                'max:10240',
            ],

            'mobile_receipt_token' => [
                'nullable',
                'string',
                'max:100',
            ],
        ]);

        $receiptPath = null;
        $mobileUpload = null;

        /*
        |--------------------------------------------------------------------------
        | Computer se normal receipt upload
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('receipt')) {
            $receiptPath = $request
                ->file('receipt')
                ->store('expense-receipts', 'public');
        }

        /*
        |--------------------------------------------------------------------------
        | Mobile QR se uploaded receipt
        |--------------------------------------------------------------------------
        */

        if (
            !$receiptPath &&
            $request->filled('mobile_receipt_token')
        ) {
            $mobileUpload = ExpenseReceiptUpload::where(
                'token',
                $request->mobile_receipt_token
            )
                ->where('status', 'uploaded')
                ->first();

            if (
                $mobileUpload &&
                $mobileUpload->file_path &&
                Storage::disk('public')->exists(
                    $mobileUpload->file_path
                )
            ) {
                $extension = pathinfo(
                    $mobileUpload->file_path,
                    PATHINFO_EXTENSION
                );

                if (!$extension) {
                    $extension = 'jpg';
                }

                $newFileName = now()->format('YmdHis')
                    . '_'
                    . Str::random(12)
                    . '.'
                    . $extension;

                $newPath = 'expense-receipts/' . $newFileName;

                Storage::disk('public')->move(
                    $mobileUpload->file_path,
                    $newPath
                );

                $receiptPath = $newPath;
            }
        }

        $category = ExpenseCategory::find(
            $validated['expense_category_id']
        );

        $expense = Expense::create([
            'expense_no' => $this->generateExpenseNumber(),
            'expense_date' => $validated['expense_date'],
            'expense_category_id' => $validated['expense_category_id'],
            'expense_sub_category_id' => $validated['expense_sub_category_id'] ?? null,
            'category' => $category?->name,
            'account_id' => $validated['account_id'] ?? null,
            'vendor_name' => $validated['vendor_name'] ?? null,
            'paid_by' => $validated['paid_by'] ?? null,
            'amount' => $validated['amount'],
            'receipt' => $receiptPath,
            'description' => $validated['description'] ?? null,
            'payment_method' => $validated['payment_method'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Mobile upload session completed mark karein
        |--------------------------------------------------------------------------
        */

        if ($mobileUpload && $receiptPath) {
            $mobileUpload->update([
                'file_path' => $receiptPath,
                'status' => 'completed',
            ]);
        }

        return redirect()
            ->route('expenses.index', [
                'expense_category_id' => $expense->expense_category_id,
                'expense_sub_category_id' => $expense->expense_sub_category_id,
            ])
            ->with('success', 'Expense added successfully.');
    }

    public function edit(Expense $expense)
    {
        $accounts = Account::where('type', 'expense')
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        $categories = ExpenseCategory::where('status', 1)
            ->with([
                'subCategories' => function ($query) {
                    $query
                        ->where('status', 1)
                        ->orderBy('name');
                },
            ])
            ->orderBy('name')
            ->get();

        return view('expenses.edit', compact(
            'expense',
            'accounts',
            'categories'
        ));
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'expense_date' => [
                'required',
                'date',
            ],

            'expense_category_id' => [
                'required',
                'exists:expense_categories,id',
            ],

            'expense_sub_category_id' => [
                'nullable',
                'exists:expense_sub_categories,id',
            ],

            'account_id' => [
                'nullable',
                'exists:accounts,id',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:0',
            ],

            'payment_method' => [
                'required',
                'in:cash,bank,cheque,online',
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
            ],

            'receipt' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp,pdf',
                'max:10240',
            ],

            'mobile_receipt_token' => [
                'nullable',
                'string',
                'max:100',
            ],
        ]);

        $receiptPath = $expense->receipt;
        $mobileUpload = null;

        /*
        |--------------------------------------------------------------------------
        | New computer receipt upload
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('receipt')) {
            $this->deleteReceiptFile($expense->receipt);

            $receiptPath = $request
                ->file('receipt')
                ->store('expense-receipts', 'public');
        }

        /*
        |--------------------------------------------------------------------------
        | New mobile QR receipt upload
        |--------------------------------------------------------------------------
        */

        if (
            !$request->hasFile('receipt') &&
            $request->filled('mobile_receipt_token')
        ) {
            $mobileUpload = ExpenseReceiptUpload::where(
                'token',
                $request->mobile_receipt_token
            )
                ->where('status', 'uploaded')
                ->first();

            if (
                $mobileUpload &&
                $mobileUpload->file_path &&
                Storage::disk('public')->exists(
                    $mobileUpload->file_path
                )
            ) {
                $this->deleteReceiptFile($expense->receipt);

                $extension = pathinfo(
                    $mobileUpload->file_path,
                    PATHINFO_EXTENSION
                );

                if (!$extension) {
                    $extension = 'jpg';
                }

                $newFileName = now()->format('YmdHis')
                    . '_'
                    . Str::random(12)
                    . '.'
                    . $extension;

                $newPath = 'expense-receipts/' . $newFileName;

                Storage::disk('public')->move(
                    $mobileUpload->file_path,
                    $newPath
                );

                $receiptPath = $newPath;
            }
        }

        $category = ExpenseCategory::find(
            $validated['expense_category_id']
        );

        $expense->update([
            'expense_date' => $validated['expense_date'],
            'expense_category_id' => $validated['expense_category_id'],
            'expense_sub_category_id' => $validated['expense_sub_category_id'] ?? null,
            'category' => $category?->name,
            'account_id' => $validated['account_id'] ?? null,
            'vendor_name' => $validated['vendor_name'] ?? null,
            'paid_by' => $validated['paid_by'] ?? null,
            'amount' => $validated['amount'],
            'receipt' => $receiptPath,
            'description' => $validated['description'] ?? null,
            'payment_method' => $validated['payment_method'],
        ]);

        if ($mobileUpload && $receiptPath) {
            $mobileUpload->update([
                'file_path' => $receiptPath,
                'status' => 'completed',
            ]);
        }

        return redirect()
            ->route('expenses.index', [
                'expense_category_id' => $expense->expense_category_id,
                'expense_sub_category_id' => $expense->expense_sub_category_id,
            ])
            ->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $categoryId = $expense->expense_category_id;
        $subCategoryId = $expense->expense_sub_category_id;

        $this->deleteReceiptFile($expense->receipt);

        $expense->delete();

        return redirect()
            ->route('expenses.index', [
                'expense_category_id' => $categoryId,
                'expense_sub_category_id' => $subCategoryId,
            ])
            ->with('success', 'Expense deleted successfully.');
    }

    /**
     * Unique expense number generate karega.
     */
    private function generateExpenseNumber(): string
    {
        $lastExpense = Expense::query()
            ->orderByDesc('id')
            ->first();

        $nextNumber = ($lastExpense?->id ?? 0) + 1;

        $expenseNumber = 'EXP-'
            . str_pad(
                (string) $nextNumber,
                4,
                '0',
                STR_PAD_LEFT
            );

        while (
            Expense::where('expense_no', $expenseNumber)->exists()
        ) {
            $nextNumber++;

            $expenseNumber = 'EXP-'
                . str_pad(
                    (string) $nextNumber,
                    4,
                    '0',
                    STR_PAD_LEFT
                );
        }

        return $expenseNumber;
    }

    /**
     * Purani receipt storage se delete karega.
     */
    private function deleteReceiptFile(?string $path): void
    {
        if (
            $path &&
            Storage::disk('public')->exists($path)
        ) {
            Storage::disk('public')->delete($path);
        }
    }
}
