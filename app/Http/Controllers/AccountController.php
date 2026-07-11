<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = Account::latest()->get();
        return view('accounts.index', compact('accounts'));
    }

    public function create()
    {
        return view('accounts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:accounts,code',
            'name' => 'required|string|max:255',
            'type' => 'required',
            'opening_balance' => 'nullable|numeric',
        ]);
 
        Account::create([
            'code' => $request->code,
            'name' => $request->name,
            'type' => $request->type,
            'opening_balance' => $request->opening_balance ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect('/accounts')->with('success', 'Account created successfully.');
    }

    public function edit(Account $account)
    {
        return view('accounts.edit', compact('account'));
    }

    public function update(Request $request, Account $account)
    {
        $request->validate([
            'code' => 'required|unique:accounts,code,' . $account->id,
            'name' => 'required|string|max:255',
            'type' => 'required',
            'opening_balance' => 'nullable|numeric',
        ]);

        $account->update([
            'code' => $request->code,
            'name' => $request->name,
            'type' => $request->type,
            'opening_balance' => $request->opening_balance ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect('/accounts')->with('success', 'Account updated successfully.');
    }

    public function destroy(Account $account)
    {
        $account->delete();

        return redirect('/accounts')->with('success', 'Account deleted successfully.');
    }
}
