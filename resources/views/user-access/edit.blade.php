@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold">Manage Access</h2>
        <p class="text-muted mb-0">{{ $user->name }} - {{ $user->email }}</p>
    </div>

    <a href="{{ route('user-access.index') }}" class="btn btn-light">
        Back
    </a>
</div>

<form action="{{ route('user-access.update', $user->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">

            <h5 class="fw-bold mb-3">User Details</h5>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Name *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Role *</label>
                    <select name="role" class="form-select" required>
                        <option value="accountant" {{ $user->role == 'accountant' ? 'selected' : '' }}>Accountant</option>
                        <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="staff" {{ $user->role == 'staff' ? 'selected' : '' }}>Staff</option>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Leave empty if no change">
                </div>
            </div>

        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <h5 class="fw-bold mb-3">Sidebar Access</h5>

            <div class="row">
                @foreach($permissions as $key => $label)
                    <div class="col-md-4 mb-3">
                        <div class="border rounded p-3 h-100">
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="permissions[]"
                                       value="{{ $key }}"
                                       id="{{ $key }}"
                                       {{ in_array($key, $selected) ? 'checked' : '' }}>

                                <label class="form-check-label fw-bold" for="{{ $key }}">
                                    {{ $label }}
                                </label>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <hr>

            <button class="btn btn-dark">
                <i class="bi bi-check-circle"></i> Save Changes
            </button>

        </div>
    </div>
</form>

@endsection
