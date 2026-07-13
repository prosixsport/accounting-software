@extends('layouts.app')

@section('content')

<style>
:root {
    --db-black: #050505;
    --db-dark: #111111;
    --db-purple: #2e2d4d;
    --db-border: #e9eaee;
    --db-muted: #6b7280;
    --db-bg: #f6f7f9;
    --db-white: #ffffff;
}

.dashboard-page {
    color: #111827;
}

/* ==============================
   HERO HEADER
============================== */
.dashboard-hero {
    position: relative;
    overflow: hidden;
    min-height: 210px;
    margin-bottom: 24px;
    padding: 28px 30px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
    border-radius: 25px;
    background:
        radial-gradient(
            circle at top right,
            rgba(255, 255, 255, 0.13),
            transparent 30%
        ),
        linear-gradient(
            135deg,
            #050505 0%,
            #111111 48%,
            #2e2d4d 100%
        );
    color: #ffffff;
    box-shadow: 0 20px 55px rgba(0, 0, 0, 0.2);
}

.dashboard-hero::before {
    content: "";
    position: absolute;
    top: -90px;
    right: -40px;
    width: 260px;
    height: 260px;
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 50%;
}

.dashboard-hero::after {
    content: "";
    position: absolute;
    right: 130px;
    bottom: -130px;
    width: 280px;
    height: 280px;
    border: 1px solid rgba(255, 255, 255, 0.06);
    border-radius: 50%;
}

.hero-content,
.hero-right {
    position: relative;
    z-index: 2;
}

.hero-brand {
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 18px;
}

.hero-brand-logo {
    height: 44px;
    width: auto;
    object-fit: contain;
}

.hero-brand-divider {
    width: 1px;
    height: 38px;
    background: rgba(255, 255, 255, 0.7);
}

.hero-brand-name {
    height: 29px;
    width: auto;
    max-width: 180px;
    object-fit: contain;
}

.hero-title {
    margin: 0;
    color: #ffffff;
    font-size: 34px;
    font-weight: 900;
    letter-spacing: -1px;
}

.hero-description {
    margin: 8px 0 0;
    color: rgba(255, 255, 255, 0.72);
    font-size: 14px;
}

.hero-user {
    color: #ffffff;
    font-weight: 800;
}

.hero-right {
    min-width: 240px;
    text-align: right;
}

.hero-date {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    padding: 11px 17px;
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 50px;
    background: rgba(255, 255, 255, 0.09);
    backdrop-filter: blur(8px);
    font-size: 13px;
    font-weight: 700;
}

.hero-time {
    margin-top: 14px;
    color: #ffffff;
    font-size: 30px;
    font-weight: 900;
    letter-spacing: -1px;
}

/* ==============================
   MONTHLY ALERT
============================== */
.monthly-alert-box {
    position: relative;
    overflow: hidden;
    margin-bottom: 24px;
    padding: 22px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    border-radius: 20px;
    background:
        linear-gradient(
            135deg,
            #b91c1c 0%,
            #dc2626 55%,
            #7f1d1d 100%
        );
    color: #ffffff;
    box-shadow: 0 14px 35px rgba(220, 38, 38, 0.24);
}

.monthly-alert-box::after {
    content: "";
    position: absolute;
    top: -70px;
    right: -50px;
    width: 190px;
    height: 190px;
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 50%;
}

.alert-left,
.monthly-alert-actions {
    position: relative;
    z-index: 2;
}

.alert-left {
    display: flex;
    align-items: center;
    gap: 16px;
}

.alert-icon {
    width: 54px;
    height: 54px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.14);
    font-size: 24px;
}

.monthly-alert-box h4 {
    margin: 0 0 6px;
    color: #ffffff;
    font-size: 19px;
    font-weight: 900;
}

.monthly-alert-box p {
    margin: 0;
    color: rgba(255, 255, 255, 0.8);
    font-size: 14px;
}

.monthly-alert-box .amount {
    color: #ffffff;
    font-weight: 900;
}

.monthly-alert-actions {
    display: flex;
    gap: 10px;
    flex-shrink: 0;
}

.monthly-alert-actions .btn {
    min-height: 42px;
    padding: 9px 15px;
    border-radius: 11px;
    font-size: 13px;
    font-weight: 800;
}

