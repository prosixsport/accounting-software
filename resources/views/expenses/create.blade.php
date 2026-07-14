@extends('layouts.app')

@section('content')

<div class="card border-0 shadow-sm">

    <div class="card-header bg-white">
        <h4 class="mb-0 fw-bold">Add Expense</h4>
    </div>

    <div class="card-body">

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
                value=""
            >

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="form-label">Expense Date *</label>

                    <input
                        type="date"
                        name="expense_date"
                        value="{{ old('expense_date', date('Y-m-d')) }}"
                        class="form-control"
                        required
                    >
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Category *</label>

                    <select
                        name="expense_category_id"
                        id="expense_category_id"
                        class="form-select"
                        required
                    >
                        <option value="">Select Category</option>

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

                <div class="col-md-6 mb-3">
                    <label class="form-label">Sub Category</label>

                    <select
                        name="expense_sub_category_id"
                        id="expense_sub_category_id"
                        class="form-select"
                        data-selected="{{ old('expense_sub_category_id') }}"
                    >
                        <option value="">Select Sub Category</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Expense Account</label>

                    <select name="account_id" class="form-select">
                        <option value="">Select Account</option>

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

                <div class="col-md-6 mb-3">
                    <label class="form-label">Amount *</label>

                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        name="amount"
                        value="{{ old('amount') }}"
                        class="form-control"
                        placeholder="0.00"
                        required
                    >
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Payment Method *</label>

                    <select
                        name="payment_method"
                        class="form-select"
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

                <div class="col-md-6 mb-3">
                    <label class="form-label">Paid By</label>

                    <input
                        type="text"
                        name="paid_by"
                        value="{{ old('paid_by') }}"
                        class="form-control"
                        placeholder="Person name"
                    >
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Vendor / Person</label>

                    <input
                        type="text"
                        name="vendor_name"
                        value="{{ old('vendor_name') }}"
                        class="form-control"
                        placeholder="Vendor or person name"
                    >
                </div>

                <div class="col-md-12 mb-3">

                    <label class="form-label fw-semibold">
                        Receipt / Bill
                    </label>

                    <div class="receipt-section">

                        <div class="row g-3">

                            <div class="col-lg-6">

                                <div class="upload-option-card h-100">

                                    <div class="upload-option-icon">
                                        <i class="fa-solid fa-desktop"></i>
                                    </div>

                                    <h5 class="fw-bold">Upload From Computer</h5>

                                    <p class="text-muted small">
                                        Select a bill image or PDF already saved on this computer.
                                    </p>

                                    <input
                                        type="file"
                                        name="receipt"
                                        id="receipt"
                                        class="form-control"
                                        accept="image/*,.pdf"
                                    >

                                </div>

                            </div>

                            <div class="col-lg-6">

                                <div class="upload-option-card mobile-card h-100">

                                    <div class="upload-option-icon">
                                        <i class="fa-solid fa-mobile-screen-button"></i>
                                    </div>

                                    <h5 class="fw-bold">Take Picture From Mobile</h5>

                                    <p class="text-muted small">
                                        Generate a QR code, scan it from mobile and take the bill picture.
                                    </p>

                                    <button
                                        type="button"
                                        id="connectMobileButton"
                                        class="btn btn-primary w-100"
                                    >
                                        <i class="fa-solid fa-qrcode me-2"></i>
                                        Connect Mobile Camera
                                    </button>

                                </div>

                            </div>

                        </div>

                        <div
                            id="qrSection"
                            class="qr-section d-none"
                        >

                            <div class="row align-items-center g-4">

                                <div class="col-md-5 text-center">

                                    <div
                                        id="qrCode"
                                        class="qr-code-box"
                                    ></div>

                                </div>

                                <div class="col-md-7">

                                    <h5 class="fw-bold">
                                        Scan QR Code From Mobile
                                    </h5>

                                    <p class="text-muted mb-2">
                                        Open your mobile camera and scan this QR code.
                                    </p>

                                    <div
                                        id="waitingStatus"
                                        class="mobile-status waiting-status"
                                    >
                                        <span
                                            class="spinner-border spinner-border-sm me-2"
                                        ></span>

                                        Waiting for bill picture...
                                    </div>

                                    <div class="mt-3">
                                        <label class="form-label small fw-semibold">
                                            Mobile Link
                                        </label>

                                        <div class="input-group">
                                            <input
                                                type="text"
                                                id="mobileUrlInput"
                                                class="form-control"
                                                readonly
                                            >

                                            <button
                                                type="button"
                                                id="copyMobileUrlButton"
                                                class="btn btn-outline-secondary"
                                            >
                                                Copy
                                            </button>
                                        </div>
                                    </div>

                                    <button
                                        type="button"
                                        id="generateNewQrButton"
                                        class="btn btn-outline-primary btn-sm mt-3"
                                    >
                                        <i class="fa-solid fa-rotate me-1"></i>
                                        Generate New QR
                                    </button>

                                </div>

                            </div>

                        </div>

                        <div
                            id="receiptPreviewWrapper"
                            class="receipt-preview-wrapper d-none"
                        >

                            <div class="receipt-preview-header">

                                <div>
                                    <h5 class="fw-bold mb-1">
                                        Bill Received
                                    </h5>

                                    <small
                                        id="receiptFileName"
                                        class="text-muted"
                                    ></small>
                                </div>

                                <button
                                    type="button"
                                    id="removeReceiptButton"
                                    class="btn btn-sm btn-outline-danger"
                                >
                                    <i class="fa-solid fa-trash me-1"></i>
                                    Remove
                                </button>

                            </div>

                            <div
                                id="imagePreviewContainer"
                                class="d-none"
                            >
                                <img
                                    src=""
                                    alt="Bill Preview"
                                    id="receiptPreviewImage"
                                    class="receipt-preview-image"
                                >
                            </div>

                            <div
                                id="pdfPreviewContainer"
                                class="d-none"
                            >
                                <div class="pdf-preview">
                                    <i class="fa-solid fa-file-pdf"></i>

                                    <div>
                                        <h6 class="fw-bold mb-1">
                                            PDF Bill Received
                                        </h6>

                                        <span
                                            id="pdfFileName"
                                            class="text-muted"
                                        ></span>
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

                </div>

                <div class="col-md-12 mb-3">

                    <label class="form-label">Description</label>

                    <textarea
                        name="description"
                        rows="3"
                        class="form-control"
                        placeholder="Expense details"
                    >{{ old('description') }}</textarea>

                </div>

            </div>

            <div class="d-flex gap-2">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    <i class="fa-solid fa-floppy-disk me-1"></i>
                    Save Expense
                </button>

                <a
                    href="{{ route('expenses.index') }}"
                    class="btn btn-secondary"
                >
                    Cancel
                </a>

            </div>

        </form>

    </div>
