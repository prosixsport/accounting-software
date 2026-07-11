@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold">Edit Alert Schedule</h2>
        <p class="text-muted mb-0">Update automatic monthly alert schedule.</p>
    </div>

    <a href="{{ route('monthly-alert-schedules.index') }}" class="btn btn-light">
        Back
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('monthly-alert-schedules.update', $monthlyAlertSchedule->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Title *</label>
                    <input type="text"
                           name="title"
                           class="form-control"
                           value="{{ old('title', $monthlyAlertSchedule->title) }}"
                           required>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Alert Date *</label>
                    <input type="date"
                           name="alert_date"
                           class="form-control"
                           value="{{ old('alert_date', $monthlyAlertSchedule->alert_date->format('Y-m-d')) }}"
                           required>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Alert Time *</label>
                    <input type="time"
                           name="alert_time"
                           class="form-control"
                           value="{{ old('alert_time', \Carbon\Carbon::parse($monthlyAlertSchedule->alert_time)->format('H:i')) }}"
                           required>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">For Month *</label>
                    <select name="month" class="form-select" required>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ old('month', $monthlyAlertSchedule->month) == $m ? 'selected' : '' }}>
                                {{ date('F', mktime(0,0,0,$m,1)) }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">For Year *</label>
                    <input type="number"
                           name="year"
                           class="form-control"
                           value="{{ old('year', $monthlyAlertSchedule->year) }}"
                           required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Notes</label>
                    <input type="text"
                           name="notes"
                           class="form-control"
                           value="{{ old('notes', $monthlyAlertSchedule->notes) }}">
                </div>
            </div>

            <button class="btn btn-dark">
                <i class="bi bi-check-circle"></i> Update Schedule
            </button>
        </form>
    </div>
</div>

@endsection
