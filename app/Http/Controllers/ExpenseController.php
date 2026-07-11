<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Account;
use App\Models\ExpenseCategory;
use App\Models\ExpenseSubCategory;
use Illuminate\Http\Request;

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
            $expenses = Expense::with(['account', 'category', 'subCategory'])
                ->where('expense_sub_category_id', $selectedSubCategory)
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
            ->get();

        $categories = ExpenseCategory::where('status', 1)
            ->with(['subCategories' => function ($q) {
                $q->where('status', 1)->orderBy('name');
            }])
            ->orderBy('name')
            ->get();

        return view('expenses.create', compact('accounts', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'expense_date' => 'required|date',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'expense_sub_category_id' => 'nullable|exists:expense_sub_categories,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required',
        ]);

        $receiptPath = null;

        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('receipts', 'public');
        }

        $category = ExpenseCategory::find($request->expense_category_id);

        Expense::create([
            'expense_no' => 'EXP-' . str_pad(Expense::count() + 1, 4, '0', STR_PAD_LEFT),
            'expense_date' => $request->expense_date,
            'expense_category_id' => $request->expense_category_id,
            'expense_sub_category_id' => $request->expense_sub_category_id,
            'category' => $category?->name,
            'account_id' => $request->account_id,
            'vendor_name' => $request->vendor_name,
            'paid_by' => $request->paid_by,
            'amount' => $request->amount,
            'receipt' => $receiptPath,
            'description' => $request->description,
            'payment_method' => $request->payment_method,
        ]);

        return redirect()
            ->route('expenses.index', [
                'expense_category_id' => $request->expense_category_id,
                'expense_sub_category_id' => $request->expense_sub_category_id,
            ])
            ->with('success', 'Expense added successfully.');
    }

    public function edit(Expense $expense)
    {
        $accounts = Account::where('type', 'expense')
            ->where('is_active', 1)
            ->get();

        $categories = ExpenseCategory::where('status', 1)
            ->with(['subCategories' => function ($q) {
                $q->where('status', 1)->orderBy('name');
            }])
            ->orderBy('name')
            ->get();

        return view('expenses.edit', compact('expense', 'accounts', 'categories'));
    }

    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'expense_date' => 'required|date',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'expense_sub_category_id' => 'nullable|exists:expense_sub_categories,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required',
        ]);

        $receiptPath = $expense->receipt;

        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('receipts', 'public');
        }

        $category = ExpenseCategory::find($request->expense_category_id);

        $expense->update([
            'expense_date' => $request->expense_date,
            'expense_category_id' => $request->expense_category_id,
            'expense_sub_category_id' => $request->expense_sub_category_id,
            'category' => $category?->name,
            'account_id' => $request->account_id,
            'vendor_name' => $request->vendor_name,
            'paid_by' => $request->paid_by,
            'amount' => $request->amount,
            'receipt' => $receiptPath,
            'description' => $request->description,
            'payment_method' => $request->payment_method,
        ]);

        return redirect()
            ->route('expenses.index', [
                'expense_category_id' => $request->expense_category_id,
                'expense_sub_category_id' => $request->expense_sub_category_id,
            ])
            ->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $categoryId = $expense->expense_category_id;
        $subCategoryId = $expense->expense_sub_category_id;

        $expense->delete();

        return redirect()
            ->route('expenses.index', [
                'expense_category_id' => $categoryId,
                'expense_sub_category_id' => $subCategoryId,
            ])
            ->with('success', 'Expense deleted successfully.');
    }
}
