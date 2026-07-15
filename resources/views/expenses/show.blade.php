@extends('layouts.app')

@section('content')

@php
    $receiptExtension = $expense->receipt
        ? strtolower(
            pathinfo(
                $expense->receipt,
                PATHINFO_EXTENSION
            )
        )
        : null;

    $isReceiptImage = in_array(
        $receiptExtension,
        [
            'jpg',
            'jpeg',
            'png',
            'webp',
            'gif',
        ]
    );
@endphp

<div class="expense-slip-page">

    {{-- Page Header --}}
    <div class="expense-slip-page-header no-print">

        <div>
            <h3>
                Expense Slip
            </h3>

            <p>
                View complete expense information and receipt
            </p>
        </div>

        <div class="expense-slip-actions">

            <button type="button"
                    onclick="window.print()"
                    class="print-slip-button">

                <i class="bi bi-printer"></i>
                Print Slip

            </button>

            <a href="{{ route('expenses.edit', $expense->id) }}"
               class="edit-slip-button">

                <i class="bi bi-pencil-square"></i>
                Edit

            </a>

            <a href="{{ route('expenses.index', [
                'expense_category_id' =>
                    $expense->expense_category_id,

                'expense_sub_category_id' =>
                    $expense->expense_sub_category_id,

                'period' => 'month',

                'month' =>
                    \Carbon\Carbon::parse(
                        $expense->expense_date
                    )->format('Y-m'),
            ]) }}"
               class="back-slip-button">

                <i class="bi bi-arrow-left"></i>
                Back

            </a>

        </div>

    </div>

    {{-- Printable Slip --}}
    <div class="expense-slip">

        <div class="slip-header">

            <div class="company-section">

                <div class="company-icon">
                    <i class="bi bi-receipt-cutoff"></i>
                </div>

                <div>
                    <h2>
                        Accounts System
                    </h2>

                    <span>
                        Factory Expense Slip
                    </span>
                </div>

            </div>

            <div class="slip-number-box">

                <small>
                    Expense Number
                </small>

                <strong>
                    {{ $expense->expense_no }}
                </strong>

            </div>

        </div>

        <div class="slip-divider"></div>

        {{-- Main Information --}}
        <div class="expense-main-grid">

            <div class="expense-info-card">

                <span class="info-label">
                    <i class="bi bi-calendar3"></i>
                    Expense Date
                </span>

                <strong>
                    {{ \Carbon\Carbon::parse(
                        $expense->expense_date
                    )->format('d F Y') }}
                </strong>

                <small>
                    {{ \Carbon\Carbon::parse(
                        $expense->expense_date
                    )->format('l') }}
                </small>

            </div>

            <div class="expense-info-card">

                <span class="info-label">
                    <i class="bi bi-folder"></i>
                    Main Category
                </span>

                <strong>
                    {{ $expense->category?->name
                        ?? $expense->category
                        ?? '-' }}
                </strong>

                <small>
                    Expense category
                </small>

            </div>

            <div class="expense-info-card">

                <span class="info-label">
                    <i class="bi bi-folder2-open"></i>
                    Sub Category
                </span>

                <strong>
                    {{ $expense->subCategory?->name
                        ?? 'No Sub Category' }}
                </strong>

                <small>
                    Expense classification
                </small>

            </div>

            <div class="expense-info-card amount-info-card">

                <span class="info-label">
                    <i class="bi bi-cash-stack"></i>
                    Expense Amount
                </span>

                <strong>
                    Rs {{ number_format(
                        $expense->amount,
                        2
                    ) }}
                </strong>

                <small>
                    Total paid amount
                </small>

            </div>

        </div>

        {{-- Detail Table --}}
        <div class="expense-detail-section">

            <div class="section-heading">

                <div>
                    <h5>
                        Expense Details
                    </h5>

                    <p>
                        Complete payment and vendor information
                    </p>
                </div>

                <i class="bi bi-list-check"></i>

            </div>

            <table class="expense-detail-table">

                <tr>
                    <th>
                        <i class="bi bi-credit-card"></i>
                        Payment Method
                    </th>

                    <td>
                        {{ ucfirst(
                            $expense->payment_method
                        ) }}
                    </td>
                </tr>

                <tr>
                    <th>
                        <i class="bi bi-bank"></i>
                        Expense Account
                    </th>

                    <td>
                        @if($expense->account)

                            {{ $expense->account->code }}
                            -
                            {{ $expense->account->name }}

                        @else

                            Not selected

                        @endif
                    </td>
                </tr>

                <tr>
                    <th>
                        <i class="bi bi-person-check"></i>
                        Paid By
                    </th>

                    <td>
                        {{ $expense->paid_by ?: '-' }}
                    </td>
                </tr>

                <tr>
                    <th>
                        <i class="bi bi-person-workspace"></i>
                        Vendor / Person
                    </th>

                    <td>
                        {{ $expense->vendor_name ?: '-' }}
                    </td>
                </tr>

                <tr>
                    <th>
                        <i class="bi bi-card-text"></i>
                        Description
                    </th>

                    <td>
                        {{ $expense->description
                            ?: 'No description added.' }}
                    </td>
                </tr>

            </table>

        </div>

        {{-- Receipt --}}
        <div class="expense-detail-section">

            <div class="section-heading">

                <div>
                    <h5>
                        Receipt / Bill
                    </h5>

                    <p>
                        Attached expense proof
                    </p>
                </div>

                <i class="bi bi-paperclip"></i>

            </div>

            @if($expense->receipt)

                @if($isReceiptImage)

                    <a href="{{ asset(
                        'storage/' . $expense->receipt
                    ) }}"
                       target="_blank"
                       class="receipt-image-wrapper">

                        <img src="{{ asset(
                            'storage/' . $expense->receipt
                        ) }}"
                             alt="Expense Receipt">

                    </a>

                    <a href="{{ asset(
                        'storage/' . $expense->receipt
                    ) }}"
                       target="_blank"
                       class="open-receipt-button no-print">

                        <i class="bi bi-arrows-fullscreen"></i>
                        Open Full Receipt

                    </a>

                @else

                    <div class="pdf-receipt-box">

                        <div class="pdf-icon">
                            <i class="bi bi-file-earmark-pdf-fill"></i>
                        </div>

                        <div>
                            <strong>
                                PDF Expense Bill
                            </strong>

                            <span>
                                {{ basename(
                                    $expense->receipt
                                ) }}
                            </span>
                        </div>

                        <a href="{{ asset(
                            'storage/' . $expense->receipt
                        ) }}"
                           target="_blank"
                           class="open-pdf-button no-print">

                            <i class="bi bi-eye"></i>
                            View PDF

                        </a>

                    </div>

                @endif

            @else

                <div class="no-receipt-box">

                    <i class="bi bi-file-earmark-x"></i>

                    <strong>
                        No Receipt Attached
                    </strong>

                    <span>
                        No bill or receipt was uploaded with this expense.
                    </span>

                </div>

            @endif

        </div>

        {{-- Footer --}}
        <div class="slip-footer">

            <div class="signature-box">

                <div class="signature-line"></div>

                <strong>
                    Prepared By
                </strong>

            </div>

            <div class="signature-box">

                <div class="signature-line"></div>

                <strong>
                    Authorized Signature
                </strong>

            </div>

        </div>

        <div class="printed-date">

            Generated on:

            {{ now()->format('d F Y, h:i A') }}

        </div>

    </div>

