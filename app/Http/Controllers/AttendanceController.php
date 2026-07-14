<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $year = (int) ($request->year ?? now()->year);
        $month = (int) ($request->month ?? now()->month);
        $search = trim((string) $request->search);

        $employees = Employee::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($employeeQuery) use ($search) {
                    $employeeQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('employee_code', 'like', "%{$search}%")
                        ->orWhere('department', 'like', "%{$search}%")
                        ->orWhere('designation', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->get();

        $monthDate = Carbon::create($year, $month, 1);

        $startDate = $monthDate
            ->copy()
            ->startOfMonth()
            ->toDateString();

        $endDate = $monthDate
            ->copy()
            ->endOfMonth()
            ->toDateString();

        $attendanceSummary = Attendance::query()
            ->whereBetween('attendance_date', [
                $startDate,
                $endDate,
            ])
            ->selectRaw("
                employee_id,
                SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_days,
                SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_days,
                SUM(CASE WHEN status = 'leave' THEN 1 ELSE 0 END) as leave_days,
                SUM(CASE WHEN status = 'half_day' THEN 1 ELSE 0 END) as half_days,
                SUM(
                    CASE
                        WHEN status = 'present'
                        AND source = 'manual'
                        THEN 1
                        ELSE 0
                    END
                ) as manual_days,
                SUM(
                    CASE
                        WHEN status = 'present'
                        AND source = 'biometric'
                        THEN 1
                        ELSE 0
                    END
                ) as biometric_days
            ")
            ->groupBy('employee_id')
            ->get()
            ->keyBy('employee_id');

        $months = collect(range(1, 12))
            ->map(function ($monthNumber) {
                return [
                    'number' => $monthNumber,
                    'name' => Carbon::create(
                        now()->year,
                        $monthNumber,
                        1
                    )->format('F'),
                ];
            });

        return view('attendances.index', compact(
            'employees',
            'attendanceSummary',
            'months',
            'year',
            'month',
            'search'
        ));
    }

    /**
     * Employee monthly attendance calendar.
     */
    public function calendar(
        Request $request,
        Employee $employee
    ) {
        $year = (int) ($request->year ?? now()->year);
        $month = (int) ($request->month ?? now()->month);

        $monthDate = Carbon::create($year, $month, 1);

        $startDate = $monthDate
            ->copy()
            ->startOfMonth();

        $endDate = $monthDate
            ->copy()
            ->endOfMonth();

        $attendances = Attendance::query()
            ->where('employee_id', $employee->id)
            ->whereBetween('attendance_date', [
                $startDate->toDateString(),
                $endDate->toDateString(),
            ])
            ->get()
            ->keyBy(function ($attendance) {
                return Carbon::parse(
                    $attendance->attendance_date
                )->format('Y-m-d');
            });

        $calendarDays = collect();

        /*
         * Monday first day.
         */
        for (
            $emptyDay = 1;
            $emptyDay < $startDate->dayOfWeekIso;
            $emptyDay++
        ) {
            $calendarDays->push(null);
        }

        for (
            $day = 1;
            $day <= $monthDate->daysInMonth;
            $day++
        ) {
            $date = Carbon::create(
                $year,
                $month,
                $day
            );

            $dateKey = $date->format('Y-m-d');

            $calendarDays->push([
                'day' => $day,
                'date' => $dateKey,
                'date_label' => $date->format('d M Y'),
                'is_today' => $date->isToday(),
                'is_sunday' => $date->isSunday(),
                'attendance' => $attendances->get($dateKey),
            ]);
        }

        $manualPresentDays = $attendances
            ->filter(function ($attendance) {
                return $attendance->status === 'present'
                    && ($attendance->source ?? 'manual') === 'manual';
            })
            ->count();

        $biometricPresentDays = $attendances
            ->filter(function ($attendance) {
                return $attendance->status === 'present'
                    && $attendance->source === 'biometric';
            })
            ->count();

        $absentDays = $attendances
            ->where('status', 'absent')
            ->count();

        $leaveDays = $attendances
            ->where('status', 'leave')
            ->count();

        $halfDays = $attendances
            ->where('status', 'half_day')
            ->count();

        $months = collect(range(1, 12))
            ->map(function ($monthNumber) use ($year) {
                return [
                    'number' => $monthNumber,
                    'name' => Carbon::create(
                        $year,
                        $monthNumber,
                        1
                    )->format('F'),
                ];
            });

        return view('attendances.calendar', compact(
            'employee',
            'calendarDays',
            'monthDate',
            'months',
            'year',
            'month',
            'manualPresentDays',
            'biometricPresentDays',
            'absentDays',
            'leaveDays',
            'halfDays'
        ));
    }

    /**
     * Calendar date se attendance save/update.
     */
    public function calendarSave(
        Request $request,
        Employee $employee
    ) {
        $validated = $request->validate([
            'attendance_date' => [
                'required',
                'date',
            ],

            'status' => [
                'required',
                'in:present,absent,leave,half_day,clear',
            ],

            'check_in' => [
                'nullable',
                'date_format:H:i',
            ],

            'check_out' => [
                'nullable',
                'date_format:H:i',
            ],

            'overtime_hours' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'remarks' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $existingAttendance = Attendance::query()
            ->where('employee_id', $employee->id)
            ->where(
                'attendance_date',
                $validated['attendance_date']
            )
            ->first();

        if (
            $existingAttendance &&
            ($existingAttendance->source ?? 'manual') === 'biometric'
        ) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Biometric attendance cannot be changed manually.',
            ], 422);
        }

        if ($validated['status'] === 'clear') {
            $existingAttendance?->delete();

            return response()->json([
                'success' => true,
                'message' => 'Attendance cleared successfully.',
                'attendance' => null,
            ]);
        }

        $attendance = Attendance::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'attendance_date' =>
                    $validated['attendance_date'],
            ],
            [
                'status' => $validated['status'],
                'source' => 'manual',
                'check_in' =>
                    $validated['check_in'] ?? null,
                'check_out' =>
                    $validated['check_out'] ?? null,
                'overtime_hours' =>
                    $validated['overtime_hours'] ?? 0,
                'remarks' =>
                    $validated['remarks'] ?? null,
                'biometric_record_id' => null,
                'biometric_synced_at' => null,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Attendance saved successfully.',
            'attendance' => [
                'id' => $attendance->id,
                'status' => $attendance->status,
                'source' => $attendance->source,
                'check_in' => $attendance->check_in,
                'check_out' => $attendance->check_out,
                'overtime_hours' =>
                    $attendance->overtime_hours,
                'remarks' => $attendance->remarks,
            ],
        ]);
    }

    public function create()
    {
        $employees = Employee::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('attendances.create', compact(
            'employees'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => [
                'required',
                'exists:employees,id',
            ],

            'attendance_date' => [
                'required',
                'date',
            ],

            'status' => [
                'required',
                'in:present,absent,leave,half_day',
            ],

            'check_in' => [
                'nullable',
                'date_format:H:i',
            ],

            'check_out' => [
                'nullable',
                'date_format:H:i',
            ],

            'overtime_hours' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'remarks' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        Attendance::updateOrCreate(
            [
                'employee_id' => $validated['employee_id'],
                'attendance_date' =>
                    $validated['attendance_date'],
            ],
            [
                'status' => $validated['status'],
                'source' => 'manual',
                'check_in' =>
                    $validated['check_in'] ?? null,
                'check_out' =>
                    $validated['check_out'] ?? null,
                'overtime_hours' =>
                    $validated['overtime_hours'] ?? 0,
                'remarks' =>
                    $validated['remarks'] ?? null,
            ]
        );

        return redirect()
            ->route('attendances.index')
            ->with(
                'success',
                'Attendance saved successfully.'
            );
    }

    public function edit(Attendance $attendance)
    {
        if (
            ($attendance->source ?? 'manual') === 'biometric'
        ) {
            return redirect()
                ->route('attendances.index')
                ->withErrors([
                    'attendance' =>
                        'Biometric attendance cannot be edited manually.',
                ]);
        }

        $employees = Employee::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('attendances.edit', compact(
            'attendance',
            'employees'
        ));
    }

    public function update(
        Request $request,
        Attendance $attendance
    ) {
        if (
            ($attendance->source ?? 'manual') === 'biometric'
        ) {
            return back()->withErrors([
                'attendance' =>
                    'Biometric attendance cannot be updated manually.',
            ]);
        }

        $validated = $request->validate([
            'employee_id' => [
                'required',
                'exists:employees,id',
            ],

            'attendance_date' => [
                'required',
                'date',
            ],

            'status' => [
                'required',
                'in:present,absent,leave,half_day',
            ],

            'check_in' => [
                'nullable',
                'date_format:H:i',
            ],

            'check_out' => [
                'nullable',
                'date_format:H:i',
            ],

            'overtime_hours' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'remarks' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $attendance->update([
            'employee_id' => $validated['employee_id'],
            'attendance_date' =>
                $validated['attendance_date'],
            'status' => $validated['status'],
            'source' => 'manual',
            'check_in' =>
                $validated['check_in'] ?? null,
            'check_out' =>
                $validated['check_out'] ?? null,
            'overtime_hours' =>
                $validated['overtime_hours'] ?? 0,
            'remarks' =>
                $validated['remarks'] ?? null,
        ]);

        return redirect()
            ->route('attendances.index')
            ->with(
                'success',
                'Attendance updated successfully.'
            );
    }

    public function destroy(Attendance $attendance)
    {
        if (
            ($attendance->source ?? 'manual') === 'biometric'
        ) {
            return back()->withErrors([
                'attendance' =>
                    'Biometric attendance cannot be deleted manually.',
            ]);
        }

        $attendance->delete();

        return redirect()
            ->route('attendances.index')
            ->with(
                'success',
                'Attendance deleted successfully.'
            );
    }
}
