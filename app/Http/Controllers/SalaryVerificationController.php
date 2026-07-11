<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Payroll;
use App\Models\SalaryVerification;
use Illuminate\Http\Request;

class SalaryVerificationController extends Controller
{
    public function index()
    {
        $verifications = SalaryVerification::with(['employee', 'payroll', 'verifier'])
            ->latest()
            ->paginate(20);

        return view('salary-verifications.index', compact('verifications'));
    }

    public function create()
    {
$employees = Employee::orderBy('name')->get();
        $payrolls = Payroll::with('employee')->latest()->get();

        return view('salary-verifications.create', compact('employees', 'payrolls'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'payroll_id' => 'nullable|exists:payrolls,id',
            'verification_status' => 'required|in:pending,verified,failed',
            'device_name' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        $data['verified_by'] = auth()->id();

        if ($data['verification_status'] === 'verified') {
            $data['verified_at'] = now();
        }

        SalaryVerification::create($data);

        return redirect()
            ->route('salary-verifications.index')
            ->with('success', 'Salary verification saved successfully.');
    }

    public function show(SalaryVerification $salaryVerification)
    {
        $salaryVerification->load(['employee', 'payroll', 'verifier']);

        return view('salary-verifications.show', compact('salaryVerification'));
    }

    public function edit(SalaryVerification $salaryVerification)
    {
$employees = Employee::orderBy('name')->get();
        $payrolls = Payroll::with('employee')->latest()->get();

        return view('salary-verifications.edit', compact('salaryVerification', 'employees', 'payrolls'));
    }

    public function update(Request $request, SalaryVerification $salaryVerification)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'payroll_id' => 'nullable|exists:payrolls,id',
            'verification_status' => 'required|in:pending,verified,failed',
            'device_name' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        if ($data['verification_status'] === 'verified' && !$salaryVerification->verified_at) {
            $data['verified_at'] = now();
        }

        if ($data['verification_status'] !== 'verified') {
            $data['verified_at'] = null;
        }

        $salaryVerification->update($data);

        return redirect()
            ->route('salary-verifications.index')
            ->with('success', 'Salary verification updated successfully.');
    }

    public function destroy(SalaryVerification $salaryVerification)
    {
        $salaryVerification->delete();

        return redirect()
            ->route('salary-verifications.index')
            ->with('success', 'Salary verification deleted successfully.');
    }
}