/* ==============================
   SECTION HEADER
============================== */
.dashboard-section-header {
    margin: 0 0 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.dashboard-section-title {
    margin: 0;
    color: #111827;
    font-size: 18px;
    font-weight: 900;
}

.dashboard-section-subtitle {
    margin-top: 3px;
    color: var(--db-muted);
    font-size: 13px;
}

/* ==============================
   STATS CARDS
============================== */
.stats-grid {
    margin-bottom: 24px;
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 17px;
}

.stat-card {
    position: relative;
    overflow: hidden;
    min-height: 173px;
    padding: 20px;
    border: 1px solid var(--db-border);
    border-radius: 21px;
    background: #ffffff;
    box-shadow: 0 4px 16px rgba(17, 24, 39, 0.045);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 15px 35px rgba(17, 24, 39, 0.1);
}

.stat-card.primary-card {
    border-color: #111111;
    background:
        radial-gradient(
            circle at top right,
            rgba(255, 255, 255, 0.11),
            transparent 34%
        ),
        linear-gradient(
            135deg,
            #050505,
            #171717 65%,
            #2e2d4d
        );
    color: #ffffff;
}

.stat-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
}

.stat-icon {
    width: 46px;
    height: 46px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 14px;
    background: #f3f4f6;
    color: #111111;
    font-size: 21px;
}

.primary-card .stat-icon {
    background: rgba(255, 255, 255, 0.11);
    color: #ffffff;
}

.stat-badge {
    padding: 6px 9px;
    border-radius: 30px;
    background: #f3f4f6;
    color: #6b7280;
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.primary-card .stat-badge {
    background: rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 0.78);
}

.stat-label {
    margin-top: 17px;
    color: #8b93a1;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.stat-value {
    margin-top: 5px;
    color: #111827;
    font-size: 27px;
    font-weight: 900;
    line-height: 1.2;
    word-break: break-word;
}

.primary-card .stat-value {
    color: #ffffff;
}

.stat-sub {
    margin-top: 5px;
    color: #9ca3af;
    font-size: 12px;
}

.primary-card .stat-sub {
    color: rgba(255, 255, 255, 0.58);
}

/* ==============================
   BOTTOM GRID
============================== */
.dashboard-bottom-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.45fr) minmax(300px, 0.55fr);
    gap: 20px;
}

.dashboard-panel {
    overflow: hidden;
    border: 1px solid var(--db-border);
    border-radius: 21px;
    background: #ffffff;
    box-shadow: 0 4px 16px rgba(17, 24, 39, 0.045);
}

.panel-header {
    padding: 18px 21px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    border-bottom: 1px solid #f0f1f3;
}

.panel-header h5 {
    margin: 0;
    color: #111827;
    font-size: 16px;
    font-weight: 900;
}

.panel-header span {
    color: #9ca3af;
    font-size: 12px;
}

.summary-list {
    padding: 6px 0;
}

.summary-row {
    padding: 14px 21px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    border-bottom: 1px solid #f6f6f7;
}

.summary-row:last-child {
    border-bottom: none;
}

.summary-left {
    display: flex;
    align-items: center;
    gap: 12px;
}

.summary-icon {
    width: 37px;
    height: 37px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 11px;
    background: #f4f4f5;
    color: #111111;
    font-size: 16px;
}

.summary-label {
    color: #374151;
    font-size: 13px;
    font-weight: 750;
}

.summary-amount {
    color: #111827;
    font-size: 13px;
    font-weight: 900;
    text-align: right;
}

/* ==============================
   QUICK ACTIONS
============================== */
.quick-actions {
    padding: 15px;
    display: grid;
    gap: 10px;
}

.quick-action {
    padding: 13px 14px;
    display: flex;
    align-items: center;
    gap: 12px;
    border: 1px solid #ebecf0;
    border-radius: 14px;
    background: #ffffff;
    color: #111827;
    text-decoration: none;
    transition: 0.2s;
}

.quick-action:hover {
    border-color: #111111;
    background: #111111;
    color: #ffffff;
    transform: translateX(3px);
}

.quick-action-icon {
    width: 39px;
    height: 39px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 11px;
    background: #f3f4f6;
    color: #111111;
    font-size: 17px;
    transition: 0.2s;
}

.quick-action:hover .quick-action-icon {
    background: rgba(255, 255, 255, 0.12);
    color: #ffffff;
}

.quick-action-content {
    min-width: 0;
    flex: 1;
}

.quick-action-title {
    font-size: 13px;
    font-weight: 850;
}

.quick-action-text {
    margin-top: 2px;
    color: #9ca3af;
    font-size: 11px;
}

