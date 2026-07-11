@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Add Contractor Bill</h2>
        <p class="text-muted mb-0">
            Select contractor and add work items for the assigned machine.
        </p>
    </div>

    <a href="{{ route('contractor-bills.index') }}"
       class="btn btn-light border">
        Back
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger">
        <strong>Please fix the following errors:</strong>

        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body">

        <form action="{{ route('contractor-bills.store') }}"
              method="POST"
              id="billForm">

            @csrf

            <div class="row align-items-start">

                <div class="col-lg-4 mb-3">
                    <label class="form-label">Contractor *</label>

                    <select name="contractor_id"
                            id="contractorSelect"
                            class="form-select"
                            required>

                        <option value="">Select Contractor</option>

                        @foreach($contractors as $contractor)
                            @php
                                $photoUrl = $contractor->photo
                                    ? asset(
                                        'storage/' .
                                        ltrim($contractor->photo, '/')
                                    )
                                    : '';
                            @endphp

                            <option value="{{ $contractor->id }}"
                                    data-machine-id="{{ $contractor->contractor_machine_id }}"
                                    data-machine-name="{{ $contractor->machine?->name ?? '' }}"
                                    data-name="{{ $contractor->name }}"
                                    data-department="{{ $contractor->department?->name ?? 'No department' }}"
                                    data-phone="{{ $contractor->phone ?? '' }}"
                                    data-photo="{{ $photoUrl }}"
                                    {{ old('contractor_id') == $contractor->id
                                        ? 'selected'
                                        : '' }}>

                                {{ $contractor->name }}
                                — {{ $contractor->machine?->name }}
                            </option>
                        @endforeach
                    </select>

                    <small class="text-muted">
                        Only active contractors with assigned machines are shown.
                    </small>
                </div>

                <div class="col-lg-3 mb-3">
                    <label class="form-label">Assigned Machine</label>

                    <input type="text"
                           id="selectedMachineName"
                           class="form-control"
                           placeholder="Select contractor first"
                           readonly>
                </div>

                <div class="col-lg-3 mb-3">
                    <label class="form-label">Bill Date *</label>

                    <input type="date"
                           name="bill_date"
                           value="{{ old('bill_date', date('Y-m-d')) }}"
                           class="form-control"
                           required>
                </div>

                <div class="col-lg-2 mb-3">
                    <div id="contractorProfile"
                         class="contractor-profile-card d-none">

                        <div id="profilePhotoBox"
                             class="profile-photo-box">

                            <img id="profilePhoto"
                                 src=""
                                 alt="Contractor">

                            <div id="profileInitial"
                                 class="profile-initial">
                                C
                            </div>
                        </div>

                        <div class="profile-information">
                            <strong id="profileName">-</strong>
                            <small id="profileDepartment">-</small>
                            <small id="profileMachine">-</small>
                        </div>
                    </div>
                </div>

            </div>

            <hr>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Bill Items</h5>

                <button type="button"
                        id="addRow"
                        class="btn btn-outline-dark btn-sm">

                    <i class="bi bi-plus-lg me-1"></i>
                    Add Item
                </button>
            </div>

            <div id="machineMessage"
                 class="alert alert-info">

                Select a contractor first. Only items for the contractor machine will appear.
            </div>

            <div class="row g-2 mb-2 bill-item-headings">
                <div class="col-lg-2">
                    <label class="form-label fw-bold">Order No *</label>
                </div>

                <div class="col-lg-4">
                    <label class="form-label fw-bold">Item / Work *</label>
                </div>

                <div class="col-lg-1">
                    <label class="form-label fw-bold">Quantity *</label>
                </div>

                <div class="col-lg-2">
                    <label class="form-label fw-bold">Rate / Price</label>
                </div>

                <div class="col-lg-2">
                    <label class="form-label fw-bold">Total Amount</label>
                </div>

                <div class="col-lg-1">
                    <label class="form-label fw-bold">Action</label>
                </div>
            </div>

            <div id="itemsBox">

                <div class="row item-row g-2 mb-3">

                    <div class="col-lg-2">
                        <input type="text"
                               name="items[0][order_no]"
                               class="form-control order-no"
                               placeholder="Order No"
                               required>
                    </div>

                    <div class="col-lg-4">
                        <select name="items[0][contractor_item_id]"
                                class="form-select item-select"
                                required
                                disabled>

                            <option value="">
                                Select Contractor First
                            </option>

                            @foreach($items as $item)
                                <option value="{{ $item->id }}"
                                        data-machine-id="{{ $item->contractor_machine_id }}"
                                        data-rate="{{ $item->rate }}">

                                    {{ $item->name }}
                                    — {{ $item->unit }}
                                    — Rs {{ number_format($item->rate, 2) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-1">
                        <input type="number"
                               name="items[0][quantity]"
                               step="0.01"
                               min="0.01"
                               class="form-control qty"
                               placeholder="Qty"
                               required>
                    </div>

                    <div class="col-lg-2">
                        <input type="number"
                               step="0.01"
                               class="form-control rate"
                               placeholder="Rate"
                               readonly>
                    </div>

                    <div class="col-lg-2">
                        <input type="number"
                               step="0.01"
                               class="form-control total"
                               value="0.00"
                               readonly>
                    </div>

                    <div class="col-lg-1">
                        <button type="button"
                                class="btn btn-danger w-100 remove-row">

                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>

                </div>

            </div>

            <hr>

            <div class="row">

                <div class="col-lg-4 ms-auto mb-3">
                    <label class="form-label">Grand Total</label>

                    <div class="input-group">
                        <span class="input-group-text">Rs</span>

                        <input type="text"
                               id="grandTotal"
                               class="form-control fw-bold"
                               value="0.00"
                               readonly>
                    </div>
                </div>

                <div class="col-lg-4 mb-3">
                    <label class="form-label">Paid Amount</label>

                    <div class="input-group">
                        <span class="input-group-text">Rs</span>

                        <input type="number"
                               name="paid_amount"
                               id="paidAmount"
                               value="{{ old('paid_amount', 0) }}"
                               step="0.01"
                               min="0"
                               class="form-control">
                    </div>
                </div>

                <div class="col-lg-4 mb-3">
                    <label class="form-label">Remaining Balance</label>

                    <div class="input-group">
                        <span class="input-group-text">Rs</span>

                        <input type="text"
                               id="balanceAmount"
                               class="form-control fw-bold text-danger"
                               value="0.00"
                               readonly>
                    </div>
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label">Notes</label>

                    <textarea name="notes"
                              rows="3"
                              class="form-control">{{ old('notes') }}</textarea>
                </div>
            </div>

            <button type="submit"
                    class="btn btn-dark px-4">

                <i class="bi bi-check-circle me-1"></i>
                Save Bill
            </button>

        </form>
    </div>
</div>

<style>
.contractor-profile-card {
    display: flex;
    align-items: center;
    gap: 10px;
    min-height: 76px;
    padding: 10px;
    border: 1px solid #dce2ea;
    border-radius: 12px;
    background: #f8fafc;
}

.profile-photo-box {
    width: 58px;
    height: 58px;
    flex-shrink: 0;
    overflow: hidden;
    border-radius: 50%;
    background: #0d6efd;
}

.profile-photo-box img,
.profile-initial {
    width: 100%;
    height: 100%;
}

.profile-photo-box img {
    display: none;
    object-fit: cover;
}

.profile-initial {
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 22px;
    font-weight: 800;
}

.profile-information {
    min-width: 0;
}

.profile-information strong,
.profile-information small {
    display: block;
}

.profile-information strong {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.profile-information small {
    color: #6b7280;
}

@media (max-width: 991px) {
    .bill-item-headings {
        display: none;
    }

    .item-row > div {
        margin-bottom: 7px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const contractorSelect =
        document.getElementById('contractorSelect');

    const machineNameInput =
        document.getElementById('selectedMachineName');

    const contractorProfile =
        document.getElementById('contractorProfile');

    const profilePhoto =
        document.getElementById('profilePhoto');

    const profileInitial =
        document.getElementById('profileInitial');

    const profileName =
        document.getElementById('profileName');

    const profileDepartment =
        document.getElementById('profileDepartment');

    const profileMachine =
        document.getElementById('profileMachine');

    const itemsBox =
        document.getElementById('itemsBox');

    const grandTotalInput =
        document.getElementById('grandTotal');

    const paidAmountInput =
        document.getElementById('paidAmount');

    const balanceInput =
        document.getElementById('balanceAmount');

    const machineMessage =
        document.getElementById('machineMessage');

    let rowIndex = 1;

    function selectedOption() {
        return contractorSelect.options[
            contractorSelect.selectedIndex
        ];
    }

    function machineId() {
        return selectedOption()?.dataset.machineId || '';
    }

    function updateProfile() {
        const option = selectedOption();
        const selectedMachineId =
            option?.dataset.machineId || '';

        const name = option?.dataset.name || '';
        const department =
            option?.dataset.department || '';

        const machine =
            option?.dataset.machineName || '';

        const photo =
            option?.dataset.photo || '';

        machineNameInput.value = machine;

        if (!contractorSelect.value) {
            contractorProfile.classList.add('d-none');
        } else {
            contractorProfile.classList.remove('d-none');

            profileName.textContent = name;
            profileDepartment.textContent = department;
            profileMachine.textContent = machine;

            profileInitial.textContent =
                (name.charAt(0) || 'C').toUpperCase();

            if (photo) {
                profilePhoto.src = photo;
                profilePhoto.style.display = 'block';
                profileInitial.style.display = 'none';
            } else {
                profilePhoto.style.display = 'none';
                profileInitial.style.display = 'flex';
            }
        }

        machineMessage.style.display =
            selectedMachineId ? 'none' : 'block';

        document.querySelectorAll('.item-select')
            .forEach(function (select) {
                filterItems(select, selectedMachineId);
            });

        calculateTotals();
    }

    function filterItems(select, selectedMachineId) {
        select.value = '';

        Array.from(select.options)
            .forEach(function (option, index) {
                if (index === 0) {
                    option.hidden = false;

                    option.textContent =
                        selectedMachineId
                            ? 'Select Item'
                            : 'Select Contractor First';

                    return;
                }

                option.hidden =
                    String(option.dataset.machineId) !==
                    String(selectedMachineId);
            });

        select.disabled = !selectedMachineId;

        const row = select.closest('.item-row');

        row.querySelector('.qty').value = '';
        row.querySelector('.rate').value = '';
        row.querySelector('.total').value = '0.00';
    }

    function calculateTotals() {
        let grandTotal = 0;

        document.querySelectorAll('.item-row')
            .forEach(function (row) {
                const quantity =
                    parseFloat(
                        row.querySelector('.qty').value
                    ) || 0;

                const rate =
                    parseFloat(
                        row.querySelector('.rate').value
                    ) || 0;

                const total = quantity * rate;

                row.querySelector('.total').value =
                    total.toFixed(2);

                grandTotal += total;
            });

        grandTotalInput.value =
            grandTotal.toFixed(2);

        const paidAmount =
            parseFloat(paidAmountInput.value) || 0;

        balanceInput.value =
            Math.max(
                0,
                grandTotal - paidAmount
            ).toFixed(2);

        paidAmountInput.max =
            grandTotal.toFixed(2);
    }

    contractorSelect.addEventListener(
        'change',
        updateProfile
    );

    document.addEventListener(
        'change',
        function (event) {
            if (
                event.target.classList.contains(
                    'item-select'
                )
            ) {
                const option =
                    event.target.options[
                        event.target.selectedIndex
                    ];

                const row =
                    event.target.closest('.item-row');

                row.querySelector('.rate').value =
                    parseFloat(
                        option?.dataset.rate || 0
                    ).toFixed(2);

                calculateTotals();
            }
        }
    );

    document.addEventListener(
        'input',
        function (event) {
            if (
                event.target.classList.contains('qty') ||
                event.target.id === 'paidAmount'
            ) {
                calculateTotals();
            }
        }
    );

    document.getElementById('addRow')
        .addEventListener('click', function () {
            const firstRow =
                document.querySelector('.item-row');

            const newRow =
                firstRow.cloneNode(true);

            newRow.querySelectorAll('input, select')
                .forEach(function (input) {
                    input.value = '';

                    if (input.name) {
                        input.name =
                            input.name.replace(
                                /\[\d+\]/,
                                '[' + rowIndex + ']'
                            );
                    }
                });

            newRow.querySelector('.total').value =
                '0.00';

            itemsBox.appendChild(newRow);

            filterItems(
                newRow.querySelector('.item-select'),
                machineId()
            );

            rowIndex++;
        });

    document.addEventListener(
        'click',
        function (event) {
            if (event.target.closest('.remove-row')) {
                const rows =
                    document.querySelectorAll('.item-row');

                if (rows.length === 1) {
                    alert(
                        'At least one bill item is required.'
                    );

                    return;
                }

                event.target
                    .closest('.item-row')
                    .remove();

                calculateTotals();
            }
        }
    );

    updateProfile();
    calculateTotals();
});
</script>

@endsection