</div>

<style>
:root {
    --slip-black: #111111;
    --slip-white: #ffffff;
    --slip-border: #dfe3e8;
    --slip-light: #f7f8fa;
    --slip-muted: #6f7782;
}

.expense-slip-page-header {
    margin-bottom: 22px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
}

.expense-slip-page-header h3 {
    margin: 0;
    font-size: 30px;
    font-weight: 900;
}

.expense-slip-page-header p {
    margin: 5px 0 0;
    color: var(--slip-muted);
    font-size: 14px;
}

.expense-slip-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

.print-slip-button,
.edit-slip-button,
.back-slip-button {
    min-height: 43px;
    padding: 9px 15px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    border: 1px solid var(--slip-black);
    border-radius: 8px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 900;
}

.print-slip-button {
    color: var(--slip-white);
    background: var(--slip-black);
}

.edit-slip-button,
.back-slip-button {
    color: var(--slip-black);
    background: var(--slip-white);
}

.expense-slip {
    max-width: 900px;
    margin: 0 auto;
    padding: 32px;
    border: 2px solid var(--slip-black);
    border-radius: 15px;
    background: var(--slip-white);
    box-shadow: 0 9px 30px rgba(0, 0, 0, 0.08);
}

.slip-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
}

.company-section {
    display: flex;
    align-items: center;
    gap: 14px;
}

.company-icon {
    width: 58px;
    height: 58px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 13px;
    color: white;
    background: var(--slip-black);
    font-size: 25px;
}

.company-section h2 {
    margin: 0;
    font-size: 27px;
    font-weight: 900;
}

.company-section span {
    display: block;
    margin-top: 3px;
    color: var(--slip-muted);
    font-size: 14px;
}

.slip-number-box {
    min-width: 190px;
    padding: 13px 17px;
    border: 1px solid var(--slip-border);
    border-radius: 10px;
    background: var(--slip-light);
    text-align: center;
}

.slip-number-box small,
.slip-number-box strong {
    display: block;
}

.slip-number-box small {
    color: var(--slip-muted);
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
}

.slip-number-box strong {
    margin-top: 4px;
    font-size: 18px;
}

.slip-divider {
    margin: 24px 0;
    border-top: 2px solid var(--slip-black);
}

.expense-main-grid {
    margin-bottom: 22px;
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
}

