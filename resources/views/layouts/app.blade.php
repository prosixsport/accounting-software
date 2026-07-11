<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token"
          content="{{ csrf_token() }}">

    <title>
        @yield('title', 'Accounts System')
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    @stack('styles')
</head>

<body>

@php
    $user = auth()->user();

    $can = function ($key) use ($user) {
        return $user && $user->hasPermission($key);
    };  

    $expenseMenuOpen =
        request()->routeIs('expenses.*')
        || request()->routeIs('expense-categories.*')
        || request()->routeIs('expense-sub-categories.*');

    $contractorMenuOpen =
        request()->routeIs('contractors.*')
        || request()->routeIs('contractor-items.*')
        || request()->routeIs('contractor-bills.*')
        || request()->routeIs('contractor-departments.*')
        || request()->routeIs('contractor-machines.*');

    $biometricMenuOpen =
        request()->routeIs('biometric.*')
        || request()->routeIs('salary-verifications.*');

    $monthlyMenuOpen =
        request()->routeIs('monthly-alerts.*')
        || request()->routeIs('monthly-alert-schedules.*');

        $salaryWorkerMenuOpen =
    request()->routeIs('employees.*')
    || request()->routeIs('payrolls.*');
@endphp

<div class="app-layout">

    {{-- Mobile Header --}}
    <header class="mobile-header">
        <button type="button"
                class="mobile-menu-button"
                id="openSidebarButton">
            <i class="bi bi-list"></i>
        </button>

        <div>
            <div class="mobile-title">
                Accounts System
            </div>

            <small class="mobile-user">
                {{ $user?->name }}
            </small>
        </div>
    </header>

    {{-- Mobile Overlay --}}
    <div class="sidebar-overlay"
         id="sidebarOverlay"></div>

    {{-- Sidebar --}}
    <aside id="sidebar">

        <div class="sidebar-header">

            <div class="sidebar-title-row">
                <h4>Accounts System</h4>

                <button type="button"
                        class="sidebar-close-button"
                        id="closeSidebarButton">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="user-box">
                <div class="user-icon">
                    {{ strtoupper(
                        substr($user?->name ?? 'U', 0, 1)
                    ) }}
                </div>

                <div class="user-details">
                    <div class="user-name">
                        {{ $user?->name ?? 'User' }}
                    </div>

                    <small>
                        {{ ucfirst(
                            str_replace(
                                '_',
                                ' ',
                                $user?->role ?? ''
                            )
                        ) }}
                    </small>
                </div>
            </div>
        </div>

        <nav class="sidebar-nav">

            @if($can('dashboard'))
                <a href="{{ route('dashboard') }}"
                   class="side-link
                   {{ request()->routeIs('dashboard')
                        ? 'active'
                        : '' }}">

                    <i class="bi bi-speedometer2"></i>

                    <span>Dashboard</span>
                </a>
            @endif

            {{-- Monthly Alerts --}}
            @if(
                $can('monthly_alerts')
                || $can('monthly_alert_schedules')
            )
                <div class="side-group">

                    <button class="side-link side-toggle
                            {{ $monthlyMenuOpen
                                ? 'active'
                                : '' }}"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#monthlyMenu"
                            aria-expanded="{{ $monthlyMenuOpen
                                ? 'true'
                                : 'false' }}"
                            aria-controls="monthlyMenu">

                        <i class="bi bi-bell-fill"></i>

                        <span>Monthly Alerts</span>

                        <i class="bi bi-chevron-right arrow"></i>
                    </button>

                    <div id="monthlyMenu"
                         class="collapse
                         {{ $monthlyMenuOpen
                            ? 'show'
                            : '' }}">

                        @if($can('monthly_alerts'))
                            <a href="{{ route(
                                    'monthly-alerts.index'
                                ) }}"
                               class="sub-link
                               {{ request()->routeIs(
                                    'monthly-alerts.*'
                               ) ? 'active' : '' }}">

                                Alerts
                            </a>
                        @endif

                        @if($can('monthly_alert_schedules'))
                            <a href="{{ route(
                                    'monthly-alert-schedules.index'
                                ) }}"
                               class="sub-link
                               {{ request()->routeIs(
                                    'monthly-alert-schedules.*'
                               ) ? 'active' : '' }}">

                                Alert Schedule
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Access Management --}}
            @if($user && $user->role === 'super_admin')
                <a href="{{ route('user-access.index') }}"
                   class="side-link
                   {{ request()->routeIs('user-access.*')
                        ? 'active'
                        : '' }}">

                    <i class="bi bi-shield-lock"></i>

                    <span>Access Management</span>
                </a>
            @endif

            {{-- Accounts --}}
            @if($can('accounts'))
                <a href="{{ route('accounts.index') }}"
                   class="side-link
                   {{ request()->routeIs('accounts.*')
                        ? 'active'
                        : '' }}">

                    <i class="bi bi-journal-text"></i>

                    <span>Chart of Accounts</span>
                </a>
            @endif



            {{-- Attendance --}}
            @if($can('attendances'))
                <a href="{{ route('attendances.index') }}"
                   class="side-link
                   {{ request()->routeIs('attendances.*')
                        ? 'active'
                        : '' }}">

                    <i class="bi bi-calendar-check"></i>

                    <span>Attendance</span>
                </a>
            @endif


            {{-- Biometric --}}
            @if(
                $can('biometric')
                || $can('salary_verifications')
            )
                <div class="side-group">

                    <button class="side-link side-toggle
                            {{ $biometricMenuOpen
                                ? 'active'
                                : '' }}"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#biometricMenu"
                            aria-expanded="{{ $biometricMenuOpen
                                ? 'true'
                                : 'false' }}"
                            aria-controls="biometricMenu">

                        <i class="bi bi-fingerprint"></i>

                        <span>Biometric</span>

                        <i class="bi bi-chevron-right arrow"></i>
                    </button>

                    <div id="biometricMenu"
                         class="collapse
                         {{ $biometricMenuOpen
                            ? 'show'
                            : '' }}">

                        @if($can('biometric'))
                            <a href="{{ route(
                                    'biometric.index'
                                ) }}"
                               class="sub-link
                               {{ request()->routeIs(
                                    'biometric.*'
                               ) ? 'active' : '' }}">

                                Register Finger
                            </a>
                        @endif

                        @if($can('salary_verifications'))
                            <a href="{{ route(
                                    'salary-verifications.index'
                                ) }}"
                               class="sub-link
                               {{ request()->routeIs(
                                    'salary-verifications.*'
                               ) ? 'active' : '' }}">

                                Salary Verification
                            </a>
                        @endif
                    </div>
                </div>
            @endif
{{-- Salary Workers --}}
@if(
    $can('employees')
    || $can('payrolls')
)
    <div class="side-group">

        <button class="side-link side-toggle
                {{ $salaryWorkerMenuOpen
                    ? 'active'
                    : '' }}"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#salaryWorkerMenu"
                aria-expanded="{{ $salaryWorkerMenuOpen
                    ? 'true'
                    : 'false' }}"
                aria-controls="salaryWorkerMenu">

            <i class="bi bi-people-fill"></i>

            <span>Salary Workers</span>

            <i class="bi bi-chevron-right arrow"></i>
        </button>

        <div id="salaryWorkerMenu"
             class="collapse
             {{ $salaryWorkerMenuOpen
                ? 'show'
                : '' }}">

            @if($can('employees'))
                <a href="{{ route('employees.index') }}"
                   class="sub-link
                   {{ request()->routeIs('employees.*')
                        ? 'active'
                        : '' }}">

                    Employees
                </a>
            @endif

            @if($can('payrolls'))
                <a href="{{ route('payrolls.index') }}"
                   class="sub-link
                   {{ request()->routeIs('payrolls.*')
                        ? 'active'
                        : '' }}">

                    Payroll / Salary
                </a>
            @endif

        </div>
    </div>
