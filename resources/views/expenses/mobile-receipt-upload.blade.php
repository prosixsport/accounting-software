<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0"
    >

    <title>Upload Expense Bill</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <style>
        body {
            min-height: 100vh;
            background: #f4f6f9;
            padding: 20px;
        }

        .upload-card {
            width: 100%;
            max-width: 520px;
            margin: 20px auto;
            border: 0;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.08);
        }

        .upload-header {
            padding: 28px 20px;
            color: #ffffff;
            text-align: center;
            background: linear-gradient(135deg, #0d6efd, #084298);
        }

        .upload-header-icon {
            width: 72px;
            height: 72px;
            margin: 0 auto 14px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            background: rgba(255, 255, 255, 0.18);
        }

        .upload-body {
            padding: 24px;
            background: #ffffff;
        }

        .camera-box {
            padding: 24px 16px;
            border: 2px dashed #d5dce5;
            border-radius: 16px;
            text-align: center;
            background: #fafbfc;
        }

        .camera-icon {
            font-size: 48px;
            color: #0d6efd;
            margin-bottom: 12px;
        }

        .preview-image {
            width: 100%;
            max-height: 420px;
            object-fit: contain;
            border-radius: 14px;
            border: 1px solid #e1e5ea;
            background: #f7f7f7;
        }

        .status-box {
            display: none;
            margin-top: 16px;
            padding: 14px;
            border-radius: 12px;
        }

        .status-success {
            color: #0f5132;
            background: #d1e7dd;
            border: 1px solid #badbcc;
        }

        .status-error {
            color: #842029;
            background: #f8d7da;
            border: 1px solid #f5c2c7;
        }

        .upload-btn {
            min-height: 52px;
            font-weight: 700;
            border-radius: 12px;
        }

        .change-btn {
            border-radius: 10px;
        }
    </style>
</head>

<body>

<div class="card upload-card">

    <div class="upload-header">
        <div class="upload-header-icon">
            <i class="fa-solid fa-receipt"></i>
        </div>

        <h3 class="fw-bold mb-1">Expense Bill</h3>

        <p class="mb-0 opacity-75">
            Take a clear picture of the bill
        </p>
    </div>

    <div class="upload-body">

        <form
            id="mobileReceiptForm"
            action="{{ route('expense.receipt.mobile.upload', $upload->token) }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf

            <div id="cameraBox" class="camera-box">

                <i class="fa-solid fa-camera camera-icon"></i>

                <h5 class="fw-bold">Take Bill Picture</h5>

                <p class="text-muted small">
                    Make sure the complete bill is visible and text is clear.
                </p>

                <input
                    type="file"
                    name="receipt"
                    id="receiptInput"
                    class="d-none"
                    accept="image/*"
                    capture="environment"
                    required
                >

                <button
                    type="button"
                    id="openCameraButton"
                    class="btn btn-primary upload-btn w-100"
                >
                    <i class="fa-solid fa-camera me-2"></i>
                    Open Camera
                </button>

                <button
                    type="button"
                    id="openGalleryButton"
                    class="btn btn-outline-secondary mt-2 w-100 change-btn"
                >
                    <i class="fa-solid fa-images me-2"></i>
                    Select From Gallery
                </button>
            </div>

            <div id="previewBox" class="d-none">

                <img
                    src=""
                    alt="Bill Preview"
                    id="previewImage"
                    class="preview-image"
                >

                <div class="d-flex gap-2 mt-3">

                    <button
                        type="button"
                        id="changePictureButton"
                        class="btn btn-outline-secondary flex-grow-1 change-btn"
                    >
                        <i class="fa-solid fa-rotate me-1"></i>
                        Change
                    </button>

                    <button
                        type="submit"
                        id="uploadButton"
                        class="btn btn-success flex-grow-1 upload-btn"
                    >
                        <i class="fa-solid fa-paper-plane me-1"></i>
                        Send to PC
                    </button>
                </div>
            </div>

            <div id="uploadStatus" class="status-box"></div>

        </form>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('mobileReceiptForm');

    const receiptInput = document.getElementById('receiptInput');
    const openCameraButton = document.getElementById('openCameraButton');
    const openGalleryButton = document.getElementById('openGalleryButton');
    const changePictureButton = document.getElementById('changePictureButton');

    const cameraBox = document.getElementById('cameraBox');
    const previewBox = document.getElementById('previewBox');
    const previewImage = document.getElementById('previewImage');

    const uploadButton = document.getElementById('uploadButton');
    const uploadStatus = document.getElementById('uploadStatus');

    let previewUrl = null;

    openCameraButton.addEventListener('click', function () {
        receiptInput.setAttribute('capture', 'environment');
        receiptInput.click();
    });

    openGalleryButton.addEventListener('click', function () {
        receiptInput.removeAttribute('capture');
        receiptInput.click();
    });

    changePictureButton.addEventListener('click', function () {
        receiptInput.value = '';
        receiptInput.setAttribute('capture', 'environment');
        receiptInput.click();
    });

    receiptInput.addEventListener('change', function () {
        const file = this.files && this.files[0];

        if (!file) {
            return;
        }

        if (!file.type.startsWith('image/')) {
            showError('Please select an image.');
            return;
        }

        if (previewUrl) {
            URL.revokeObjectURL(previewUrl);
        }

        previewUrl = URL.createObjectURL(file);
        previewImage.src = previewUrl;

        cameraBox.classList.add('d-none');
        previewBox.classList.remove('d-none');

        hideStatus();
    });

    form.addEventListener('submit', async function (event) {
        event.preventDefault();

        const file = receiptInput.files && receiptInput.files[0];

        if (!file) {
            showError('Please take a picture first.');
            return;
        }

        const formData = new FormData(form);

        uploadButton.disabled = true;
        uploadButton.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2"></span>
            Sending...
        `;

        hideStatus();

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (!response.ok) {
                let errorMessage = result.message || 'Upload failed.';

                if (result.errors && result.errors.receipt) {
                    errorMessage = result.errors.receipt[0];
                }

                throw new Error(errorMessage);
            }

            previewBox.classList.add('d-none');

            uploadStatus.style.display = 'block';
            uploadStatus.className = 'status-box status-success text-center';

            uploadStatus.innerHTML = `
                <div class="fs-1 mb-2">
                    <i class="fa-solid fa-circle-check"></i>
                </div>

                <h5 class="fw-bold">Bill Sent Successfully</h5>

                <p class="mb-0">
                    The bill picture is now visible on the computer.
                    You may close this page.
                </p>
            `;
        } catch (error) {
            showError(error.message);
        } finally {
            uploadButton.disabled = false;
            uploadButton.innerHTML = `
                <i class="fa-solid fa-paper-plane me-1"></i>
                Send to PC
            `;
        }
    });

    function showError(message) {
        uploadStatus.style.display = 'block';
        uploadStatus.className = 'status-box status-error';
        uploadStatus.textContent = message;
    }

    function hideStatus() {
        uploadStatus.style.display = 'none';
        uploadStatus.textContent = '';
    }
});
</script>

</body>
</html>
