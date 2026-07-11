@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0">Attendance</h3>
        <small class="text-muted">Manage workers daily attendance</small>
    </div>

    <a href="{{ route('attendances.create') }}" class="btn btn-primary">
        + Add Attendance
    </a>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body">

        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Employee</th>
                    <th>Status</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Overtime</th>
                    <th width="180">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($attendances as $attendance)
                    <tr>
                        <td>{{ date('d M Y', strtotime($attendance->attendance_date)) }}</td>

                        <td>
                            <strong>{{ $attendance->employee->name ?? '-' }}</strong><br>
                            <small class="text-muted">{{ $attendance->employee->employee_code ?? '' }}</small>
                        </td>

                        <td>
                            @if($attendance->status == 'present')
                                <span class="badge bg-success">Present</span>
                            @elseif($attendance->status == 'absent')
                                <span class="badge bg-danger">Absent</span>
                            @elseif($attendance->status == 'leave')
                                <span class="badge bg-warning text-dark">Leave</span>
                            @else
                                <span class="badge bg-info text-dark">Half Day</span>
                            @endif
                        </td>

                        <td>{{ $attendance->check_in ?? '-' }}</td>
                        <td>{{ $attendance->check_out ?? '-' }}</td>
                        <td>{{ $attendance->overtime_hours }} hrs</td>

                        <td>
                            <a href="{{ route('attendances.edit', $attendance->id) }}"
                               class="btn btn-warning btn-sm">
                                Edit
                            </a>

                            <form action="{{ route('attendances.destroy', $attendance->id) }}"
                                  method="POST"
                                  class="d-inline">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-danger btn-sm"
                                        onclick="return confirm('Delete attendance?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            No attendance found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>
</div>

@endsection