@endif
            {{-- Contract Work --}}
            @if(
                $can('contractors')
                || $can('contractor_items')
                || $can('contractor_bills')
            )
                <div class="side-group">

                    <button class="side-link side-toggle
                            {{ $contractorMenuOpen
                                ? 'active'
                                : '' }}"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#contractorMenu"
                            aria-expanded="{{ $contractorMenuOpen
                                ? 'true'
                                : 'false' }}"
                            aria-controls="contractorMenu">

                        <i class="bi bi-person-workspace"></i>

                        <span>Contract Work</span>

                        <i class="bi bi-chevron-right arrow"></i>
                    </button>

                    <div id="contractorMenu"
                         class="collapse
                         {{ $contractorMenuOpen
                            ? 'show'
                            : '' }}">

                        @if($can('contractors'))
                            <a href="{{ route(
                                    'contractors.index'
                                ) }}"
                               class="sub-link
                               {{ request()->routeIs(
                                    'contractors.*'
                               ) ? 'active' : '' }}">

                               Add Contractors
                            </a>
                        @endif

                        @if($can('contractor_items'))
                            <a href="{{ route(
                                    'contractor-items.index'
                                ) }}"
                               class="sub-link
                               {{ request()->routeIs(
                                    'contractor-items.*'
                               ) ? 'active' : '' }}">

                                Add Items
                            </a>
                        @endif

                        @if($can('contractor_bills'))
                            <a href="{{ route(
                                    'contractor-bills.index'
                                ) }}"
                               class="sub-link
                               {{ request()->routeIs(
                                    'contractor-bills.*'
                               ) ? 'active' : '' }}">

                                Contractor Bills
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Expenses --}}
            @if(
                $can('expense_categories')
                || $can('expense_sub_categories')
                || $can('expenses')
            )
                <div class="side-group">

                    <button class="side-link side-toggle
                            {{ $expenseMenuOpen
                                ? 'active'
                                : '' }}"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#expenseMenu"
                            aria-expanded="{{ $expenseMenuOpen
                                ? 'true'
                                : 'false' }}"
                            aria-controls="expenseMenu">

                        <i class="bi bi-wallet2"></i>

                        <span>Factory Expenses</span>

                        <i class="bi bi-chevron-right arrow"></i>
                    </button>

                    <div id="expenseMenu"
                         class="collapse
                         {{ $expenseMenuOpen
                            ? 'show'
                            : '' }}">

                        @if($can('expense_categories'))
                            <a href="{{ route(
                                    'expense-categories.index'
                                ) }}"
                               class="sub-link
                               {{ request()->routeIs(
                                    'expense-categories.*'
                               ) ? 'active' : '' }}">

                                Expense Categories
                            </a>
                        @endif

                        @if($can('expense_sub_categories'))
                            <a href="{{ route(
                                    'expense-sub-categories.index'
                                ) }}"
                               class="sub-link
                               {{ request()->routeIs(
                                    'expense-sub-categories.*'
                               ) ? 'active' : '' }}">

                                Expense Sub Categories
                            </a>
                        @endif

                        @if($can('expenses'))
                            <a href="{{ route(
                                    'expenses.index'
                                ) }}"
                               class="sub-link
                               {{ request()->routeIs(
                                    'expenses.*'
                               ) ? 'active' : '' }}">

                                Expenses
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Customers --}}
            @if($can('customers'))
                <a href="{{ route('customers.index') }}"
                   class="side-link
                   {{ request()->routeIs('customers.*')
                        ? 'active'
                        : '' }}">

                    <i class="bi bi-person-lines-fill"></i>

                    <span>Customers</span>
                </a>
            @endif

            {{-- Invoices --}}
            @if($can('invoices'))
                <a href="{{ route('invoices.index') }}"
                   class="side-link
                   {{ request()->routeIs('invoices.*')
                        ? 'active'
                        : '' }}">

                    <i class="bi bi-receipt"></i>

                    <span>Invoices</span>
                </a>
            @endif

            {{-- Payments --}}
            @if($can('payments'))
                <a href="{{ route('payments.index') }}"
                   class="side-link
                   {{ request()->routeIs('payments.*')
                        ? 'active'
                        : '' }}">

                    <i class="bi bi-credit-card"></i>

                    <span>Payments Received</span>
                </a>
            @endif

            {{-- Customer Ledger --}}
            @if($can('customer_ledgers'))
                <a href="{{ route(
                        'customer-ledgers.index'
                    ) }}"
                   class="side-link
                   {{ request()->routeIs(
                        'customer-ledgers.*'
                   ) ? 'active' : '' }}">

                    <i class="bi bi-book"></i>

                    <span>Customer Ledger</span>
                </a>
            @endif

            {{-- Reports --}}
            @if($can('reports'))
                <a href="{{ route(
                        'reports.profit-loss'
                    ) }}"
                   class="side-link
                   {{ request()->routeIs(
                        'reports.profit-loss'
                   ) ? 'active' : '' }}">

                    <i class="bi bi-graph-up-arrow"></i>

                    <span>Profit & Loss</span>
                </a>
            @endif

        </nav>

        <form method="POST"
              action="{{ url('/logout') }}"
              class="logout-form">

            @csrf

            <button type="submit"
                    class="btn btn-danger w-100">

                <i class="bi bi-box-arrow-right me-1"></i>

                Logout
            </button>
        </form>
    </aside>

    {{-- Main Content --}}
    <main class="main-content">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show"
                 role="alert">

                {{ session('success') }}

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert">
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show"
                 role="alert">

                {{ session('error') }}

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert">
                </button>
            </div>
        @endif

        @yield('content')
    </main>
