@extends('layouts.app')

@section('content')

<div class="expense-create-page">

    {{-- PAGE HEADER --}}
    <div class="expense-page-header">

        <div>
            <h3>Add Expense</h3>

            <p>
                Add factory expense details and attach its bill or receipt
            </p>
        </div>

        <a href="{{ route('expenses.index') }}"
           class="back-expense-button">

            <i class="bi bi-arrow-left"></i>
            Back to Expenses

        </a>

    </div>

    {{-- VALIDATION ERRORS --}}
    @if($errors->any())

        <div class="alert alert-danger page-alert">

            <div class="fw-bold mb-2">

                <i class="bi bi-exclamation-circle-fill me-2"></i>
                Please fix the following errors:

            </div>

            <ul class="mb-0">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif

    <form
        action="{{ route('expenses.store') }}"
        method="POST"
        enctype="multipart/form-data"
        id="expenseForm"
    >
        @csrf

        <input
            type="hidden"
            name="mobile_receipt_token"
            id="mobile_receipt_token"
            value="{{ old('mobile_receipt_token') }}"
        >

        {{-- EXPENSE DETAILS --}}
        <div class="expense-form-card">

            <div class="form-card-header">

                <div>
                    <h4>Expense Details</h4>

                    <p>
                        Enter expense category, payment and vendor information
                    </p>
                </div>

                <div class="form-header-icon">
                    <i class="bi bi-wallet2"></i>
                </div>

            </div>

            <div class="form-card-body">

                <div class="row g-3">

                    {{-- DATE --}}
                    <div class="col-lg-6">

                        <label class="field-label">
                            Expense Date
                            <span>*</span>
                        </label>

                        <div class="input-icon-wrapper">

                            <i class="bi bi-calendar3"></i>

                            <input
                                type="date"
                                name="expense_date"
                                value="{{ old('expense_date', date('Y-m-d')) }}"
                                class="form-control styled-input @error('expense_date') is-invalid @enderror"
                                required
                            >

                        </div>

                        @error('expense_date')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    {{-- CATEGORY --}}
                    <div class="col-lg-6">

                        <label class="field-label">
                            Category
                            <span>*</span>
                        </label>

                        <div class="input-icon-wrapper">

                            <i class="bi bi-folder-fill"></i>

                            <select
                                name="expense_category_id"
                                id="expense_category_id"
                                class="form-select styled-input @error('expense_category_id') is-invalid @enderror"
                                required
                            >
                                <option value="">
                                    Select Category
                                </option>

                                @foreach($categories as $category)

                                    <option
                                        value="{{ $category->id }}"
                                        {{ old('expense_category_id') == $category->id ? 'selected' : '' }}
                                    >
                                        {{ $category->name }}
                                    </option>

                                @endforeach

                            </select>

                        </div>

                        @error('expense_category_id')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    {{-- SUB CATEGORY --}}
                    <div class="col-lg-6">

                        <label class="field-label">
                            Sub Category
                        </label>

                        <div class="input-icon-wrapper">

                            <i class="bi bi-diagram-3-fill"></i>

                            <select
                                name="expense_sub_category_id"
                                id="expense_sub_category_id"
                                class="form-select styled-input @error('expense_sub_category_id') is-invalid @enderror"
                                data-selected="{{ old('expense_sub_category_id') }}"
                            >
                                <option value="">
                                    Select Sub Category
                                </option>
                            </select>

                        </div>

                        <small
                            id="subcategoryHelp"
                            class="field-help-text"
                        >
                            Select a category first.
                        </small>

                        @error('expense_sub_category_id')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    {{-- ACCOUNT --}}
                    <div class="col-lg-6">

                        <label class="field-label">
                            Expense Account
                        </label>

                        <div class="input-icon-wrapper">

                            <i class="bi bi-bank"></i>

                            <select
                                name="account_id"
                                class="form-select styled-input @error('account_id') is-invalid @enderror"
                            >
                                <option value="">
                                    Select Account
                                </option>

                                @foreach($accounts as $account)

                                    <option
                                        value="{{ $account->id }}"
                                        {{ old('account_id') == $account->id ? 'selected' : '' }}
                                    >
                                        {{ $account->code }} - {{ $account->name }}
                                    </option>

                                @endforeach

                            </select>

                        </div>

                        @error('account_id')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    {{-- AMOUNT --}}
                    <div class="col-lg-6">

                        <label class="field-label">
                            Amount
                            <span>*</span>
                        </label>

                        <div class="input-icon-wrapper">

                            <i class="bi bi-cash-stack"></i>

                            <input
                                type="number"
                                step="0.01"
                                min="0.01"
                                name="amount"
                                value="{{ old('amount') }}"
                                class="form-control styled-input @error('amount') is-invalid @enderror"
                                placeholder="0.00"
                                required
                            >

                        </div>

                        @error('amount')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    {{-- PAYMENT METHOD --}}
                    <div class="col-lg-6">

                        <label class="field-label">
                            Payment Method
                            <span>*</span>
                        </label>

                        <div class="input-icon-wrapper">

                            <i class="bi bi-credit-card-fill"></i>

                            <select
                                name="payment_method"
                                class="form-select styled-input @error('payment_method') is-invalid @enderror"
                                required
                            >
                                <option
                                    value="cash"
                                    {{ old('payment_method', 'cash') === 'cash' ? 'selected' : '' }}
                                >
                                    Cash
                                </option>

                                <option
                                    value="bank"
                                    {{ old('payment_method') === 'bank' ? 'selected' : '' }}
                                >
                                    Bank
                                </option>

                                <option
                                    value="cheque"
                                    {{ old('payment_method') === 'cheque' ? 'selected' : '' }}
                                >
                                    Cheque
                                </option>

                                <option
                                    value="online"
                                    {{ old('payment_method') === 'online' ? 'selected' : '' }}
                                >
                                    Online
                                </option>
                            </select>

                        </div>

                        @error('payment_method')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    {{-- PAID FROM ACCOUNT --}}
                    <div class="col-lg-6">

                        <label class="field-label">
                            Paid From
                            <span>*</span>
                        </label>

                        <div class="input-icon-wrapper">

                            <i class="bi bi-wallet-fill"></i>

                            <select
                                name="paid_from_account_id"
                                class="form-select styled-input @error('paid_from_account_id') is-invalid @enderror"
                                required
                            >
                                <option value="">
                                    Select Cash or Bank Account
                                </option>

                                @foreach($paidFromAccounts as $paidFromAccount)

                                    <option
                                        value="{{ $paidFromAccount->id }}"
                                        {{ old('paid_from_account_id') == $paidFromAccount->id ? 'selected' : '' }}
                                    >
                                        {{ $paidFromAccount->code }} - {{ $paidFromAccount->name }}
                                    </option>

                                @endforeach

                            </select>

                        </div>

                        <small class="field-help-text">
                            Select the cash or bank account used for this payment.
                        </small>

                        @error('paid_from_account_id')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    {{-- PAID BY --}}
                    <div class="col-lg-6">

                        <label class="field-label">
                            Paid By
                        </label>

                        <div class="input-icon-wrapper">

                            <i class="bi bi-person-check-fill"></i>

                            <input
                                type="text"
                                name="paid_by"
                                value="{{ old('paid_by') }}"
                                class="form-control styled-input @error('paid_by') is-invalid @enderror"
                                placeholder="Person name"
                            >

                        </div>

                        @error('paid_by')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    {{-- VENDOR --}}
                    <div class="col-lg-6">

                        <label class="field-label">
                            Vendor / Person
                        </label>

                        <div class="input-icon-wrapper">

                            <i class="bi bi-person-workspace"></i>

                            <input
                                type="text"
                                name="vendor_name"
                                value="{{ old('vendor_name') }}"
                                class="form-control styled-input @error('vendor_name') is-invalid @enderror"
                                placeholder="Vendor or person name"
                            >

                        </div>

                        @error('vendor_name')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    {{-- DESCRIPTION --}}
                    <div class="col-12">

                        <label class="field-label">
                            Description
                        </label>

                        <div class="textarea-icon-wrapper">

                            <i class="bi bi-card-text"></i>

                            <textarea
                                name="description"
                                rows="4"
                                class="form-control styled-textarea @error('description') is-invalid @enderror"
                                placeholder="Enter expense details"
                            >{{ old('description') }}</textarea>

                        </div>

                        @error('description')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>

            </div>

        </div>

        {{-- RECEIPT SECTION --}}
        <div class="expense-form-card">

            <div class="form-card-header">

                <div>
                    <h4>Receipt / Bill</h4>

                    <p>
                        Upload from computer or take a picture directly from mobile
                    </p>
                </div>

                <div class="form-header-icon">
                    <i class="bi bi-receipt-cutoff"></i>
                </div>

            </div>

            <div class="form-card-body">

                <div class="receipt-section">

                    <div class="row g-3">

                        {{-- COMPUTER BOX --}}
                        <div class="col-xl-6">

                            <div class="receipt-method-card computer-method-card h-100">

                                <div class="receipt-method-header">

                                    <div class="receipt-method-icon">
                                        <i class="fa-solid fa-desktop"></i>
                                    </div>

                                    <div>
                                        <h5>
                                            Upload From Computer
                                        </h5>

                                        <p>
                                            Select an image or PDF already saved on your computer.
                                        </p>
                                    </div>

                                </div>

                                <label
                                    for="receipt"
                                    class="computer-upload-area"
                                    id="computerUploadArea"
                                >

                                    <div class="computer-upload-symbol">
                                        <i class="fa-solid fa-cloud-arrow-up"></i>
                                    </div>

                                    <strong>
                                        Choose Receipt File
                                    </strong>

                                    <span>
                                        JPG, PNG, WEBP or PDF up to 100 MB
                                    </span>

                                </label>

                                <input
                                    type="file"
                                    name="receipt"
                                    id="receipt"
                                    class="d-none"
                                    accept="image/*,.pdf"
                                >

                                {{-- COMPUTER PREVIEW --}}
                                <div
                                    id="computerPreviewBox"
                                    class="method-preview-box d-none"
                                >

                                    <div class="method-preview-header">

                                        <div>
                                            <strong>
                                                Selected Receipt
                                            </strong>

                                            <span id="computerFileName"></span>
                                        </div>

                                        <button
                                            type="button"
                                            id="removeComputerReceiptButton"
                                            class="preview-remove-button"
                                            title="Remove Receipt"
                                        >
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>

                                    </div>

                                    <div
                                        id="computerImagePreviewContainer"
                                        class="preview-media-container d-none"
                                    >
                                        <img
                                            src=""
                                            id="computerPreviewImage"
                                            class="inside-receipt-image"
                                            alt="Computer Receipt Preview"
                                        >
                                    </div>

                                    <div
                                        id="computerPdfPreviewContainer"
                                        class="inside-pdf-preview d-none"
                                    >
                                        <i class="fa-solid fa-file-pdf"></i>

                                        <div>
                                            <strong>
                                                PDF Receipt
                                            </strong>

                                            <span id="computerPdfFileName"></span>
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>

                        {{-- MOBILE BOX --}}
                        <div class="col-xl-6">

                            <div class="receipt-method-card mobile-method-card h-100">

                                <div class="receipt-method-header">

                                    <div class="receipt-method-icon mobile-method-icon">
                                        <i class="fa-solid fa-mobile-screen-button"></i>
                                    </div>

                                    <div>
                                        <h5>
                                            Take Picture From Mobile
                                        </h5>

                                        <p>
                                            Generate a QR code, scan it and take the bill picture.
                                        </p>
                                    </div>

                                </div>

                                <button
                                    type="button"
                                    id="connectMobileButton"
                                    class="connect-mobile-button"
                                >
                                    <i class="fa-solid fa-qrcode"></i>
                                    Connect Mobile Camera
                                </button>

                                {{-- QR INSIDE MOBILE BOX --}}
                                <div
                                    id="qrSection"
                                    class="mobile-inside-section d-none"
                                >

                                    <div class="mobile-qr-layout">

                                        <div
                                            id="qrCode"
                                            class="qr-code-box"
                                        ></div>

                                        <div class="mobile-qr-information">

                                            <h6>
                                                Scan QR From Mobile
                                            </h6>

                                            <p>
                                                Open the mobile camera and scan this QR code.
                                            </p>

                                            <div
                                                id="waitingStatus"
                                                class="mobile-status waiting-status"
                                            >
                                                <span class="spinner-border spinner-border-sm"></span>

                                                Waiting for bill picture...
                                            </div>

                                            <div class="mobile-link-area">

                                                <label>
                                                    Mobile Link
                                                </label>

                                                <div class="mobile-link-control">

                                                    <input
                                                        type="text"
                                                        id="mobileUrlInput"
                                                        readonly
                                                    >

                                                    <button
                                                        type="button"
                                                        id="copyMobileUrlButton"
                                                        title="Copy Mobile Link"
                                                    >
                                                        <i class="fa-regular fa-copy"></i>
                                                    </button>

                                                </div>

                                            </div>

                                            <button
                                                type="button"
                                                id="generateNewQrButton"
                                                class="generate-new-qr-button"
                                            >
                                                <i class="fa-solid fa-rotate"></i>
                                                Generate New QR
                                            </button>

                                        </div>

                                    </div>

                                </div>

                                {{-- MOBILE PREVIEW INSIDE MOBILE BOX --}}
                                <div
                                    id="mobilePreviewBox"
                                    class="method-preview-box mobile-preview-box d-none"
                                >

                                    <div class="method-preview-header">

                                        <div>
                                            <strong>
                                                Bill Received From Mobile
                                            </strong>

                                            <span id="mobileFileName"></span>
                                        </div>

                                        <button
                                            type="button"
                                            id="removeMobileReceiptButton"
                                            class="preview-remove-button"
                                            title="Remove Receipt"
                                        >
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>

                                    </div>

                                    <div
                                        id="mobileImagePreviewContainer"
                                        class="preview-media-container d-none"
                                    >
                                        <img
                                            src=""
                                            alt="Mobile Bill Preview"
                                            id="mobilePreviewImage"
                                            class="inside-receipt-image"
                                        >
                                    </div>

                                    <div
                                        id="mobilePdfPreviewContainer"
                                        class="inside-pdf-preview d-none"
                                    >
                                        <i class="fa-solid fa-file-pdf"></i>

                                        <div>
                                            <strong>
                                                PDF Receipt
                                            </strong>

                                            <span id="mobilePdfFileName"></span>
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                @error('receipt')
                    <div class="text-danger small mt-2">
                        {{ $message }}
                    </div>
                @enderror

                @error('mobile_receipt_token')
                    <div class="text-danger small mt-2">
                        {{ $message }}
                    </div>
                @enderror

            </div>

        </div>

        {{-- FORM ACTIONS --}}
        <div class="form-actions">

            <button
                type="submit"
                class="save-expense-button"
                id="saveExpenseButton"
            >
                <i class="fa-solid fa-floppy-disk"></i>
                Save Expense
            </button>

            <a
                href="{{ route('expenses.index') }}"
                class="cancel-expense-button"
            >
                Cancel
            </a>

        </div>

    </form>

