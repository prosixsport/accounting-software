<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use App\Models\ExpenseSubCategory;
use Illuminate\Http\Request;

class ExpenseSubCategoryController extends Controller
{
    public function index()
    {
        $categories = ExpenseCategory::where('status', 1)->orderBy('name')->get();

        $subCategories = ExpenseSubCategory::with('category')
            ->latest()
            ->get();

        return view('expense-sub-categories.index', compact('categories', 'subCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'name' => 'required|string|max:255',
        ]);

        ExpenseSubCategory::create([
            'expense_category_id' => $request->expense_category_id,
            'name' => $request->name,
            'status' => $request->status ?? 1,
        ]);

        return back()->with('success', 'Expense sub category added successfully.');
    }

    public function update(Request $request, ExpenseSubCategory $expenseSubCategory)
    {
        $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'name' => 'required|string|max:255',
        ]);

        $expenseSubCategory->update([
            'expense_category_id' => $request->expense_category_id,
            'name' => $request->name,
            'status' => $request->status ?? 0,
        ]);

        return back()->with('success', 'Expense sub category updated successfully.');
    }

    public function destroy(ExpenseSubCategory $expenseSubCategory)
    {
        $expenseSubCategory->delete();

        return back()->with('success', 'Expense sub category deleted successfully.');
    }
}