</div>

<style>
    :root {
        --sidebar-width: 270px;
        --sidebar-bg: #111820;
        --sidebar-panel: #18222e;
        --sidebar-hover: #1b2530;
        --sidebar-border: #223044;
        --sidebar-text: #d8dee7;
        --sidebar-muted: #8b98a8;
        --page-bg: #f5f7fb;
    }

    * {
        box-sizing: border-box;
    }

    html,
    body {
        min-height: 100%;
    }

    body {
        margin: 0;
        background: var(--page-bg);
        color: #172033;
        overflow-x: hidden;
    }

    .app-layout {
        display: flex;
        min-height: 100vh;
    }

    #sidebar {
        width: var(--sidebar-width);
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1050;
        display: flex;
        flex-direction: column;
        padding: 18px 14px;
        color: #ffffff;
        background: var(--sidebar-bg);
        border-right: 1px solid rgba(255, 255, 255, 0.04);
        transition: transform 0.25s ease;
    }

    .sidebar-header {
        flex-shrink: 0;
    }

    .sidebar-title-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .sidebar-header h4 {
        margin: 0 0 20px;
        font-size: 22px;
        font-weight: 800;
        line-height: 1.1;
    }

    .sidebar-close-button {
        display: none;
        width: 34px;
        height: 34px;
        margin-bottom: 18px;
        border: 0;
        border-radius: 9px;
        color: #ffffff;
        background: var(--sidebar-hover);
        align-items: center;
        justify-content: center;
    }

    .user-box {
        display: flex;
        align-items: center;
        gap: 11px;
        margin-bottom: 18px;
        padding: 12px;
        border-radius: 14px;
        background: var(--sidebar-panel);
    }

    .user-icon {
        width: 40px;
        height: 40px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: #ffffff;
        color: var(--sidebar-bg);
        font-size: 16px;
        font-weight: 800;
    }

    .user-details {
        min-width: 0;
    }

    .user-name {
        max-width: 170px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-size: 15px;
        font-weight: 700;
    }

    .user-box small {
        color: var(--sidebar-muted);
        font-size: 12px;
    }

    .sidebar-nav {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        display: flex;
        flex-direction: column;
        gap: 4px;
        padding-right: 4px;
        padding-bottom: 14px;
    }

    .side-group {
        width: 100%;
    }

    .side-link,
    .side-toggle {
        width: 100%;
        min-height: 44px;
        display: flex;
        align-items: center;
        gap: 11px;
        padding: 11px 12px;
        border: 0;
        border-radius: 10px;
        color: var(--sidebar-text);
        background: transparent;
        text-decoration: none;
        text-align: left;
        font-size: 14px;
        transition:
            background 0.2s ease,
            color 0.2s ease,
            transform 0.2s ease;
    }

    .side-link > i:first-child,
    .side-toggle > i:first-child {
        min-width: 18px;
        font-size: 17px;
    }

    .side-link:hover,
    .side-toggle:hover {
        color: #ffffff;
        background: var(--sidebar-hover);
    }

    .side-link.active,
    .side-toggle.active {
        color: var(--sidebar-bg);
        background: #ffffff;
        font-weight: 700;
        box-shadow: 0 8px 20px
            rgba(255, 255, 255, 0.08);
    }

    .side-toggle .arrow {
        margin-left: auto;
        font-size: 13px;
        transition: transform 0.25s ease;
    }

    .side-toggle[aria-expanded="true"] .arrow {
        transform: rotate(90deg);
    }

    .sub-link {
        position: relative;
        display: block;
        margin: 3px 0;
        padding: 9px 12px 9px 42px;
        border-radius: 8px;
        color: #b8c1cc;
        text-decoration: none;
        font-size: 13.5px;
        transition: 0.2s ease;
    }

    .sub-link:hover {
        color: #ffffff;
        background: var(--sidebar-hover);
    }

    .sub-link.active {
        color: #ffffff;
        background: var(--sidebar-hover);
        font-weight: 700;
    }

    .sub-link.active::before {
        content: "";
        position: absolute;
        top: 9px;
        left: 25px;
        width: 4px;
        height: 20px;
        border-radius: 10px;
        background: #ffffff;
    }

    .logout-form {
        flex-shrink: 0;
        padding-top: 12px;
        padding-bottom: 4px;
        border-top: 1px solid var(--sidebar-border);
    }

    .main-content {
        width: calc(100% - var(--sidebar-width));
        min-height: 100vh;
        margin-left: var(--sidebar-width);
        padding: 28px;
    }

    .mobile-header {
        display: none;
    }

    .sidebar-overlay {
        display: none;
    }

    .sidebar-nav::-webkit-scrollbar {
        width: 5px;
    }

    .sidebar-nav::-webkit-scrollbar-track {
        background: transparent;
    }

    .sidebar-nav::-webkit-scrollbar-thumb {
        border-radius: 10px;
        background: #334155;
    }

    @media (max-width: 991.98px) {
        body.sidebar-open {
            overflow: hidden;
        }

        .app-layout {
            display: block;
            padding-top: 68px;
        }

        #sidebar {
            transform: translateX(-100%);
            box-shadow: 15px 0 40px
                rgba(15, 23, 42, 0.25);
        }

        body.sidebar-open #sidebar {
            transform: translateX(0);
        }

        .sidebar-close-button {
            display: flex;
        }

        .mobile-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            height: 68px;
            display: flex;
            align-items: center;
            gap: 13px;
            padding: 10px 16px;
            color: #ffffff;
            background: var(--sidebar-bg);
            box-shadow: 0 5px 20px
                rgba(15, 23, 42, 0.18);
        }

        .mobile-menu-button {
            width: 42px;
            height: 42px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: 11px;
            color: #ffffff;
            background: var(--sidebar-panel);
            font-size: 24px;
        }

        .mobile-title {
            font-size: 16px;
            font-weight: 800;
        }

        .mobile-user {
            display: block;
            max-width: 230px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            color: #9ba8b8;
        }

        .sidebar-overlay {
            position: fixed;
            inset: 0;
            z-index: 1040;
            background: rgba(15, 23, 42, 0.58);
            backdrop-filter: blur(2px);
        }

        body.sidebar-open .sidebar-overlay {
            display: block;
        }

        .main-content {
            width: 100%;
            margin-left: 0;
            padding: 22px 16px;
        }
    }

    @media (max-width: 575.98px) {
        #sidebar {
            width: min(86vw, 300px);
        }

        .main-content {
            padding: 18px 12px;
        }

        .card-body {
            padding: 16px;
        }

        .mobile-user {
            max-width: 180px;
        }
    }
