@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold">Monthly Alert Schedules</h2>
        <p class="text-muted mb-0">Set date and time to auto-generate monthly alerts.</p>
    </div>

    <a href="{{ route('monthly-alert-schedules.create') }}" class="btn btn-dark">
        <i class="bi bi-plus-circle"></i> Add Schedule
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Alert Date</th>
                    <th>Alert Time</th>
                    <th>For Month</th>
                    <th>Status</th>
                    <th width="180">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($schedules as $schedule)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="fw-bold">{{ $schedule->title }}</td>
                        <td>{{ $schedule->alert_date->format('d M Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($schedule->alert_time)->format('h:i A') }}</td>
                        <td>
                            {{ date('F', mktime(0,0,0,$schedule->month,1)) }}
                            {{ $schedule->year }}
                        </td>
                        <td>
                            @if($schedule->status == 'sent')
                                <span class="badge bg-success">Sent</span>
                            @else
                                <span class="badge bg-warning text-dark">Pending</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('monthly-alert-schedules.edit', $schedule->id) }}" class="btn btn-sm btn-primary">
                                Edit
                            </a>

                            <form action="{{ route('monthly-alert-schedules.destroy', $schedule->id) }}"
                                  method="POST"
                                  class="d-inline"
                                  onsubmit="return confirm('Delete this schedule?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            No schedule found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $schedules->links() }}
    </div>
</div>

@endsection
