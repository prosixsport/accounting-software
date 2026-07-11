@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Salary Verification</h2>
        <p class="text-muted mb-0">Verify worker salary using biometric record.</p>
    </div>

    <a href="{{ route('salary-verifications.create') }}" class="btn btn-dark">
        <i class="bi bi-fingerprint"></i> Verify Salary
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
                        <th>Payroll</th>
                        <th>Status</th>
                        <th>Device</th>
                        <th>Verified By</th>
                        <th>Verified At</th>
                        <th width="180">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($verifications as $verification)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td class="fw-bold">
                                {{ $verification->employee->employee_name ?? $verification->employee->name ?? 'N/A' }}
                            </td>

                            <td>
                                @if($verification->payroll)
                                    Payroll #{{ $verification->payroll->id }}
                                @else
                                    N/A
                                @endif
                            </td>

                            <td>
                                @if($verification->verification_status == 'verified')
                                    <span class="badge bg-success">Verified</span>
                                @elseif($verification->verification_status == 'failed')
                                    <span class="badge bg-danger">Failed</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                            </td>

                            <td>{{ $verification->device_name ?? 'DigitalPersona' }}</td>

                            <td>{{ $verification->verifier->name ?? 'N/A' }}</td>

                            <td>
                                {{ $verification->verified_at ? $verification->verified_at->format('d M Y h:i A') : 'N/A' }}
                            </td>

                            <td>
                                <a href="{{ route('salary-verifications.edit', $verification->id) }}" class="btn btn-sm btn-primary">
                                    Edit
                                </a>

                                <form action="{{ route('salary-verifications.destroy', $verification->id) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Delete this verification?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                No salary verification found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $verifications->links() }}
    </div>
</div>

@endsection
