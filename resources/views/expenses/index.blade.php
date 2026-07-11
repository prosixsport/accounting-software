@extends('layouts.app')

@section('content')

<style>
.exp-top{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px}
.exp-title h3{font-size:28px;font-weight:800;margin:0}
.exp-title small{color:#8b98a8}

.cat-grid{display:flex;gap:16px;overflow-x:auto;padding:6px 2px 20px;margin-bottom:18px}
.cat-card{min-width:155px;width:155px;height:138px;background:#fff;border:2px solid #e5e7eb;border-radius:18px;text-decoration:none;color:#111;display:flex;flex-direction:column;align-items:center;justify-content:center;transition:.2s;box-shadow:0 3px 12px rgba(0,0,0,.04);padding:14px;overflow:hidden}
.cat-card:hover{transform:translateY(-4px);border-color:#111;color:#111}
.cat-card.active{background:#050505;color:#fff;border-color:#050505}

.cat-icon{width:48px;height:48px;border-radius:14px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;font-size:22px;margin-bottom:12px;flex-shrink:0}
.cat-card.active .cat-icon{background:#1f2937;color:#fff}

.cat-name{font-weight:800;font-size:13px;text-align:center;width:100%;line-height:1.2;min-height:32px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;word-break:break-word}
.cat-count{font-size:12px;color:#9ca3af;margin-top:6px}
.cat-card.active .cat-count{color:#d1d5db}

.sub-box{background:#fff;border:1px solid #e5e7eb;border-radius:18px;padding:20px;box-shadow:0 3px 12px rgba(0,0,0,.04);margin-bottom:22px}
.sub-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px}
.sub-grid{display:flex;gap:12px;flex-wrap:wrap}

.sub-pill{text-decoration:none;padding:12px 18px;border-radius:999px;border:1px solid #d1d5db;color:#111;background:#fff;font-weight:700;transition:.2s;display:flex;align-items:center;gap:8px}
.sub-pill:hover,.sub-pill.active{background:#111;color:#fff;border-color:#111}

.empty-state{background:#fff;border:1px dashed #cbd5e1;border-radius:18px;padding:55px;text-align:center;color:#64748b;margin-bottom:22px}
.empty-state i{font-size:42px;color:#94a3b8;margin-bottom:12px;display:block}

.total-card{background:#111;color:#fff;border-radius:18px;padding:22px;margin-bottom:18px}
.exp-table-card{background:#fff;border-radius:18px;border:1px solid #e5e7eb;box-shadow:0 3px 12px rgba(0,0,0,.04);overflow:hidden}
.exp-table-head{padding:18px 22px;border-bottom:1px solid #f1f5f9;font-weight:800}
</style>

@php
    function expenseIcon($text) {
        $name = strtolower($text);

        if(str_contains($name, 'gas')) return 'bi-fire';
        if(str_contains($name, 'water')) return 'bi-droplet-fill';
        if(str_contains($name, 'hotel') || str_contains($name, 'food') || str_contains($name, 'tea') || str_contains($name, 'lunch') || str_contains($name, 'dinner')) return 'bi-cup-hot-fill';
        if(str_contains($name, 'worker') || str_contains($name, 'labour') || str_contains($name, 'labor')) return 'bi-people-fill';
        if(str_contains($name, 'salary') || str_contains($name, 'payroll')) return 'bi-cash-stack';
        if(str_contains($name, 'contractor') || str_contains($name, 'contracter') || str_contains($name, 'thekedar')) return 'bi-person-workspace';
        if(str_contains($name, 'electric') || str_contains($name, 'bijli')) return 'bi-lightning-charge-fill';
        if(str_contains($name, 'fuel') || str_contains($name, 'diesel') || str_contains($name, 'petrol')) return 'bi-fuel-pump-fill';
        if(str_contains($name, 'transport') || str_contains($name, 'vehicle') || str_contains($name, 'car') || str_contains($name, 'truck')) return 'bi-truck';
        if(str_contains($name, 'rent')) return 'bi-house-door-fill';
        if(str_contains($name, 'internet') || str_contains($name, 'wifi')) return 'bi-wifi';
        if(str_contains($name, 'machine') || str_contains($name, 'maintenance') || str_contains($name, 'repair')) return 'bi-gear-fill';
        if(str_contains($name, 'office')) return 'bi-building-fill';
        if(str_contains($name, 'print') || str_contains($name, 'printing')) return 'bi-printer-fill';
        if(str_contains($name, 'stitch') || str_contains($name, 'sewing')) return 'bi-scissors';
        if(str_contains($name, 'packing') || str_contains($name, 'box')) return 'bi-box-seam-fill';
        if(str_contains($name, 'cutting')) return 'bi-rulers';
        if(str_contains($name, 'embroidery')) return 'bi-flower1';
        if(str_contains($name, 'security')) return 'bi-shield-lock-fill';
        if(str_contains($name, 'clean')) return 'bi-stars';
        if(str_contains($name, 'medical') || str_contains($name, 'medicine')) return 'bi-bandaid-fill';
        if(str_contains($name, 'tax')) return 'bi-file-earmark-text-fill';
        if(str_contains($name, 'bank')) return 'bi-bank';
        if(str_contains($name, 't shirt') || str_contains($name, 'shirt') || str_contains($name, 'apparel')) return 'bi-bag-fill';

        return 'bi-grid-3x3-gap-fill';
    }
@endphp

<div class="exp-top">
    <div class="exp-title">
        <h3>Factory Expenses</h3>
        <small>Select category, then sub category to view expenses</small>
    </div>

    <a href="{{ route('expenses.create') }}" class="btn btn-dark">
        + Add Expense
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="cat-grid">
    @forelse($categories as $category)
        @php
            $catExpenseCount = \App\Models\Expense::where('expense_category_id', $category->id)->count();
            $icon = expenseIcon($category->name);
        @endphp

        <a href="{{ route('expenses.index', ['expense_category_id' => $category->id]) }}"
           class="cat-card {{ $selectedCategory == $category->id ? 'active' : '' }}">

            <div class="cat-icon">
                <i class="bi {{ $icon }}"></i>
            </div>

            <div class="cat-name" title="{{ $category->name }}">
                {{ $category->name }}
            </div>

            <div class="cat-count">{{ $catExpenseCount }} expenses</div>
        </a>
    @empty
        <div class="empty-state w-100">
            <i class="bi bi-folder-x"></i>
            <strong>No categories found</strong>
            <div class="mt-2">
                <a href="{{ route('expense-categories.index') }}">Add Category</a>
            </div>
        </div>
    @endforelse
</div>

@if($selectedCategory)

    <div class="sub-box">
        <div class="sub-head">
            <div>
                <strong>Sub Categories</strong>
                <div class="text-muted small">Choose sub category to show expenses</div>
            </div>

            <a href="{{ route('expense-sub-categories.index') }}" class="btn btn-sm btn-outline-dark">
                Manage
            </a>
        </div>

        @if($subCategories->count() > 0)
            <div class="sub-grid">
                @foreach($subCategories as $subCategory)
                    @php
                        $subIcon = expenseIcon($subCategory->name);
                    @endphp

                    <a href="{{ route('expenses.index', [
                        'expense_category_id' => $selectedCategory,
                        'expense_sub_category_id' => $subCategory->id
                    ]) }}"
                       class="sub-pill {{ $selectedSubCategory == $subCategory->id ? 'active' : '' }}">
                        <i class="bi {{ $subIcon }}"></i>
                        {{ $subCategory->name }}
                    </a>
                @endforeach
            </div>
        @else
            <div class="empty-state mb-0">
                <i class="bi bi-folder2-open"></i>
                <strong>No sub categories found</strong>
                <div class="small mt-1">Create sub categories for this category first.</div>
            </div>
        @endif
    </div>

@else

    <div class="empty-state">
        <i class="bi bi-hand-index-thumb"></i>
        <strong>Select a category</strong>
        <div class="small mt-1">Sub categories will appear after selecting a category.</div>
    </div>

@endif

@if($selectedSubCategory)

    <div class="total-card">
        <small>Selected Sub Category Total</small>
        <h3 class="fw-bold mt-2 mb-0">
            Rs {{ number_format($totalExpense, 2) }}
        </h3>
    </div>

    <div class="exp-table-card">
        <div class="exp-table-head">
            Expenses
        </div>

        <div class="table-responsive p-3">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Expense No</th>
                        <th>Category</th>
                        <th>Sub Category</th>
                        <th>Account</th>
                        <th>Payment</th>
                        <th>Vendor / Person</th>
                        <th>Amount</th>
                        <th>Receipt</th>
                        <th width="150">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($expenses as $expense)
                        <tr>
                            <td>{{ date('d M Y', strtotime($expense->expense_date)) }}</td>
                            <td>{{ $expense->expense_no }}</td>

                            <td>
                                <span class="badge bg-dark">
                                    {{ $expense->category?->name ?? $expense->category ?? '-' }}
                                </span>
                            </td>

                            <td>{{ $expense->subCategory->name ?? '-' }}</td>
                            <td>{{ $expense->account->name ?? '-' }}</td>
                            <td>{{ ucfirst($expense->payment_method) }}</td>
                            <td>{{ $expense->vendor_name ?? '-' }}</td>

                            <td>
                                <strong class="text-danger">
                                    Rs {{ number_format($expense->amount, 2) }}
                                </strong>
                            </td>

                            <td>
                                @if($expense->receipt)
                                    @php
                                        $ext = strtolower(pathinfo($expense->receipt, PATHINFO_EXTENSION));
                                    @endphp

                                    @if(in_array($ext, ['jpg','jpeg','png','webp','gif']))
                                        <a href="{{ asset('storage/'.$expense->receipt) }}" target="_blank">
                                            <img src="{{ asset('storage/'.$expense->receipt) }}"
                                                 width="45"
                                                 height="45"
                                                 class="rounded border"
                                                 style="object-fit:cover;">
                                        </a>
                                    @else
                                        <a href="{{ asset('storage/'.$expense->receipt) }}"
                                           target="_blank"
                                           class="btn btn-sm btn-outline-primary">
                                            View PDF
                                        </a>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>

                            <td>
                                <a href="{{ route('expenses.edit', $expense->id) }}"
                                   class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                                <form action="{{ route('expenses.destroy', $expense->id) }}"
                                      method="POST"
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-danger btn-sm"
                                            onclick="return confirm('Delete expense?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                No expenses found in this sub category.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endif

@endsection