</div>

<style>
:root {
    --expense-black: #111111;
    --expense-white: #ffffff;
    --expense-border: #dfe3e8;
    --expense-light: #f6f7f9;
    --expense-muted: #737b86;
    --expense-blue: #0d6efd;
    --expense-red: #dc3545;
}

.expense-create-page {
    color: var(--expense-black);
}

.expense-page-header {
    margin-bottom: 22px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
}

.expense-page-header h3 {
    margin: 0;
    font-size: 29px;
    font-weight: 900;
}

.expense-page-header p {
    margin: 4px 0 0;
    color: var(--expense-muted);
    font-size: 13px;
}

.back-expense-button {
    min-height: 43px;
    padding: 9px 15px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    border: 1px solid var(--expense-black);
    border-radius: 8px;
    color: var(--expense-white);
    background: var(--expense-black);
    text-decoration: none;
    font-size: 13px;
    font-weight: 900;
}

.back-expense-button:hover {
    color: var(--expense-black);
    background: var(--expense-white);
}

.page-alert {
    border-radius: 10px;
}

.expense-form-card {
    margin-bottom: 18px;
    overflow: hidden;
    border: 1px solid var(--expense-border);
    border-radius: 14px;
    background: var(--expense-white);
    box-shadow: 0 6px 20px rgba(17, 24, 39, 0.05);
}