.quick-action:hover .quick-action-text {
    color: rgba(255, 255, 255, 0.65);
}

.quick-action-arrow {
    font-size: 13px;
}

/* ==============================
   RESPONSIVE
============================== */
@media (max-width: 1200px) {
    .stats-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .dashboard-bottom-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .dashboard-hero {
        min-height: auto;
        padding: 24px 20px;
        display: block;
    }

    .hero-right {
        margin-top: 22px;
        min-width: 0;
        text-align: left;
    }

    .hero-title {
        font-size: 28px;
    }

    .hero-brand {
        gap: 13px;
    }

    .hero-brand-logo {
        height: 37px;
    }

    .hero-brand-divider {
        height: 32px;
    }

    .hero-brand-name {
        height: 25px;
        max-width: 145px;
    }

    .monthly-alert-box {
        display: block;
    }

    .monthly-alert-actions {
        margin-top: 17px;
        flex-wrap: wrap;
    }
}

@media (max-width: 576px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }

    .dashboard-hero {
        border-radius: 19px;
    }

    .hero-title {
        font-size: 25px;
    }

    .hero-brand-name {
        max-width: 125px;
    }

    .alert-left {
        align-items: flex-start;
    }

    .monthly-alert-box {
        padding: 19px;
        border-radius: 17px;
    }

    .monthly-alert-actions,
    .monthly-alert-actions form,
    .monthly-alert-actions .btn {
        width: 100%;
    }

    .summary-row {
        align-items: flex-start;
    }

    .summary-amount {
        max-width: 45%;
    }
}
</style>

