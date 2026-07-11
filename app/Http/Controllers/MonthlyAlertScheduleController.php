<?php

namespace App\Http\Controllers;

use App\Models\MonthlyAlertSchedule;
use Illuminate\Http\Request;

class MonthlyAlertScheduleController extends Controller
{
    public function index()
    {
        $schedules = MonthlyAlertSchedule::latest()->paginate(15);

        return view('monthly-alert-schedules.index', compact('schedules'));
    }

    public function create()
    {
        return view('monthly-alert-schedules.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'alert_date' => 'required|date',
            'alert_time' => 'required',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2100',
            'notes' => 'nullable|string',
        ]);

        $data['status'] = 'pending';

        MonthlyAlertSchedule::create($data);

        return redirect()
            ->route('monthly-alert-schedules.index')
            ->with('success', 'Monthly alert schedule created successfully.');
    }

    public function edit(MonthlyAlertSchedule $monthlyAlertSchedule)
    {
        return view('monthly-alert-schedules.edit', compact('monthlyAlertSchedule'));
    }

    public function update(Request $request, MonthlyAlertSchedule $monthlyAlertSchedule)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'alert_date' => 'required|date',
            'alert_time' => 'required',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2100',
            'notes' => 'nullable|string',
        ]);

        $data['status'] = 'pending';
        $data['sent_at'] = null;

        $monthlyAlertSchedule->update($data);

        return redirect()
            ->route('monthly-alert-schedules.index')
            ->with('success', 'Monthly alert schedule updated successfully.');
    }

    public function destroy(MonthlyAlertSchedule $monthlyAlertSchedule)
    {
        $monthlyAlertSchedule->delete();

        return redirect()
            ->route('monthly-alert-schedules.index')
            ->with('success', 'Schedule deleted successfully.');
    }
}
