<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::latest()->get();

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required',
            'phone' => 'nullable',
            'email' => 'nullable|email',
            'opening_balance' => 'nullable|numeric',
        ]);

        Customer::create([
            'customer_code' => 'CUS-' . str_pad(Customer::count() + 1, 4, '0', STR_PAD_LEFT),
            'customer_name' => $request->customer_name,
            'company_name' => $request->company_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'opening_balance' => $request->opening_balance ?? 0,
            'status' => $request->status ?? 'active',
            'notes' => $request->notes,
        ]);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer added successfully.');
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'customer_name' => 'required',
            'phone' => 'nullable',
            'email' => 'nullable|email',
            'opening_balance' => 'nullable|numeric',
        ]);

        $customer->update([
            'customer_name' => $request->customer_name,
            'company_name' => $request->company_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'opening_balance' => $request->opening_balance ?? 0,
            'status' => $request->status ?? 'active',
            'notes' => $request->notes,
        ]);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer deleted successfully.');
    }
}
