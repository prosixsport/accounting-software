@extends('layouts.app')

@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">

    <div>
        <h2 class="fw-bold mb-1">
            Items / Rates
        </h2>

        <p class="text-muted mb-0">
            Manage contract items, machine and fixed rates.
        </p>
    </div>

    <a href="{{ route('contractor-items.create') }}"
       class="btn btn-dark">

        <i class="bi bi-plus-lg me-1"></i>
        Add Item
    </a>

</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">

        {{ session('success') }}

        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert">
        </button>

    </div>
@endif

<div class="card border-0 shadow-sm mb-3">

    <div class="card-body">

        <div class="row align-items-end">

            <div class="col-md-8">

                <label class="form-label">
                    Search Item
                </label>

                <input type="text"
                       id="itemSearch"
                       class="form-control"
                       placeholder="Search item, machine, department, unit or status...">

            </div>

            <div class="col-md-4 mt-3 mt-md-0">

                <div class="text-md-end text-muted">

                    Showing

                    <strong id="visibleItemCount"
                            class="text-dark">

                        {{ $items->count() }}

                    </strong>

                    item(s)

                </div>

            </div>

        </div>

    </div>

</div>

<div class="card border-0 shadow-sm">

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-hover align-middle item-table">

                <thead>
                    <tr>
                        <th width="85">Image</th>
                        <th>Item Details</th>
                        <th>Machine</th>
                        <th>Department</th>
                        <th>Unit</th>
                        <th>Rate / Price</th>
                        <th>Status</th>
                        <th width="180">Action</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($items as $item)

                        @php
                            $searchText = strtolower(
                                implode(' ', [
                                    $item->name ?? '',
                                    $item->description ?? '',
                                    $item->unit ?? '',
                                    $item->rate ?? '',
                                    $item->status ?? '',
                                    $item->machine?->name ?? '',
                                    $item->machine?->department?->name ?? '',
                                ])
                            );
                        @endphp

                        <tr class="item-row"
                            data-search="{{ $searchText }}">

                            <td>

                                @if($item->thumbnail)

                                    <img src="{{ asset(
                                            'storage/' .
                                            $item->thumbnail
                                        ) }}"
                                         class="item-thumbnail"
                                         alt="{{ $item->name }}">

                                @else

                                    <div class="item-placeholder">
                                        <i class="bi bi-image"></i>
                                    </div>

                                @endif

                            </td>

                            <td>

                                <strong class="item-name">
                                    {{ $item->name }}
                                </strong>

                                @if($item->description)

                                    <br>

                                    <small class="text-muted">
                                        {{ \Illuminate\Support\Str::limit(
                                            $item->description,
                                            55
                                        ) }}
                                    </small>

                                @endif

                            </td>

                            <td>

                                <strong>
                                    {{ $item->machine?->name ?? '-' }}
                                </strong>

                            </td>

                            <td>

                                {{ $item->machine?->department?->name ?? '-' }}

                            </td>

                            <td>
                                {{ $item->unit }}
                            </td>

                            <td class="fw-bold text-success">

                                Rs {{ number_format(
                                    $item->rate,
                                    2
                                ) }}

                            </td>

                            <td>

                                @if($item->status === 'active')

                                    <span class="badge bg-success-subtle text-success">
                                        Active
                                    </span>

                                @else

                                    <span class="badge bg-secondary-subtle text-secondary">
                                        Inactive
                                    </span>

                                @endif

                            </td>

                            <td>

                                <a href="{{ route(
                                        'contractor-items.edit',
                                        $item->id
                                    ) }}"
                                   class="btn btn-sm btn-primary">

                                    <i class="bi bi-pencil-square me-1"></i>
                                    Edit
                                </a>

                                <form action="{{ route(
                                            'contractor-items.destroy',
                                            $item->id
                                        ) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Delete this item?')">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-sm btn-danger">

                                        <i class="bi bi-trash me-1"></i>
                                        Delete
                                    </button>

                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="8"
                                class="text-center text-muted py-5">

                                <i class="bi bi-box-seam fs-2 d-block mb-2"></i>

                                No item found

                            </td>
                        </tr>

                    @endforelse

                    <tr id="noItemResult"
                        style="display:none;">

                        <td colspan="8"
                            class="text-center text-muted py-5">

                            No matching item found

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

        @if($items->hasPages())
            <div class="d-flex justify-content-end mt-3">
                {{ $items->links() }}
            </div>
        @endif

    </div>
</div>

<style>
.item-table {
    min-width: 1100px;
}

.item-table th,
.item-table td {
    padding: 14px 12px;
}

.item-thumbnail {
    width: 58px;
    height: 58px;
    border: 1px solid #e2e7ee;
    border-radius: 10px;
    object-fit: cover;
}

.item-placeholder {
    width: 58px;
    height: 58px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    background: #f1f3f5;
    color: #8b95a4;
    font-size: 20px;
}

.item-name {
    color: #111827;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const searchInput =
        document.getElementById('itemSearch');

    const rows =
        document.querySelectorAll('.item-row');

    const countElement =
        document.getElementById(
            'visibleItemCount'
        );

    const noResult =
        document.getElementById(
            'noItemResult'
        );

    searchInput?.addEventListener('input', function () {

        const value =
            this.value.toLowerCase().trim();

        let visible = 0;

        rows.forEach(function (row) {

            const matched = (
                row.dataset.search || ''
            )
                .toLowerCase()
                .includes(value);

            row.style.display =
                matched ? '' : 'none';

            if (matched) {
                visible++;
            }

        });

        if (countElement) {
            countElement.textContent = visible;
        }

        if (noResult) {
            noResult.style.display =
                visible === 0 && rows.length > 0
                    ? ''
                    : 'none';
        }

    });

});
</script>

@endsection
