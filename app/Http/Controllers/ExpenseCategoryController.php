<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        $categories = ExpenseCategory::latest()->get();

        return view('expense-categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name',
        ]);

        ExpenseCategory::create([
            'name' => $request->name,
            'status' => $request->status ?? 1,
        ]);

        return back()->with('success', 'Expense category added successfully.');
    }

    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name,' . $expenseCategory->id,
        ]);

        $expenseCategory->update([
            'name' => $request->name,
            'status' => $request->status ?? 0,
        ]);

        return back()->with('success', 'Expense category updated successfully.');
    }

    public function destroy(ExpenseCategory $expenseCategory)
    {
        $expenseCategory->delete();

        return back()->with('success', 'Expense category deleted successfully.');
    }
}