</div>

<style>
    .receipt-section {
        padding: 18px;
        border: 1px solid #e2e6ea;
        border-radius: 14px;
        background: #fafbfc;
    }

    .upload-option-card {
        padding: 20px;
        border: 1px solid #e0e5eb;
        border-radius: 14px;
        background: #ffffff;
    }

    .upload-option-card.mobile-card {
        border-color: rgba(13, 110, 253, 0.25);
        background: rgba(13, 110, 253, 0.025);
    }

    .upload-option-icon {
        width: 48px;
        height: 48px;
        margin-bottom: 14px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 21px;
        color: #0d6efd;
        background: rgba(13, 110, 253, 0.1);
    }

    .qr-section {
        margin-top: 20px;
        padding: 22px;
        border: 1px solid #d9e4f5;
        border-radius: 14px;
        background: #ffffff;
    }

    .qr-code-box {
        display: inline-flex;
        min-width: 230px;
        min-height: 230px;
        padding: 14px;
        align-items: center;
        justify-content: center;
        border: 1px solid #e1e5e9;
        border-radius: 14px;
        background: #ffffff;
    }

    .qr-code-box img,
    .qr-code-box canvas {
        max-width: 100%;
        height: auto;
    }

    .mobile-status {
        padding: 13px 15px;
        border-radius: 10px;
        font-weight: 600;
    }

    .waiting-status {
        color: #664d03;
        border: 1px solid #ffecb5;
        background: #fff3cd;
    }

    .success-status {
        color: #0f5132;
        border: 1px solid #badbcc;
        background: #d1e7dd;
    }

    .error-status {
        color: #842029;
        border: 1px solid #f5c2c7;
        background: #f8d7da;
    }

    .receipt-preview-wrapper {
        margin-top: 20px;
        padding: 18px;
        border: 1px solid #badbcc;
        border-radius: 14px;
        background: #f4fff8;
    }

    .receipt-preview-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        margin-bottom: 15px;
    }

    .receipt-preview-image {
        display: block;
        width: 100%;
        max-width: 650px;
        max-height: 600px;
        margin: 0 auto;
        object-fit: contain;
        border-radius: 12px;
        border: 1px solid #dde3e8;
        background: #ffffff;
    }

    .pdf-preview {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 22px;
        border-radius: 12px;
        border: 1px solid #e1e5ea;
        background: #ffffff;
    }

    .pdf-preview i {
        font-size: 44px;
        color: #dc3545;
    }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>
