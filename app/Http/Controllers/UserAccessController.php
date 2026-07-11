<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserAccessController extends Controller
{
    private $permissions = [
        'dashboard' => 'Dashboard',
        'accounts' => 'Chart of Accounts',
        'employees' => 'Employees / Workers',
        'attendances' => 'Attendance',
        'payrolls' => 'Payroll / Salary',
'biometric' => 'Biometric Register Finger',
'salary_verifications' => 'Salary Verification',
'monthly_alerts' => 'Monthly Alerts',
'monthly_alert_schedules' => 'Monthly Alert Schedule',
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

        return view('user-access.index', [
            'users' => $users,
            'permissions' => $this->permissions,
        ]);
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
            ->route('user-access.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $selected = $user->permissions()
            ->pluck('permission_key')
            ->toArray();

        return view('user-access.edit', [
            'user' => $user,
            'permissions' => $this->permissions,
            'selected' => $selected,
        ]);
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
            ->route('user-access.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->role === 'super_admin') {
            return back()->withErrors('Super admin cannot be deleted.');
        }

        $user->delete();

        return redirect()
            ->route('user-access.index')
            ->with('success', 'User deleted successfully.');
    }
}