.form-card-header {
    min-height: 77px;
    padding: 17px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid var(--expense-border);
}

.form-card-header h4 {
    margin: 0;
    font-size: 19px;
    font-weight: 900;
}

.form-card-header p {
    margin: 4px 0 0;
    color: var(--expense-muted);
    font-size: 12px;
}

.form-header-icon {
    width: 42px;
    height: 42px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    color: var(--expense-white);
    background: var(--expense-black);
    font-size: 19px;
}

.form-card-body {
    padding: 20px;
}

.field-label {
    margin-bottom: 7px;
    color: var(--expense-black);
    font-size: 12px;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.field-label span {
    color: var(--expense-red);
}

.field-help-text {
    display: block;
    margin-top: 5px;
    color: var(--expense-muted);
    font-size: 11px;
}

.input-icon-wrapper,
.textarea-icon-wrapper {
    position: relative;
}

.input-icon-wrapper > i,
.textarea-icon-wrapper > i {
    position: absolute;
    left: 14px;
    z-index: 2;
    color: var(--expense-black);
    font-size: 15px;
    pointer-events: none;
}

.input-icon-wrapper > i {
    top: 50%;
    transform: translateY(-50%);
}

.textarea-icon-wrapper > i {
    top: 14px;
}

.styled-input {
    min-height: 47px;
    padding-left: 42px;
    border: 1px solid var(--expense-border);
    border-radius: 9px;
    color: var(--expense-black);
    background: var(--expense-light);
    font-size: 14px;
    font-weight: 650;
    box-shadow: none !important;
}

.styled-textarea {
    padding: 13px 14px 13px 42px;
    border: 1px solid var(--expense-border);
    border-radius: 9px;
    color: var(--expense-black);
    background: var(--expense-light);
    font-size: 14px;
    box-shadow: none !important;
}

.styled-input:focus,
.styled-textarea:focus {
    border-color: var(--expense-black);
    background: var(--expense-white);
}

.receipt-section {
    padding: 14px;
    overflow: hidden;
    border: 1px solid var(--expense-border);
    border-radius: 14px;
    background: var(--expense-light);
}

.receipt-method-card {
    min-height: 285px;
    padding: 18px;
    overflow: hidden;
    border: 1px solid var(--expense-border);
    border-radius: 13px;
    background: var(--expense-white);
    box-shadow: 0 4px 14px rgba(17, 24, 39, 0.04);
}

.mobile-method-card {
    border-color: rgba(13, 110, 253, 0.28);
    background: #fbfdff;
}

.receipt-method-header {
    margin-bottom: 16px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.receipt-method-icon {
    flex: 0 0 46px;
    width: 46px;
    height: 46px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 11px;
    color: var(--expense-black);
    background: #eef0f3;
    font-size: 19px;
}

.mobile-method-icon {
    color: var(--expense-blue);
    background: rgba(13, 110, 253, 0.1);
}

.receipt-method-header h5 {
    margin: 0;
    color: var(--expense-black);
    font-size: 17px;
    font-weight: 900;
}

.receipt-method-header p {
    margin: 4px 0 0;
    color: var(--expense-muted);
    font-size: 12px;
    line-height: 1.5;
}

.computer-upload-area {
    min-height: 160px;
    padding: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border: 1.5px dashed #bfc5cd;
    border-radius: 11px;
    color: var(--expense-black);
    background: #f8f9fa;
    text-align: center;
    cursor: pointer;
    transition: 0.2s ease;
}

.computer-upload-area:hover {
    border-color: var(--expense-black);
    background: var(--expense-white);
}

.computer-upload-symbol {
    width: 50px;
    height: 50px;
    margin-bottom: 11px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    color: var(--expense-white);
    background: var(--expense-black);
    font-size: 21px;
}

.computer-upload-area strong {
    font-size: 14px;
    font-weight: 900;
}

.computer-upload-area span {
    margin-top: 5px;
    color: var(--expense-muted);
    font-size: 11px;
}

.connect-mobile-button {
    width: 100%;
    min-height: 49px;
    padding: 10px 15px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    border: 1px solid var(--expense-blue);
    border-radius: 9px;
    color: var(--expense-white);
    background: var(--expense-blue);
    font-size: 14px;
    font-weight: 900;
    transition: 0.2s ease;
}

.connect-mobile-button:hover {
    color: var(--expense-blue);
    background: var(--expense-white);
}

.mobile-inside-section {
    margin-top: 14px;
    padding: 13px;
    border: 1px solid #d9e5f7;
    border-radius: 11px;
    background: var(--expense-white);
}

.mobile-qr-layout {
    display: grid;
    grid-template-columns: 150px minmax(0, 1fr);
    align-items: center;
    gap: 14px;
}

.qr-code-box {
    width: 150px;
    height: 150px;
    padding: 7px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    border: 1px solid var(--expense-border);
    border-radius: 9px;
    background: var(--expense-white);
}

.qr-code-box img,
.qr-code-box canvas {
    width: 100% !important;
    height: 100% !important;
    object-fit: contain;
}

.mobile-qr-information {
    min-width: 0;
}

.mobile-qr-information h6 {
    margin: 0;
    color: var(--expense-black);
    font-size: 14px;
    font-weight: 900;
}

.mobile-qr-information > p {
    margin: 4px 0 10px;
    color: var(--expense-muted);
    font-size: 11px;
}

.mobile-status {
    padding: 9px 10px;
    display: flex;
    align-items: center;
    gap: 7px;
    border-radius: 7px;
    font-size: 11px;
    font-weight: 800;
}

.waiting-status {
    border: 1px solid #ffe69c;
    color: #664d03;
    background: #fff3cd;
}

.success-status {
    border: 1px solid #badbcc;
    color: #0f5132;
    background: #d1e7dd;
}

.error-status {
    border: 1px solid #f5c2c7;
    color: #842029;
    background: #f8d7da;
}

.mobile-link-area {
    margin-top: 10px;
}

.mobile-link-area label {
    display: block;
    margin-bottom: 4px;
    color: var(--expense-black);
    font-size: 10px;
    font-weight: 900;
}

.mobile-link-control {
    display: flex;
    overflow: hidden;
    border: 1px solid var(--expense-border);
    border-radius: 7px;
    background: #f8f9fa;
}

.mobile-link-control input {
    width: 100%;
    min-width: 0;
    height: 37px;
    padding: 7px 9px;
    border: 0;
    outline: 0;
    color: #555b63;
    background: transparent;
    font-size: 10px;
}

.mobile-link-control button {
    flex: 0 0 39px;
    width: 39px;
    border: 0;
    border-left: 1px solid var(--expense-border);
    color: var(--expense-black);
    background: var(--expense-white);
}

.generate-new-qr-button {
    margin-top: 9px;
    padding: 7px 10px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border: 1px solid var(--expense-blue);
    border-radius: 7px;
    color: var(--expense-blue);
    background: var(--expense-white);
    font-size: 10px;
    font-weight: 900;
}

.method-preview-box {
    margin-top: 14px;
    padding: 12px;
    overflow: hidden;
    border: 1px solid #badbcc;
    border-radius: 10px;
    background: #f4fff8;
}

.mobile-preview-box {
    border-color: rgba(13, 110, 253, 0.28);
    background: #f6f9ff;
}

.method-preview-header {
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}

.method-preview-header strong,
.method-preview-header span {
    display: block;
}

.method-preview-header strong {
    color: var(--expense-black);
    font-size: 13px;
    font-weight: 900;
}

.method-preview-header span {
    max-width: 330px;
    margin-top: 2px;
    overflow: hidden;
    color: var(--expense-muted);
    font-size: 10px;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.preview-remove-button {
    flex: 0 0 33px;
    width: 33px;
    height: 33px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--expense-red);
    border-radius: 7px;
    color: var(--expense-red);
    background: var(--expense-white);
}

.preview-remove-button:hover {
    color: var(--expense-white);
    background: var(--expense-red);
}

.preview-media-container {
    width: 100%;
    overflow: hidden;
    border: 1px solid var(--expense-border);
    border-radius: 9px;
    background: var(--expense-white);
}

.inside-receipt-image {
    display: block;
    width: 100%;
    height: 280px;
    object-fit: contain;
    background: var(--expense-white);
}

.inside-pdf-preview {
    min-height: 110px;
    padding: 15px;
    display: flex;
    align-items: center;
    gap: 12px;
    border: 1px solid var(--expense-border);
    border-radius: 9px;
    background: var(--expense-white);
}

.inside-pdf-preview > i {
    color: var(--expense-red);
    font-size: 38px;
}

.inside-pdf-preview strong,
.inside-pdf-preview span {
    display: block;
}

.inside-pdf-preview strong {
    font-size: 13px;
}

.inside-pdf-preview span {
    margin-top: 3px;
    color: var(--expense-muted);
    font-size: 10px;
}

.form-actions {
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 9px;
}

.save-expense-button,
.cancel-expense-button {
    min-height: 45px;
    padding: 10px 18px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    border: 1px solid var(--expense-black);
    border-radius: 8px;
    font-size: 13px;
    font-weight: 900;
}

.save-expense-button {
    color: var(--expense-white);
    background: var(--expense-black);
}

.save-expense-button:hover {
    color: var(--expense-black);
    background: var(--expense-white);
}

.cancel-expense-button {
    color: var(--expense-black);
    background: var(--expense-white);
    text-decoration: none;
}

@media(max-width: 1199px) {
    .mobile-qr-layout {
        grid-template-columns: 130px minmax(0, 1fr);
    }

    .qr-code-box {
        width: 130px;
        height: 130px;
    }
}

@media(max-width: 767px) {
    .expense-page-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .back-expense-button {
        width: 100%;
    }

    .form-card-body {
        padding: 15px;
    }

    .receipt-section {
        padding: 10px;
    }

    .receipt-method-card {
        padding: 14px;
    }

    .mobile-qr-layout {
        grid-template-columns: 1fr;
    }

    .qr-code-box {
        width: 150px;
        height: 150px;
        margin: 0 auto;
    }

    .inside-receipt-image {
        height: 220px;
    }

    .form-actions {
        display: grid;
        grid-template-columns: 1fr;
    }
}
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>
const categories = @json($categories);

document.addEventListener('DOMContentLoaded', function () {
    const categorySelect = document.getElementById(
        'expense_category_id'
    );

    const subCategorySelect = document.getElementById(
        'expense_sub_category_id'
    );

    const subcategoryHelp = document.getElementById(
        'subcategoryHelp'
    );

    const connectMobileButton = document.getElementById(
        'connectMobileButton'
    );

    const generateNewQrButton = document.getElementById(
        'generateNewQrButton'
    );

    const copyMobileUrlButton = document.getElementById(
        'copyMobileUrlButton'
    );

    const qrSection = document.getElementById(
        'qrSection'
    );

    const qrCode = document.getElementById(
        'qrCode'
    );

    const waitingStatus = document.getElementById(
        'waitingStatus'
    );

    const mobileUrlInput = document.getElementById(
        'mobileUrlInput'
    );

    const mobileReceiptTokenInput = document.getElementById(
        'mobile_receipt_token'
    );

    const receiptInput = document.getElementById(
        'receipt'
    );

    const computerPreviewBox = document.getElementById(
        'computerPreviewBox'
    );

    const computerImagePreviewContainer = document.getElementById(
        'computerImagePreviewContainer'
    );

    const computerPdfPreviewContainer = document.getElementById(
        'computerPdfPreviewContainer'
    );

    const computerPreviewImage = document.getElementById(
        'computerPreviewImage'
    );

    const computerFileName = document.getElementById(
        'computerFileName'
    );

    const computerPdfFileName = document.getElementById(
        'computerPdfFileName'
    );

    const removeComputerReceiptButton = document.getElementById(
        'removeComputerReceiptButton'
    );

    const mobilePreviewBox = document.getElementById(
        'mobilePreviewBox'
    );

    const mobileImagePreviewContainer = document.getElementById(
        'mobileImagePreviewContainer'
    );

    const mobilePdfPreviewContainer = document.getElementById(
        'mobilePdfPreviewContainer'
    );

    const mobilePreviewImage = document.getElementById(
        'mobilePreviewImage'
    );

    const mobileFileName = document.getElementById(
        'mobileFileName'
    );

    const mobilePdfFileName = document.getElementById(
        'mobilePdfFileName'
    );

    const removeMobileReceiptButton = document.getElementById(
        'removeMobileReceiptButton'
    );

    let uploadToken = null;
    let statusInterval = null;
    let localPreviewUrl = null;
    let receiptSource = null;

    function loadSubCategories() {
        const categoryId = categorySelect.value;

        const selectedSubCategoryId =
            subCategorySelect.dataset.selected;

        subCategorySelect.innerHTML =
            '<option value="">Select Sub Category</option>';

        const category = categories.find(function (item) {
            return String(item.id) === String(categoryId);
        });

        if (
            category &&
            Array.isArray(category.sub_categories) &&
            category.sub_categories.length > 0
        ) {
            category.sub_categories.forEach(function (subCategory) {
                const option = document.createElement('option');

                option.value = subCategory.id;
                option.textContent = subCategory.name;

                if (
                    selectedSubCategoryId &&
                    String(selectedSubCategoryId) ===
                    String(subCategory.id)
                ) {
                    option.selected = true;
                }

                subCategorySelect.appendChild(option);
            });

            subCategorySelect.disabled = false;
            subCategorySelect.required = true;

            subcategoryHelp.textContent =
                'Select the required sub category.';
        } else {
            subCategorySelect.disabled = true;
            subCategorySelect.required = false;

            subcategoryHelp.textContent =
                categoryId
                    ? 'This category has no sub categories.'
                    : 'Select a category first.';
        }

        subCategorySelect.dataset.selected = '';
    }

    async function generateMobileSession() {
        stopPolling();

        clearMobilePreview();

        connectMobileButton.disabled = true;

        connectMobileButton.innerHTML = `
            <span class="spinner-border spinner-border-sm"></span>
            Generating QR...
        `;

        try {
            const response = await fetch(
                '{{ route('expense.receipt.session') }}',
                {
                    method: 'POST',

                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },

                    body: JSON.stringify({})
                }
            );

            let result = null;

            try {
                result = await response.json();
            } catch (error) {
                throw new Error(
                    'Server returned an invalid response.'
                );
            }

            if (!response.ok || !result.success) {
                throw new Error(
                    result.message ||
                    'Unable to create mobile session.'
                );
            }

            uploadToken = result.token;

            mobileReceiptTokenInput.value =
                uploadToken;

            mobileUrlInput.value =
                result.mobile_url;

            qrCode.innerHTML = '';

            new QRCode(qrCode, {
                text: result.mobile_url,
                width: 136,
                height: 136,
                correctLevel: QRCode.CorrectLevel.H
            });

            qrSection.classList.remove('d-none');

            waitingStatus.className =
                'mobile-status waiting-status';

            waitingStatus.innerHTML = `
                <span class="spinner-border spinner-border-sm"></span>
                Waiting for bill picture...
            `;

            startPolling();
        } catch (error) {
            alert(error.message);
        } finally {
            connectMobileButton.disabled = false;

            connectMobileButton.innerHTML = `
                <i class="fa-solid fa-qrcode"></i>
                Connect Mobile Camera
            `;
        }
    }

    function startPolling() {
        stopPolling();

        checkUploadStatus();

        statusInterval = setInterval(function () {
            checkUploadStatus();
        }, 2000);
    }

    function stopPolling() {
        if (statusInterval) {
            clearInterval(statusInterval);
            statusInterval = null;
        }
    }

    async function checkUploadStatus() {
        if (!uploadToken) {
            return;
        }

        const statusUrl =
            `{{ url('/expense-receipt/status') }}/${uploadToken}`;

        try {
            const response = await fetch(statusUrl, {
                headers: {
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (!response.ok) {
                return;
            }

            if (
                result.status === 'uploaded' &&
                result.file_url
            ) {
                stopPolling();

                receiptSource = 'mobile';

                receiptInput.value = '';

                clearComputerPreview();

                waitingStatus.className =
                    'mobile-status success-status';

                waitingStatus.innerHTML = `
                    <i class="fa-solid fa-circle-check"></i>
                    Bill picture received from mobile.
                `;

                showRemotePreview(
                    result.file_url,
                    result.original_name,
                    result.mime_type
                );
            }

            if (result.status === 'expired') {
                stopPolling();

                waitingStatus.className =
                    'mobile-status error-status';

                waitingStatus.innerHTML = `
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    QR code expired. Generate a new QR code.
                `;
            }
        } catch (error) {
            console.error(
                'Status checking failed:',
                error
            );
        }
    }

    function clearComputerPreview() {
        if (localPreviewUrl) {
            URL.revokeObjectURL(localPreviewUrl);
            localPreviewUrl = null;
        }

        computerPreviewImage.src = '';
        computerFileName.textContent = '';
        computerPdfFileName.textContent = '';

        computerImagePreviewContainer.classList.add(
            'd-none'
        );

        computerPdfPreviewContainer.classList.add(
            'd-none'
        );

        computerPreviewBox.classList.add(
            'd-none'
        );
    }

    function clearMobilePreview() {
        mobilePreviewImage.src = '';
        mobileFileName.textContent = '';
        mobilePdfFileName.textContent = '';

        mobileImagePreviewContainer.classList.add(
            'd-none'
        );

        mobilePdfPreviewContainer.classList.add(
            'd-none'
        );

        mobilePreviewBox.classList.add(
            'd-none'
        );
    }

    function showLocalPreview(file) {
        clearComputerPreview();
        clearMobilePreview();

        receiptSource = 'computer';

        mobileReceiptTokenInput.value = '';

        if (uploadToken) {
            stopPolling();
        }

        computerFileName.textContent =
            file.name;

        computerPreviewBox.classList.remove(
            'd-none'
        );

        if (
            file.type === 'application/pdf' ||
            file.name.toLowerCase().endsWith('.pdf')
        ) {
            computerPdfFileName.textContent =
                file.name;

            computerPdfPreviewContainer.classList.remove(
                'd-none'
            );

            return;
        }

        localPreviewUrl =
            URL.createObjectURL(file);

        computerPreviewImage.src =
            localPreviewUrl;

        computerImagePreviewContainer.classList.remove(
            'd-none'
        );
    }

    function showRemotePreview(
        fileUrl,
        fileName,
        mimeType
    ) {
        clearComputerPreview();
        clearMobilePreview();

        receiptSource = 'mobile';

        mobileFileName.textContent =
            fileName || 'Mobile bill picture';

        mobilePreviewBox.classList.remove(
            'd-none'
        );

        if (
            mimeType === 'application/pdf' ||
            String(fileName || '')
                .toLowerCase()
                .endsWith('.pdf')
        ) {
            mobilePdfFileName.textContent =
                fileName || 'Receipt.pdf';

            mobilePdfPreviewContainer.classList.remove(
                'd-none'
            );

            return;
        }

        mobilePreviewImage.src =
            fileUrl + '?time=' + Date.now();

        mobileImagePreviewContainer.classList.remove(
            'd-none'
        );
    }

    async function removeComputerReceipt() {
        receiptInput.value = '';

        clearComputerPreview();

        receiptSource = null;
    }

    async function removeMobileReceipt() {
        clearMobilePreview();

        if (uploadToken) {
            try {
                await fetch(
                    `{{ url('/expense-receipt') }}/${uploadToken}`,
                    {
                        method: 'DELETE',

                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }
                );
            } catch (error) {
                console.error(error);
            }
        }

        uploadToken = null;
        receiptSource = null;

        mobileReceiptTokenInput.value = '';

        qrSection.classList.add('d-none');
        qrCode.innerHTML = '';
        mobileUrlInput.value = '';

        stopPolling();
    }

    categorySelect.addEventListener(
        'change',
        function () {
            subCategorySelect.dataset.selected = '';
            loadSubCategories();
        }
    );

    connectMobileButton.addEventListener(
        'click',
        generateMobileSession
    );

    generateNewQrButton.addEventListener(
        'click',
        generateMobileSession
    );

    copyMobileUrlButton.addEventListener(
        'click',
        async function () {
            if (!mobileUrlInput.value) {
                return;
            }

            try {
                await navigator.clipboard.writeText(
                    mobileUrlInput.value
                );

                copyMobileUrlButton.innerHTML =
                    '<i class="fa-solid fa-check"></i>';

                setTimeout(function () {
                    copyMobileUrlButton.innerHTML =
                        '<i class="fa-regular fa-copy"></i>';
                }, 1500);
            } catch (error) {
                mobileUrlInput.select();
                document.execCommand('copy');
            }
        }
    );

    receiptInput.addEventListener(
        'change',
        function () {
            const file =
                this.files &&
                this.files[0];

            if (!file) {
                clearComputerPreview();
                return;
            }

            stopPolling();

            showLocalPreview(file);
        }
    );

    removeComputerReceiptButton.addEventListener(
        'click',
        removeComputerReceipt
    );

    removeMobileReceiptButton.addEventListener(
        'click',
        removeMobileReceipt
    );

    window.addEventListener(
        'beforeunload',
        stopPolling
    );

    loadSubCategories();
});
</script>

@endsection
