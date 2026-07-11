@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0">Employees</h3>
        <small class="text-muted">Manage company employees</small>
    </div>

    <a href="{{ route('employees.create') }}" class="btn btn-primary">
        + Add Employee
    </a>
</div>

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body">

        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th width="90">Profile</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Designation</th>
                    <th>Salary</th>
                    <th>Status</th>
                    <th width="180">Action</th>
                </tr>
            </thead>

            <tbody>

                @forelse($employees as $employee)

                <tr>
                    <td>
                        @php
                            $profile = $employee->pictures[0] ?? null;
                        @endphp

                        @if($profile)
                            <img src="{{ asset('storage/' . $profile) }}"
                                 alt="Profile"
                                 width="52"
                                 height="52"
                                 class="rounded-circle border shadow-sm"
                                 style="object-fit: cover;">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($employee->name) }}&background=0D6EFD&color=fff&size=80"
                                 alt="Profile"
                                 width="52"
                                 height="52"
                                 class="rounded-circle border shadow-sm">
                        @endif
                    </td>

                    <td>{{ $employee->name }}</td>
                    <td>{{ $employee->department ?? '-' }}</td>
                    <td>{{ $employee->designation ?? '-' }}</td>

                    <td>
                        Rs {{ number_format($employee->basic_salary, 2) }}
                    </td>

                    <td>
                        @if($employee->status == 'active')
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Inactive</span>
                        @endif
                    </td>

                    <td>
                        <a href="{{ route('employees.edit', $employee->id) }}"
                           class="btn btn-warning btn-sm">
                            Edit
                        </a>

                        <form action="{{ route('employees.destroy', $employee->id) }}"
                              method="POST"
                              class="d-inline">

                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Delete Employee?')">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>

                @empty

                <tr>
                    <td colspan="7" class="text-center">
                        No Employees Found
                    </td>
                </tr>

                @endforelse

            </tbody>
        </table>

    </div>
</div>

@endsection