.expense-info-card {
    min-height: 120px;
    padding: 16px;
    border: 1px solid var(--slip-border);
    border-radius: 11px;
    background: var(--slip-light);
}

.info-label {
    display: flex;
    align-items: center;
    gap: 6px;
    color: var(--slip-muted);
    font-size: 11px;
    font-weight: 900;
    text-transform: uppercase;
}

.expense-info-card strong {
    display: block;
    margin-top: 12px;
    font-size: 16px;
    line-height: 1.35;
}

.expense-info-card small {
    display: block;
    margin-top: 5px;
    color: var(--slip-muted);
    font-size: 11px;
}

.amount-info-card {
    color: white;
    background: var(--slip-black);
}

.amount-info-card .info-label,
.amount-info-card small {
    color: #cccccc;
}

.amount-info-card strong {
    font-size: 20px;
}

.expense-detail-section {
    margin-top: 20px;
    overflow: hidden;
    border: 1px solid var(--slip-border);
    border-radius: 11px;
}

.section-heading {
    padding: 15px 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid var(--slip-border);
    background: var(--slip-light);
}

.section-heading h5 {
    margin: 0;
    font-size: 17px;
    font-weight: 900;
}

.section-heading p {
    margin: 3px 0 0;
    color: var(--slip-muted);
    font-size: 12px;
}

.section-heading > i {
    font-size: 22px;
}

.expense-detail-table {
    width: 100%;
    border-collapse: collapse;
}

.expense-detail-table th,
.expense-detail-table td {
    padding: 15px 18px;
    border-bottom: 1px solid #edf0f2;
    font-size: 14px;
    text-align: left;
}

.expense-detail-table th {
    width: 38%;
    background: #fbfbfc;
    font-weight: 900;
}

.expense-detail-table th i {
    width: 22px;
    margin-right: 7px;
}

.expense-detail-table tr:last-child th,
.expense-detail-table tr:last-child td {
    border-bottom: 0;
}

.receipt-image-wrapper {
    padding: 18px;
    display: block;
    background: var(--slip-light);
}

.receipt-image-wrapper img {
    display: block;
    width: 100%;
    height: 430px;
    object-fit: contain;
    border: 1px solid var(--slip-border);
    border-radius: 10px;
    background: white;
}

.open-receipt-button {
    margin: 0 18px 18px;
    padding: 10px 14px;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    border-radius: 7px;
    color: white;
    background: var(--slip-black);
    text-decoration: none;
    font-size: 13px;
    font-weight: 900;
}

.pdf-receipt-box {
    padding: 22px;
    display: flex;
    align-items: center;
    gap: 14px;
}

.pdf-icon {
    width: 55px;
    height: 55px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 11px;
    color: white;
    background: #dc3545;
    font-size: 27px;
}

.pdf-receipt-box > div:nth-child(2) {
    min-width: 0;
    flex: 1;
}

.pdf-receipt-box strong,
.pdf-receipt-box span {
    display: block;
}

.pdf-receipt-box strong {
    font-size: 15px;
}

.pdf-receipt-box span {
    margin-top: 3px;
    overflow: hidden;
    color: var(--slip-muted);
    font-size: 12px;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.open-pdf-button {
    padding: 9px 13px;
    border-radius: 7px;
    color: white;
    background: var(--slip-black);
    text-decoration: none;
    font-size: 12px;
    font-weight: 900;
}

.no-receipt-box {
    min-height: 190px;
    padding: 25px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: var(--slip-muted);
    text-align: center;
}

.no-receipt-box i {
    margin-bottom: 8px;
    color: var(--slip-black);
    font-size: 35px;
}

.no-receipt-box strong {
    font-size: 15px;
}

.no-receipt-box span {
    margin-top: 5px;
    font-size: 12px;
}

.slip-footer {
    margin-top: 55px;
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 80px;
}

.signature-box {
    text-align: center;
}

.signature-line {
    margin-bottom: 9px;
    border-top: 1px solid var(--slip-black);
}

.signature-box strong {
    font-size: 13px;
}

.printed-date {
    margin-top: 28px;
    color: var(--slip-muted);
    font-size: 10px;
    text-align: center;
}

@media(max-width: 767px) {
    .expense-slip-page-header,
    .slip-header {
        align-items: stretch;
        flex-direction: column;
    }

    .expense-slip-actions {
        display: grid;
        grid-template-columns: 1fr;
    }

    .expense-main-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .expense-slip {
        padding: 20px;
    }
}

@media print {
    .no-print,
    aside,
    nav {
        display: none !important;
    }

    body,
    main {
        margin: 0 !important;
        padding: 0 !important;
        background: white !important;
    }

    .expense-slip {
        max-width: 100%;
        border: 2px solid #000;
        border-radius: 0;
        box-shadow: none;
    }

    .receipt-image-wrapper img {
        max-height: 330px;
    }
}
</style>

@endsection