</style>

@stack('scripts')

<script>
    document.addEventListener(
        'DOMContentLoaded',
        function () {
            const body = document.body;

            const openSidebarButton =
                document.getElementById(
                    'openSidebarButton'
                );

            const closeSidebarButton =
                document.getElementById(
                    'closeSidebarButton'
                );

            const sidebarOverlay =
                document.getElementById(
                    'sidebarOverlay'
                );

            function openSidebar() {
                body.classList.add('sidebar-open');
            }

            function closeSidebar() {
                body.classList.remove('sidebar-open');
            }

            if (openSidebarButton) {
                openSidebarButton.addEventListener(
                    'click',
                    openSidebar
                );
            }

            if (closeSidebarButton) {
                closeSidebarButton.addEventListener(
                    'click',
                    closeSidebar
                );
            }

            if (sidebarOverlay) {
                sidebarOverlay.addEventListener(
                    'click',
                    closeSidebar
                );
            }

            document
                .querySelectorAll(
                    '#sidebar a.side-link, #sidebar a.sub-link'
                )
                .forEach(function (link) {
                    link.addEventListener(
                        'click',
                        function () {
                            if (
                                window.innerWidth <= 991
                            ) {
                                closeSidebar();
                            }
                        }
                    );
                });

            window.addEventListener(
                'resize',
                function () {
                    if (window.innerWidth > 991) {
                        closeSidebar();
                    }
                }
            );

            document.addEventListener(
                'keydown',
                function (event) {
                    if (event.key === 'Escape') {
                        closeSidebar();
                    }
                }
            );
        }
    );
</script>

</body>
</html>
