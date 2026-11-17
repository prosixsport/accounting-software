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
            'categories' => self::CATEGORIES,
            'entries' => $entries,
            'totalPaise' => $entries->sum('amount_paise'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'expense_date' => ['required', 'date_format:Y-m-d'],
            'category' => ['required', Rule::in(self::CATEGORIES)],
            'description' => ['required', 'string', 'max:150'],
            'amount' => ['bail', 'required', 'numeric', 'min:0.01', 'regex:/^\d{1,9}(?:\.\d{1,2})?$/D'],
        ]);

        [$rupees, $paise] = array_pad(explode('.', (string) $data['amount'], 2), 2, '');
        DB::table('house_expenses')->insert([
            'user_id' => $request->user()->getAuthIdentifier(),
            'expense_date' => $data['expense_date'],
            'category' => $data['category'],
            'description' => $data['description'],
            'amount_paise' => (int) $rupees * 100 + (int) str_pad($paise, 2, '0'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('house-expenses.index', ['date' => $data['expense_date']])
            ->with('house_expense_saved', 'Expense added.');
    }
}
