<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\BiometricTemplate;
use Illuminate\Http\Request;

class BiometricTemplateController extends Controller
{
    public function index()
    {
        $templates = BiometricTemplate::with('employee')
            ->latest()
            ->paginate(20);

        return view('biometric.index', compact('templates'));
    }

    public function create()
    {
$employees = Employee::orderBy('name')->get();
        return view('biometric.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([

            'employee_id' => 'required|exists:employees,id',
            'finger_name' => 'required',
            'device_name' => 'nullable'

        ]);

        BiometricTemplate::updateOrCreate(

            [
                'employee_id' => $request->employee_id
            ],

            [
                'finger_name' => $request->finger_name,
                'device_name' => $request->device_name,
                'template_data' => null,
                'is_active' => true,
            ]

        );

        return redirect()
            ->route('biometric.index')
            ->with('success', 'Employee ready for fingerprint registration.');
    }

    public function show(BiometricTemplate $biometric)
    {
        return view('biometric.show', compact('biometric'));
    }

    public function edit(BiometricTemplate $biometric)
    {
$employees = Employee::orderBy('name')->get();
        return view('biometric.edit', compact(
            'biometric',
            'employees'
        ));
    }

    public function update(Request $request, BiometricTemplate $biometric)
    {
        $request->validate([

            'employee_id' => 'required',
            'finger_name' => 'required',
            'device_name' => 'nullable'

        ]);

        $biometric->update([

            'employee_id' => $request->employee_id,
            'finger_name' => $request->finger_name,
            'device_name' => $request->device_name,

        ]);

        return redirect()
            ->route('biometric.index')
            ->with('success', 'Updated successfully.');
    }

    public function destroy(BiometricTemplate $biometric)
    {
        $biometric->delete();

        return back()->with('success', 'Deleted successfully.');
    }
}
