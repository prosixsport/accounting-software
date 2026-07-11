<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    private $permissions = [
        'dashboard' => 'Dashboard',
        'accounts' => 'Chart of Accounts',
        'employees' => 'Employees / Workers',
        'attendances' => 'Attendance',
        'payrolls' => 'Payroll / Salary',

        'contractors' => 'Contractors',
        'contractor_items' => 'Items / Rates',
        'contractor_bills' => 'Contractor Bills',

        'expense_categories' => 'Expense Categories',
        'expense_sub_categories' => 'Expense Sub Categories',
        'expenses' => 'Expenses',

        'customers' => 'Customers',
        'invoices' => 'Invoices',
        'payments' => 'Payments Received',
        'customer_ledgers' => 'Customer Ledger',
        'reports' => 'Profit & Loss',
    ];

    public function index()
    {
        $users = User::where('role', '!=', 'super_admin')
            ->latest()
            ->get();

        return view('user-management.index', compact('users'));
    }

    public function create()
    {
        $permissions = $this->permissions;

        return view('user-management.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:accountant,admin,staff',
            'permissions' => 'nullable|array',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ]);

        foreach ($request->permissions ?? [] as $permission) {
            UserPermission::create([
                'user_id' => $user->id,
                'permission_key' => $permission,
            ]);
        }

        return redirect()
            ->route('user-management.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $permissions = $this->permissions;
        $selected = $user->permissions()->pluck('permission_key')->toArray();

        return view('user-management.edit', compact('user', 'permissions', 'selected'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:accountant,admin,staff',
            'permissions' => 'nullable|array',
        ]);

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);

        $user->permissions()->delete();

        foreach ($request->permissions ?? [] as $permission) {
            UserPermission::create([
                'user_id' => $user->id,
                'permission_key' => $permission,
            ]);
        }

        return redirect()
            ->route('user-management.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return back()->with('success', 'User deleted successfully.');
    }
}