<div class="dashboard-page">

    {{-- HERO HEADER --}}
    <div class="dashboard-hero">

        <div class="hero-content">

            <div class="hero-brand">
                <img
                    src="{{ asset('assets/images/P LOGO WHITE.png') }}"
                    class="hero-brand-logo"
                    alt="Prosix P Logo"
                >

                <span class="hero-brand-divider"></span>

                <img
                    src="{{ asset('assets/images/PROSIX SPORTS LOGO PNG WHITE.png') }}"
                    class="hero-brand-name"
                    alt="Prosix Sports Logo"
                >
            </div>

            <h1 class="hero-title">
                Accounts Dashboard
            </h1>

            <p class="hero-description">
                Welcome back,
                <span class="hero-user">{{ auth()->user()->name }}</span>.
                Here is your factory accounts overview.
            </p>

        </div>

        <div class="hero-right">

            <div class="hero-date">
                <i class="bi bi-calendar3"></i>
                {{ now()->format('l, d M Y') }}
            </div>

            <div class="hero-time" id="dashboardClock">
                {{ now()->format('h:i A') }}
            </div>

        </div>

    </div>

    {{-- MONTHLY ALERT --}}
    @if(!empty($monthlyAlert))
        <div
            class="monthly-alert-box"
            id="monthlyAlertBox"
            data-amount="{{ number_format($monthlyAlert->total_required, 2) }}"
        >

            <div class="alert-left">

                <div class="alert-icon">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>

                <div>
                    <h4>Monthly Salary Cycle Due</h4>

                    <p>
                        Please arrange
                        <span class="amount">
                            Rs {{ number_format($monthlyAlert->total_required, 2) }}
                        </span>
                        before salary distribution.
                    </p>
                </div>

            </div>

            <div class="monthly-alert-actions">

                <a
                    href="{{ route('monthly-alerts.show', $monthlyAlert->id) }}"
                    class="btn btn-light"
                >
                    <i class="bi bi-eye me-1"></i>
                    View Details
                </a>

                <form
                    action="{{ route('monthly-alerts.arranged', $monthlyAlert->id) }}"
                    method="POST"
                >
                    @csrf

                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i>
                        Funds Arranged
                    </button>
                </form>

            </div>

        </div>
    @endif

    {{-- OVERVIEW TITLE --}}
    <div class="dashboard-section-header">
        <div>
            <h3 class="dashboard-section-title">Financial Overview</h3>
            <div class="dashboard-section-subtitle">
                Important business totals and account statistics
            </div>
        </div>
    </div>

    {{-- FIRST STATS GRID --}}
    <div class="stats-grid">

        <div class="stat-card primary-card">

            <div class="stat-top">
                <div class="stat-icon">
                    <i class="bi bi-cash-stack"></i>
                </div>

                <span class="stat-badge">Revenue</span>
            </div>

            <div class="stat-label">Total Sales</div>

            <div class="stat-value">
                Rs {{ number_format($totalSales ?? 0, 2) }}
            </div>

            <div class="stat-sub">
                Total invoice sales amount
            </div>

        </div>

        <div class="stat-card">

            <div class="stat-top">
                <div class="stat-icon">
                    <i class="bi bi-wallet2"></i>
                </div>

                <span class="stat-badge">Expenses</span>
            </div>

            <div class="stat-label">Factory Expenses</div>

            <div class="stat-value text-danger">
                Rs {{ number_format($totalExpenses ?? 0, 2) }}
            </div>

            <div class="stat-sub">
                All recorded factory expenses
            </div>

        </div>

        <div class="stat-card">

            <div class="stat-top">
                <div class="stat-icon">
                    <i class="bi bi-people"></i>
                </div>

                <span class="stat-badge">Staff</span>
            </div>

            <div class="stat-label">Employees</div>

            <div class="stat-value">
                {{ number_format($totalEmployees ?? 0) }}
            </div>

            <div class="stat-sub">
                Registered workers and staff
            </div>

        </div>

        <div class="stat-card">

            <div class="stat-top">
                <div class="stat-icon">
                    <i class="bi bi-receipt"></i>
                </div>

                <span class="stat-badge">Billing</span>
            </div>

            <div class="stat-label">Invoices</div>

            <div class="stat-value">
                {{ number_format($totalInvoices ?? 0) }}
            </div>

            <div class="stat-sub">
                Total generated invoices
            </div>

        </div>

    </div>

    {{-- SECOND STATS GRID --}}
    <div class="stats-grid">

        <div class="stat-card">

            <div class="stat-top">
                <div class="stat-icon">
                    <i class="bi bi-credit-card"></i>
                </div>

                <span class="stat-badge">Received</span>
            </div>

            <div class="stat-label">Payments Received</div>

            <div class="stat-value text-primary">
                Rs {{ number_format($totalPayments ?? 0, 2) }}
            </div>

            <div class="stat-sub">
                Customer payments received
            </div>

        </div>

        <div class="stat-card">

            <div class="stat-top">
                <div class="stat-icon">
                    <i class="bi bi-person-lines-fill"></i>
                </div>

                <span class="stat-badge">Clients</span>
            </div>

            <div class="stat-label">Customers</div>

            <div class="stat-value">
                {{ number_format($totalCustomers ?? 0) }}
            </div>

            <div class="stat-sub">
                Total registered customers
            </div>

        </div>

        <div class="stat-card">

            <div class="stat-top">
                <div class="stat-icon">
                    <i class="bi bi-currency-exchange"></i>
                </div>

                <span class="stat-badge">Salary</span>
            </div>

            <div class="stat-label">Payroll</div>

            <div class="stat-value text-warning">
                Rs {{ number_format($totalPayroll ?? 0, 2) }}
            </div>

            <div class="stat-sub">
                Total employee salary expense
            </div>

        </div>

        <div class="stat-card">

            <div class="stat-top">
                <div class="stat-icon">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>

                <span class="stat-badge">Profit</span>
            </div>

            <div class="stat-label">Net Profit</div>

            <div class="stat-value {{ ($netProfit ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                Rs {{ number_format($netProfit ?? 0, 2) }}
            </div>

            <div class="stat-sub">
                Sales minus expenses and payroll
            </div>

        </div>

    </div>

    {{-- BOTTOM CONTENT --}}
    <div class="dashboard-bottom-grid">

        {{-- BUSINESS SUMMARY --}}
        <div class="dashboard-panel">

            <div class="panel-header">
                <div>
                    <h5>Business Summary</h5>
                    <span>Complete accounts overview</span>
                </div>

                <i class="bi bi-bar-chart-line fs-5 text-muted"></i>
            </div>

            <div class="summary-list">

                <div class="summary-row">

                    <div class="summary-left">
                        <div class="summary-icon">
                            <i class="bi bi-cash-stack"></i>
                        </div>

                        <span class="summary-label">Total Sales</span>
                    </div>

                    <span class="summary-amount">
                        Rs {{ number_format($totalSales ?? 0, 2) }}
                    </span>

                </div>

                <div class="summary-row">

                    <div class="summary-left">
                        <div class="summary-icon">
                            <i class="bi bi-wallet2"></i>
                        </div>

                        <span class="summary-label">Total Expenses</span>
                    </div>

                    <span class="summary-amount text-danger">
                        Rs {{ number_format($totalExpenses ?? 0, 2) }}
                    </span>

                </div>

                <div class="summary-row">

                    <div class="summary-left">
                        <div class="summary-icon">
                            <i class="bi bi-currency-exchange"></i>
                        </div>

                        <span class="summary-label">Total Payroll</span>
                    </div>

                    <span class="summary-amount text-warning">
                        Rs {{ number_format($totalPayroll ?? 0, 2) }}
                    </span>

                </div>

                <div class="summary-row">

                    <div class="summary-left">
                        <div class="summary-icon">
                            <i class="bi bi-hourglass-split"></i>
                        </div>

                        <span class="summary-label">Receivables</span>
                    </div>

                    <span class="summary-amount text-info">
                        Rs {{ number_format($pendingReceivables ?? 0, 2) }}
                    </span>

                </div>

                <div class="summary-row">

                    <div class="summary-left">
                        <div class="summary-icon">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>

                        <span class="summary-label">Net Profit</span>
                    </div>

                    <span class="summary-amount {{ ($netProfit ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                        Rs {{ number_format($netProfit ?? 0, 2) }}
                    </span>

                </div>

            </div>

        </div>

        {{-- QUICK ACTIONS --}}
        <div class="dashboard-panel">

            <div class="panel-header">
                <div>
                    <h5>Quick Actions</h5>
                    <span>Frequently used options</span>
                </div>

                <i class="bi bi-lightning-charge fs-5 text-muted"></i>
            </div>

            <div class="quick-actions">

                <a href="{{ route('employees.create') }}" class="quick-action">

                    <span class="quick-action-icon">
                        <i class="bi bi-person-plus"></i>
                    </span>

                    <span class="quick-action-content">
                        <span class="quick-action-title d-block">
                            Add Employee
                        </span>

                        <span class="quick-action-text d-block">
                            Register a new worker
                        </span>
                    </span>

                    <i class="bi bi-chevron-right quick-action-arrow"></i>

                </a>

                <a href="{{ route('attendances.create') }}" class="quick-action">

                    <span class="quick-action-icon">
                        <i class="bi bi-calendar-check"></i>
                    </span>

                    <span class="quick-action-content">
                        <span class="quick-action-title d-block">
                            Add Attendance
                        </span>

                        <span class="quick-action-text d-block">
                            Add manual attendance record
                        </span>
                    </span>

                    <i class="bi bi-chevron-right quick-action-arrow"></i>

                </a>

                <a href="{{ route('expenses.create') }}" class="quick-action">

                    <span class="quick-action-icon">
                        <i class="bi bi-wallet2"></i>
                    </span>

                    <span class="quick-action-content">
                        <span class="quick-action-title d-block">
                            Add Expense
                        </span>

                        <span class="quick-action-text d-block">
                            Record a factory expense
                        </span>
                    </span>

                    <i class="bi bi-chevron-right quick-action-arrow"></i>

                </a>

                <a href="{{ route('payrolls.index') }}" class="quick-action">

                    <span class="quick-action-icon">
                        <i class="bi bi-cash-coin"></i>
                    </span>

                    <span class="quick-action-content">
                        <span class="quick-action-title d-block">
                            Manage Payroll
                        </span>

                        <span class="quick-action-text d-block">
                            View and process salaries
                        </span>
                    </span>

                    <i class="bi bi-chevron-right quick-action-arrow"></i>

                </a>

            </div>

        </div>

    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const clockElement = document.getElementById('dashboardClock');

    function updateClock() {
        const now = new Date();

        clockElement.textContent = now.toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        });
    }

    updateClock();
    setInterval(updateClock, 1000);
});
</script>

@if(!empty($monthlyAlert))
<script>
document.addEventListener('DOMContentLoaded', function () {
    const alertBox = document.getElementById('monthlyAlertBox');

    if (!alertBox) {
        return;
    }

    const amount = alertBox.dataset.amount;

    function showMonthlyNotification() {
        new Notification('Accounts System', {
            body: 'Monthly Salary Alert: Please arrange Rs ' +
                amount +
                ' before salary distribution.',
            icon: '{{ asset("favicon.ico") }}'
        });
    }

    if ('Notification' in window) {
        if (Notification.permission === 'granted') {
            showMonthlyNotification();
        } else if (Notification.permission !== 'denied') {
            Notification.requestPermission().then(function (permission) {
                if (permission === 'granted') {
                    showMonthlyNotification();
                }
            });
        }
    }
});
</script>
@endif

@endsection