const categories = @json($categories);

document.addEventListener('DOMContentLoaded', function () {
    const categorySelect = document.getElementById('expense_category_id');
    const subCategorySelect = document.getElementById('expense_sub_category_id');

    const connectMobileButton = document.getElementById('connectMobileButton');
    const generateNewQrButton = document.getElementById('generateNewQrButton');
    const copyMobileUrlButton = document.getElementById('copyMobileUrlButton');

    const qrSection = document.getElementById('qrSection');
    const qrCode = document.getElementById('qrCode');

    const waitingStatus = document.getElementById('waitingStatus');
    const mobileUrlInput = document.getElementById('mobileUrlInput');
    const mobileReceiptTokenInput = document.getElementById('mobile_receipt_token');

    const receiptInput = document.getElementById('receipt');

    const previewWrapper = document.getElementById('receiptPreviewWrapper');
    const imagePreviewContainer = document.getElementById('imagePreviewContainer');
    const pdfPreviewContainer = document.getElementById('pdfPreviewContainer');

    const receiptPreviewImage = document.getElementById('receiptPreviewImage');
    const receiptFileName = document.getElementById('receiptFileName');
    const pdfFileName = document.getElementById('pdfFileName');

    const removeReceiptButton = document.getElementById('removeReceiptButton');

    let uploadToken = null;
    let statusInterval = null;
    let localPreviewUrl = null;
    let receiptSource = null;

    function loadSubCategories() {
        const categoryId = categorySelect.value;
        const selectedSubCategoryId = subCategorySelect.dataset.selected;

        subCategorySelect.innerHTML =
            '<option value="">Select Sub Category</option>';

        const category = categories.find(function (item) {
            return String(item.id) === String(categoryId);
        });

        if (
            category &&
            Array.isArray(category.sub_categories)
        ) {
            category.sub_categories.forEach(function (subCategory) {
                const option = document.createElement('option');

                option.value = subCategory.id;
                option.textContent = subCategory.name;

                if (
                    selectedSubCategoryId &&
                    String(selectedSubCategoryId) === String(subCategory.id)
                ) {
                    option.selected = true;
                }

                subCategorySelect.appendChild(option);
            });
        }

        subCategorySelect.dataset.selected = '';
    }

    async function generateMobileSession() {
        stopPolling();

        connectMobileButton.disabled = true;
        connectMobileButton.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2"></span>
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

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(
                    result.message || 'Unable to create mobile session.'
                );
            }

            uploadToken = result.token;
            mobileReceiptTokenInput.value = uploadToken;
            mobileUrlInput.value = result.mobile_url;

            qrCode.innerHTML = '';

            new QRCode(qrCode, {
                text: result.mobile_url,
                width: 210,
                height: 210,
                correctLevel: QRCode.CorrectLevel.H
            });

            qrSection.classList.remove('d-none');

            waitingStatus.className =
                'mobile-status waiting-status';

            waitingStatus.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2"></span>
                Waiting for bill picture...
            `;

            startPolling();
        } catch (error) {
            alert(error.message);
        } finally {
            connectMobileButton.disabled = false;

            connectMobileButton.innerHTML = `
                <i class="fa-solid fa-qrcode me-2"></i>
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

        const statusUrl = `{{ url('/expense-receipt/status') }}/${uploadToken}`;

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

            if (result.status === 'uploaded' && result.file_url) {
                stopPolling();

                receiptSource = 'mobile';

                receiptInput.value = '';

                waitingStatus.className =
                    'mobile-status success-status';

                waitingStatus.innerHTML = `
                    <i class="fa-solid fa-circle-check me-2"></i>
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
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>
                    QR code expired. Generate a new QR code.
                `;
            }
        } catch (error) {
            console.error('Status checking failed:', error);
        }
    }

    function showRemotePreview(fileUrl, fileName, mimeType) {
        clearPreview();

        receiptFileName.textContent =
            fileName || 'Mobile bill picture';

        previewWrapper.classList.remove('d-none');

        if (mimeType === 'application/pdf') {
            pdfPreviewContainer.classList.remove('d-none');
            pdfFileName.textContent = fileName || 'Receipt.pdf';
            return;
        }

        imagePreviewContainer.classList.remove('d-none');

        receiptPreviewImage.src =
            fileUrl + '?time=' + Date.now();
    }

    function showLocalPreview(file) {
        clearPreview();

        receiptSource = 'computer';

        mobileReceiptTokenInput.value = '';

        receiptFileName.textContent = file.name;
        previewWrapper.classList.remove('d-none');

        if (file.type === 'application/pdf') {
            pdfPreviewContainer.classList.remove('d-none');
            pdfFileName.textContent = file.name;
            return;
        }

        localPreviewUrl = URL.createObjectURL(file);

        receiptPreviewImage.src = localPreviewUrl;
        imagePreviewContainer.classList.remove('d-none');
    }

    function clearPreview() {
        if (localPreviewUrl) {
            URL.revokeObjectURL(localPreviewUrl);
            localPreviewUrl = null;
        }

        receiptPreviewImage.src = '';
        receiptFileName.textContent = '';
        pdfFileName.textContent = '';

        imagePreviewContainer.classList.add('d-none');
        pdfPreviewContainer.classList.add('d-none');
    }

    async function removeCurrentReceipt() {
        receiptInput.value = '';
        clearPreview();

        previewWrapper.classList.add('d-none');

        if (receiptSource === 'mobile' && uploadToken) {
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

            uploadToken = null;
            mobileReceiptTokenInput.value = '';
            qrSection.classList.add('d-none');
            qrCode.innerHTML = '';

            stopPolling();
        }

        receiptSource = null;
    }

    categorySelect.addEventListener('change', loadSubCategories);

    connectMobileButton.addEventListener(
        'click',
        generateMobileSession
    );

    generateNewQrButton.addEventListener(
        'click',
        generateMobileSession
    );

    copyMobileUrlButton.addEventListener('click', async function () {
        if (!mobileUrlInput.value) {
            return;
        }

        try {
            await navigator.clipboard.writeText(
                mobileUrlInput.value
            );

            copyMobileUrlButton.textContent = 'Copied';

            setTimeout(function () {
                copyMobileUrlButton.textContent = 'Copy';
            }, 1500);
        } catch (error) {
            mobileUrlInput.select();
            document.execCommand('copy');
        }
    });

    receiptInput.addEventListener('change', function () {
        const file = this.files && this.files[0];

        if (!file) {
            return;
        }

        stopPolling();
        showLocalPreview(file);
    });

    removeReceiptButton.addEventListener(
        'click',
        removeCurrentReceipt
    );

    loadSubCategories();
});
</script>

@endsection
