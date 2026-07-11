@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0">Expense Sub Categories</h3>
        <small class="text-muted">Manage sub categories under main categories</small>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('expense-sub-categories.store') }}" method="POST" class="row g-3">
            @csrf

            <div class="col-md-5">
                <label class="form-label">Main Category *</label>
                <select name="expense_category_id" class="form-select" required>
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-5">
                <label class="form-label">Sub Category Name *</label>
                <input type="text" name="name" class="form-control" placeholder="Tea / Lunch / Stitching Worker" required>
            </div>

            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>

            <div class="col-md-12">
                <button class="btn btn-primary">Add Sub Category</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Main Category</th>
                    <th>Sub Category</th>
                    <th>Status</th>
                    <th width="220">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($subCategories as $subCategory)
                    <tr>
                        <form action="{{ route('expense-sub-categories.update', $subCategory->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <td>
                                <select name="expense_category_id" class="form-select" required>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ $subCategory->expense_category_id == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            <td>
                                <input type="text" name="name" value="{{ $subCategory->name }}" class="form-control" required>
                            </td>

                            <td>
                                <select name="status" class="form-select">
                                    <option value="1" {{ $subCategory->status ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ !$subCategory->status ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </td>

                            <td>
                                <button class="btn btn-warning btn-sm">Update</button>
                        </form>

                                <form action="{{ route('expense-sub-categories.destroy', $subCategory->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm" onclick="return confirm('Delete sub category?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">No sub categories found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
