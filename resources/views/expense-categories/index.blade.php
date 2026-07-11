@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0">Expense Categories</h3>
        <small class="text-muted">Manage main expense categories</small>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('expense-categories.store') }}" method="POST" class="row g-3">
            @csrf

            <div class="col-md-8">
                <label class="form-label">Category Name *</label>
                <input type="text" name="name" class="form-control" placeholder="Worker Salary" required>
            </div>

            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary w-100">Add Category</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Category</th>
                    <th>Status</th>
                    <th width="220">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <form action="{{ route('expense-categories.update', $category->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <td>
                                <input type="text" name="name" value="{{ $category->name }}" class="form-control" required>
                            </td>

                            <td>
                                <select name="status" class="form-select">
                                    <option value="1" {{ $category->status ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ !$category->status ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </td>

                            <td>
                                <button class="btn btn-warning btn-sm">Update</button>
                        </form>

                                <form action="{{ route('expense-categories.destroy', $category->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm" onclick="return confirm('Delete category?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted py-4">No categories found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
