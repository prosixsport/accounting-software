<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class HouseExpenseController extends Controller
{
    private const CATEGORIES = [
        'Groceries', 'Milk & Bread', 'Vegetables & Fruit', 'Meat & Eggs',
        'Electricity, Gas & Water', 'Rent', 'Medicines', 'School',
        'Transport', 'Repairs', 'Other',
    ];

    private function categories(Request $request): array
    {
        return array_merge(self::CATEGORIES, DB::table('house_expense_categories')
            ->where('user_id', $request->user()->getAuthIdentifier())
            ->orderBy('name')->pluck('name')->all());
    }

    public function storeCategory(Request $request)
    {
        $data = $request->validate([
            'date' => ['required', 'date_format:Y-m-d'],
            'name' => ['required', 'string', 'max:40'],
        ]);
        $name = trim($data['name']);
        foreach ($this->categories($request) as $existing) {
            if (mb_strtolower($existing) === mb_strtolower($name)) {
                return back()->withErrors(['name' => 'This category already exists.']);
            }
        }
        DB::table('house_expense_categories')->insertOrIgnore([
            'user_id' => $request->user()->getAuthIdentifier(),
            'name' => $name,
        ]);

        return redirect()->route('house-expenses.index', ['date' => $data['date']])
            ->with('house_expense_saved', 'Category added.')
            ->with('house_category_selected', $name);
    }

    public function printReport(Request $request)
    {
        $data = $request->validate([
            'from' => ['required', 'date_format:Y-m-d'],
            'to' => ['required', 'date_format:Y-m-d', 'after_or_equal:from'],
        ]);
        $entries = DB::table('house_expenses')
            ->where('user_id', $request->user()->getAuthIdentifier())
            ->whereBetween('expense_date', [$data['from'], $data['to']])
            ->orderBy('expense_date')->orderBy('id')->get();

        return view('house-expenses.print', $data + [
            'days' => $entries->groupBy('expense_date'),
            'totalPaise' => $entries->sum('amount_paise'),
        ]);
    }

    public function index(Request $request)
    {
        $data = $request->validate(['date' => ['nullable', 'date_format:Y-m-d']]);
        $date = $data['date'] ?? now('Asia/Karachi')->toDateString();

        $entries = DB::table('house_expenses')
            ->where('user_id', $request->user()->getAuthIdentifier())
            ->where('expense_date', $date)
            ->orderByDesc('id')->get();

        return view('house-expenses.index', [
            'date' => $date,
            'categories' => $this->categories($request),
            'entries' => $entries,
            'totalPaise' => $entries->sum('amount_paise'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'expense_date' => ['required', 'date_format:Y-m-d'],
            'category' => ['required', Rule::in($this->categories($request))],
            'description' => ['required', 'string', 'max:150'],
            'location' => ['required', 'string', 'max:150'],
            'amount' => ['bail', 'required', 'numeric', 'min:0.01', 'regex:/^\d{1,9}(?:\.\d{1,2})?$/D'],
        ]);

        [$rupees, $paise] = array_pad(explode('.', (string) $data['amount'], 2), 2, '');
        DB::table('house_expenses')->insert([
            'user_id' => $request->user()->getAuthIdentifier(),
            'expense_date' => $data['expense_date'],
            'category' => $data['category'],
            'description' => $data['description'],
            'location' => $data['location'],
            'amount_paise' => (int) $rupees * 100 + (int) str_pad($paise, 2, '0'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('house-expenses.index', ['date' => $data['expense_date']])
            ->with('house_expense_saved', 'Expense added.');
    }
}
