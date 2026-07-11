<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendances = Attendance::with('employee')
            ->latest()
            ->get();

        return view('attendances.index', compact('attendances'));
    }

    public function create()
    {
        $employees = Employee::where('status', 'active')->get();

        return view('attendances.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required',
            'attendance_date' => 'required|date',
            'status' => 'required',
        ]);

        Attendance::updateOrCreate(
            [
                'employee_id' => $request->employee_id,
                'attendance_date' => $request->attendance_date,
            ],
            [
                'status' => $request->status,
                'check_in' => $request->check_in,
                'check_out' => $request->check_out,
                'overtime_hours' => $request->overtime_hours ?? 0,
                'remarks' => $request->remarks,
            ]
        );

        return redirect()
            ->route('attendances.index')
            ->with('success', 'Attendance saved successfully.');
    }

    public function edit(Attendance $attendance)
    {
        $employees = Employee::where('status', 'active')->get();

        return view('attendances.edit', compact('attendance', 'employees'));
    }

    public function update(Request $request, Attendance $attendance)
    {
        $request->validate([
            'employee_id' => 'required',
            'attendance_date' => 'required|date',
            'status' => 'required',
        ]);

        $attendance->update([
            'employee_id' => $request->employee_id,
            'attendance_date' => $request->attendance_date,
            'status' => $request->status,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'overtime_hours' => $request->overtime_hours ?? 0,
            'remarks' => $request->remarks,
        ]);

        return redirect()
            ->route('attendances.index')
            ->with('success', 'Attendance updated successfully.');
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();

        return redirect()
            ->route('attendances.index')
            ->with('success', 'Attendance deleted successfully.');
    }
}
