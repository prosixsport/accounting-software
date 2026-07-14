@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Biometric Registration</h2>
        <p class="text-muted mb-0">Register worker fingerprints for salary verification.</p>
    </div>

    <a href="{{ route('biometric.create') }}" class="btn btn-dark">
        <i class="bi bi-fingerprint"></i> Register Finger
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Employee</th>
                        <th>Finger</th>
                        <th>Device</th>
                        <th>Status</th>
                        <th>Registered At</th>
                        <th width="180">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($templates as $template)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-bold">
                                {{ $template->employee->employee_name ?? $template->employee->name ?? 'N/A' }}
                            </td>
                            <td>{{ $template->finger_name }}</td>
                            <td>{{ $template->device_name ?? 'DigitalPersona' }}</td>
                            <td>
                                @if($template->template_data)
                                    <span class="badge bg-success">Fingerprint Saved</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pending Capture</span>
                                @endif
                            </td>
                            <td>{{ $template->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('biometric.edit', $template->id) }}" class="btn btn-sm btn-primary">
                                    Edit
                                </a>

                                <form action="{{ route('biometric.destroy', $template->id) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Delete this biometric record?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                No biometric record found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $templates->links() }}
    </div>
</div>

@endsection
