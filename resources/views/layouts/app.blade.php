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

    <style>
        :root {
            --sidebar-width: 285px;
            --sidebar-bg: #050505;
            --sidebar-panel: #151515;
            --sidebar-hover: #202020;
            --sidebar-border: rgba(255, 255, 255, 0.09);
            --sidebar-text: #d6d6da;
            --sidebar-muted: #8f9198;
            --theme-purple: #2e2d4d;
            --page-bg: #f5f6f8;
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
            overflow-x: hidden;
            background: var(--page-bg);
            color: #172033;
        }

        button,
        input,
        select,
        textarea {
            font-family: inherit;
        }

        .app-layout {
            display: flex;
            min-height: 100vh;
        }

        /* =====================================
           SIDEBAR
        ===================================== */
        #sidebar {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1050;
            width: var(--sidebar-width);
            height: 100vh;
            padding: 17px 14px 15px;
            display: flex;
            flex-direction: column;
            color: #ffffff;
            background:
                radial-gradient(
                    circle at top left,
                    rgba(255, 255, 255, 0.06),
                    transparent 25%
                ),
                linear-gradient(
                    180deg,
                    #050505 0%,
                    #111111 55%,
                    #19182b 100%
                );
            border-right: 1px solid var(--sidebar-border);
            box-shadow: 12px 0 35px rgba(0, 0, 0, 0.12);
            transition: transform 0.25s ease;
        }

        .sidebar-brand {
            position: relative;
            overflow: hidden;
            min-height: 78px;
            margin-bottom: 14px;
            padding: 14px 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 13px;
            border: 1px solid rgba(255, 255, 255, 0.09);
            border-radius: 17px;
            background: rgba(255, 255, 255, 0.055);
        }

        .sidebar-brand::before {
            content: "";
            position: absolute;
            top: -65px;
            right: -50px;
            width: 140px;
            height: 140px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 50%;
        }

        .sidebar-brand-p {
            position: relative;
            z-index: 2;
            width: auto;
            height: 38px;
            max-width: 56px;
            object-fit: contain;
        }

        .sidebar-brand-divider {
            position: relative;
            z-index: 2;
            width: 1px;
            height: 34px;
            flex-shrink: 0;
            background: rgba(255, 255, 255, 0.7);
        }

        .sidebar-brand-name {
            position: relative;
            z-index: 2;
            width: auto;
            height: 25px;
            max-width: 145px;
            object-fit: contain;
        }

        .sidebar-title-row {
            display: none;
            align-items: center;
            justify-content: flex-end;
        }

        .sidebar-close-button {
            width: 35px;
            height: 35px;
            padding: 0;
            display: none;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: #ffffff;
            background: rgba(255, 255, 255, 0.08);
            font-size: 16px;
            cursor: pointer;
        }

        .sidebar-close-button:hover {
            background: #ffffff;
            color: #111111;
        }

        /* =====================================
           USER CARD
        ===================================== */
        .user-box {
            position: relative;
            overflow: hidden;
            flex-shrink: 0;
            min-height: 68px;
            margin-bottom: 15px;
            padding: 11px;
            display: flex;
            align-items: center;
            gap: 11px;
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.06);
        }

        .user-icon {
            width: 43px;
            height: 43px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 13px;
            background: #ffffff;
            color: #090909;
            font-size: 17px;
            font-weight: 900;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.18);
        }

        .user-details {
            min-width: 0;
            flex: 1;
        }

        .user-name {
            max-width: 165px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            color: #ffffff;
            font-size: 14px;
            font-weight: 800;
        }

        .user-role {
            margin-top: 3px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: var(--sidebar-muted);
            font-size: 11px;
            font-weight: 600;
        }

        .user-role::before {
            content: "";
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #22c55e;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.12);
        }

        /* =====================================
           NAVIGATION
        ===================================== */
        .sidebar-nav {
            flex: 1;
            padding-right: 4px;
            padding-bottom: 14px;
            display: flex;
            flex-direction: column;
            gap: 4px;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-nav::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.18);
        }

        .nav-label {
            margin: 11px 10px 5px;
            color: #686a71;
            font-size: 9px;
            font-weight: 900;
            letter-spacing: 1.3px;
            text-transform: uppercase;
        }

        .side-group {
            width: 100%;
        }

        .side-link,
        .side-toggle {
            position: relative;
            width: 100%;
            min-height: 45px;
            padding: 10px 12px;
            display: flex;
            align-items: center;
            gap: 11px;
            border: 0;
            border-radius: 11px;
            color: var(--sidebar-text);
            background: transparent;
            text-align: left;
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            transition:
                background 0.2s ease,
                color 0.2s ease,
                transform 0.2s ease;
        }

        .side-link > i:first-child,
        .side-toggle > i:first-child {
            width: 20px;
            min-width: 20px;
            text-align: center;
            font-size: 17px;
        }

        .side-link span,
        .side-toggle span {
            min-width: 0;
            flex: 1;
        }

        .side-link:hover,
        .side-toggle:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.075);
            transform: translateX(2px);
        }

        .side-link.active,
        .side-toggle.active {
            color: #080808;
            background: #ffffff;
            font-weight: 800;
            box-shadow: 0 9px 22px rgba(0, 0, 0, 0.22);
        }

        .side-link.active::before,
        .side-toggle.active::before {
            content: "";
            position: absolute;
            top: 12px;
            left: -7px;
            width: 4px;
            height: 21px;
            border-radius: 0 8px 8px 0;
            background: #ffffff;
        }

        .side-toggle .arrow {
            width: auto !important;
            min-width: auto !important;
            margin-left: auto;
            color: inherit;
            font-size: 11px !important;
            transition: transform 0.25s ease;
        }

        .side-toggle[aria-expanded="true"] .arrow {
            transform: rotate(90deg);
        }

        .collapse {
            margin-top: 3px;
        }

        .sub-link {
            position: relative;
            min-height: 38px;
            margin: 2px 0;
            padding: 9px 12px 9px 45px;
            display: flex;
            align-items: center;
            border-radius: 9px;
            color: #aeb0b8;
            text-decoration: none;
            font-size: 12.5px;
            font-weight: 600;
            transition: 0.2s ease;
        }

        .sub-link::after {
            content: "";
            position: absolute;
            top: 50%;
            left: 27px;
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: #60626a;
            transform: translateY(-50%);
        }

        .sub-link:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.07);
        }

        .sub-link.active {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.1);
            font-weight: 800;
        }

        .sub-link.active::after {
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.1);
        }

        /* =====================================
           LOGOUT
        ===================================== */
        .logout-form {
            flex-shrink: 0;
            padding-top: 12px;
            padding-bottom: 2px;
            border-top: 1px solid var(--sidebar-border);
        }

        .logout-button {
            width: 100%;
            min-height: 45px;
            padding: 10px 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            border: 1px solid rgba(239, 68, 68, 0.22);
            border-radius: 12px;
            background: rgba(239, 68, 68, 0.1);
            color: #ffb2b2;
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .logout-button:hover {
            border-color: #dc2626;
            background: #dc2626;
            color: #ffffff;
            transform: translateY(-1px);
        }

        /* =====================================
           MAIN CONTENT
        ===================================== */
        .main-content {
            width: calc(100% - var(--sidebar-width));
            min-height: 100vh;
            margin-left: var(--sidebar-width);
            padding: 26px;
        }

        .page-alert {
            margin-bottom: 18px;
            border: none;
            border-radius: 14px;
            box-shadow: 0 6px 22px rgba(17, 24, 39, 0.07);
        }

        /* =====================================
           MOBILE HEADER
        ===================================== */
        .mobile-header {
            display: none;
        }

        .sidebar-overlay {
            display: none;
        }

        .mobile-brand {
            min-width: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .mobile-p-logo {
            width: auto;
            height: 29px;
            object-fit: contain;
        }

        .mobile-brand-line {
            width: 1px;
            height: 28px;
            background: rgba(255, 255, 255, 0.65);
        }

        .mobile-prosix-logo {
            width: auto;
            height: 20px;
            max-width: 125px;
            object-fit: contain;
        }

        .mobile-user-name {
            max-width: 165px;
            margin-left: auto;
            overflow: hidden;
            color: #aeb0b6;
            font-size: 11px;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* =====================================
           RESPONSIVE
        ===================================== */
        @media (max-width: 1199px) {
            :root {
                --sidebar-width: 270px;
            }

            .main-content {
                padding: 22px;
            }
        }

        @media (max-width: 991.98px) {
            body.sidebar-open {
                overflow: hidden;
            }

            .app-layout {
                display: block;
                padding-top: 70px;
            }

            #sidebar {
                width: min(88vw, 300px);
                transform: translateX(-105%);
                box-shadow: 20px 0 55px rgba(0, 0, 0, 0.38);
            }

            body.sidebar-open #sidebar {
                transform: translateX(0);
            }

            .sidebar-title-row {
                display: flex;
                margin-bottom: 8px;
            }

            .sidebar-close-button {
                display: flex;
            }

            .mobile-header {
                position: fixed;
                top: 0;
                right: 0;
                left: 0;
                z-index: 1030;
                height: 70px;
                padding: 10px 15px;
                display: flex;
                align-items: center;
                gap: 12px;
                color: #ffffff;
                background:
                    linear-gradient(
                        135deg,
                        #050505 0%,
                        #111111 60%,
                        #2e2d4d 100%
                    );
                box-shadow: 0 6px 25px rgba(0, 0, 0, 0.2);
            }

            .mobile-menu-button {
                width: 42px;
                height: 42px;
                flex-shrink: 0;
                padding: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 11px;
                color: #ffffff;
                background: rgba(255, 255, 255, 0.08);
                font-size: 24px;
                cursor: pointer;
            }

            .mobile-menu-button:hover {
                background: #ffffff;
                color: #111111;
            }

            .sidebar-overlay {
                position: fixed;
                inset: 0;
                z-index: 1040;
                background: rgba(0, 0, 0, 0.62);
                backdrop-filter: blur(3px);
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
            .main-content {
                padding: 17px 11px;
            }

            .mobile-header {
                height: 66px;
                padding: 9px 11px;
                gap: 9px;
            }

            .app-layout {
                padding-top: 66px;
            }

            .mobile-menu-button {
                width: 39px;
                height: 39px;
            }

            .mobile-p-logo {
                height: 25px;
            }

            .mobile-prosix-logo {
                height: 17px;
                max-width: 105px;
            }

            .mobile-brand-line {
                height: 25px;
            }

            .mobile-user-name {
                display: none;
            }

            .card-body {
                padding: 15px;
            }

            .sidebar-brand {
                min-height: 72px;
            }

            .sidebar-brand-p {
                height: 34px;
            }

            .sidebar-brand-name {
                height: 22px;
                max-width: 132px;
            }
        }
    </style>
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
                id="openSidebarButton"
                aria-label="Open menu">

            <i class="bi bi-list"></i>
        </button>

        <div class="mobile-brand">

            <img
                src="{{ asset('assets/images/P LOGO WHITE.png') }}"
                class="mobile-p-logo"
                alt="Prosix P Logo"
            >

            <span class="mobile-brand-line"></span>

            <img
                src="{{ asset('assets/images/PROSIX SPORTS LOGO PNG WHITE.png') }}"
                class="mobile-prosix-logo"
                alt="Prosix Sports Logo"
            >

        </div>

        <div class="mobile-user-name">
            {{ $user?->name }}
        </div>

    </header>

    {{-- Mobile Overlay --}}
    <div class="sidebar-overlay"
         id="sidebarOverlay"></div>

    {{-- Sidebar --}}
    <aside id="sidebar">

        <div class="sidebar-title-row">

            <button type="button"
                    class="sidebar-close-button"
                    id="closeSidebarButton"
                    aria-label="Close menu">

                <i class="bi bi-x-lg"></i>
            </button>

        </div>

        {{-- Brand Logo --}}
        <div class="sidebar-brand">

            <img
                src="{{ asset('assets/images/P LOGO WHITE.png') }}"
                class="sidebar-brand-p"
                alt="Prosix P Logo"
            >

            <span class="sidebar-brand-divider"></span>

            <img
                src="{{ asset('assets/images/PROSIX SPORTS LOGO PNG WHITE.png') }}"
                class="sidebar-brand-name"
                alt="Prosix Sports Logo"
            >

        </div>

        {{-- User Information --}}
        <div class="user-box">

            <div class="user-icon">
                {{ strtoupper(substr($user?->name ?? 'U', 0, 1)) }}
            </div>

            <div class="user-details">

                <div class="user-name">
                    {{ $user?->name ?? 'User' }}
                </div>

                <div class="user-role">
                    {{ ucfirst(
                        str_replace(
                            '_',
                            ' ',
                            $user?->role ?? 'User'
                        )
                    ) }}
                </div>

            </div>

        </div>

        {{-- Navigation --}}
        <nav class="sidebar-nav">

            <div class="nav-label">Main Menu</div>

            {{-- Dashboard --}}
            @if($can('dashboard'))
                <a href="{{ route('dashboard') }}"
                   class="side-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">

                    <i class="bi bi-grid-1x2-fill"></i>
                    <span>Dashboard</span>
                </a>
            @endif

            {{-- Monthly Alerts --}}
            @if(
                $can('monthly_alerts')
                || $can('monthly_alert_schedules')
            )
                <div class="side-group">

                    <button class="side-link side-toggle {{ $monthlyMenuOpen ? 'active' : '' }}"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#monthlyMenu"
                            aria-expanded="{{ $monthlyMenuOpen ? 'true' : 'false' }}"
                            aria-controls="monthlyMenu">

                        <i class="bi bi-bell-fill"></i>

                        <span>Monthly Alerts</span>

                        <i class="bi bi-chevron-right arrow"></i>
                    </button>

                    <div id="monthlyMenu"
                         class="collapse {{ $monthlyMenuOpen ? 'show' : '' }}">

                        @if($can('monthly_alerts'))
                            <a href="{{ route('monthly-alerts.index') }}"
                               class="sub-link {{ request()->routeIs('monthly-alerts.*') ? 'active' : '' }}">

                                Alerts
                            </a>
                        @endif

                        @if($can('monthly_alert_schedules'))
                            <a href="{{ route('monthly-alert-schedules.index') }}"
                               class="sub-link {{ request()->routeIs('monthly-alert-schedules.*') ? 'active' : '' }}">

                                Alert Schedule
                            </a>
                        @endif

                    </div>

                </div>
            @endif

            {{-- Access Management --}}
            @if($user && $user->role === 'super_admin')
                <a href="{{ route('user-access.index') }}"
                   class="side-link {{ request()->routeIs('user-access.*') ? 'active' : '' }}">

                    <i class="bi bi-shield-lock-fill"></i>
                    <span>Access Management</span>
                </a>
            @endif

            <div class="nav-label">Accounts & Salary</div>

            {{-- Chart of Accounts --}}
            @if($can('accounts'))
                <a href="{{ route('accounts.index') }}"
                   class="side-link {{ request()->routeIs('accounts.*') ? 'active' : '' }}">

                    <i class="bi bi-journal-bookmark-fill"></i>
                    <span>Chart of Accounts</span>
                </a>
            @endif

            {{-- Attendance --}}
            @if($can('attendances'))
                <a href="{{ route('attendances.index') }}"
                   class="side-link {{ request()->routeIs('attendances.*') ? 'active' : '' }}">

                    <i class="bi bi-calendar2-check-fill"></i>
                    <span>Attendance</span>
                </a>
            @endif

            {{-- Biometric --}}
            @if(
                $can('biometric')
                || $can('salary_verifications')
            )
                <div class="side-group">

                    <button class="side-link side-toggle {{ $biometricMenuOpen ? 'active' : '' }}"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#biometricMenu"
                            aria-expanded="{{ $biometricMenuOpen ? 'true' : 'false' }}"
                            aria-controls="biometricMenu">

                        <i class="bi bi-fingerprint"></i>

                        <span>Biometric</span>

                        <i class="bi bi-chevron-right arrow"></i>
                    </button>

                    <div id="biometricMenu"
                         class="collapse {{ $biometricMenuOpen ? 'show' : '' }}">

                        @if($can('biometric'))
                            <a href="{{ route('biometric.index') }}"
                               class="sub-link {{ request()->routeIs('biometric.*') ? 'active' : '' }}">

                                Register Finger
                            </a>
                        @endif

                        @if($can('salary_verifications'))
                            <a href="{{ route('salary-verifications.index') }}"
                               class="sub-link {{ request()->routeIs('salary-verifications.*') ? 'active' : '' }}">

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

                    <button class="side-link side-toggle {{ $salaryWorkerMenuOpen ? 'active' : '' }}"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#salaryWorkerMenu"
                            aria-expanded="{{ $salaryWorkerMenuOpen ? 'true' : 'false' }}"
                            aria-controls="salaryWorkerMenu">

                        <i class="bi bi-people-fill"></i>

                        <span>Salary Workers</span>

                        <i class="bi bi-chevron-right arrow"></i>
                    </button>

                    <div id="salaryWorkerMenu"
                         class="collapse {{ $salaryWorkerMenuOpen ? 'show' : '' }}">

                        @if($can('employees'))
                            <a href="{{ route('employees.index') }}"
                               class="sub-link {{ request()->routeIs('employees.*') ? 'active' : '' }}">

                                Employees
                            </a>
                        @endif

                        @if($can('payrolls'))
                            <a href="{{ route('payrolls.index') }}"
                               class="sub-link {{ request()->routeIs('payrolls.*') ? 'active' : '' }}">

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

                    <button class="side-link side-toggle {{ $contractorMenuOpen ? 'active' : '' }}"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#contractorMenu"
                            aria-expanded="{{ $contractorMenuOpen ? 'true' : 'false' }}"
                            aria-controls="contractorMenu">

                        <i class="bi bi-person-workspace"></i>

                        <span>Contract Work</span>

                        <i class="bi bi-chevron-right arrow"></i>
                    </button>

                    <div id="contractorMenu"
                         class="collapse {{ $contractorMenuOpen ? 'show' : '' }}">

                        @if($can('contractors'))
                            <a href="{{ route('contractors.index') }}"
                               class="sub-link {{ request()->routeIs('contractors.*') ? 'active' : '' }}">

                                Add Contractors
                            </a>
                        @endif

                        @if($can('contractor_items'))
                            <a href="{{ route('contractor-items.index') }}"
                               class="sub-link {{ request()->routeIs('contractor-items.*') ? 'active' : '' }}">

                                Add Items
                            </a>
                        @endif

                        @if($can('contractor_bills'))
                            <a href="{{ route('contractor-bills.index') }}"
                               class="sub-link {{ request()->routeIs('contractor-bills.*') ? 'active' : '' }}">

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

                    <button class="side-link side-toggle {{ $expenseMenuOpen ? 'active' : '' }}"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#expenseMenu"
                            aria-expanded="{{ $expenseMenuOpen ? 'true' : 'false' }}"
                            aria-controls="expenseMenu">

                        <i class="bi bi-wallet-fill"></i>

                        <span>Factory Expenses</span>

                        <i class="bi bi-chevron-right arrow"></i>
                    </button>

                    <div id="expenseMenu"
                         class="collapse {{ $expenseMenuOpen ? 'show' : '' }}">

                        @if($can('expense_categories'))
                            <a href="{{ route('expense-categories.index') }}"
                               class="sub-link {{ request()->routeIs('expense-categories.*') ? 'active' : '' }}">

                                Expense Categories
                            </a>
                        @endif

                        @if($can('expense_sub_categories'))
                            <a href="{{ route('expense-sub-categories.index') }}"
                               class="sub-link {{ request()->routeIs('expense-sub-categories.*') ? 'active' : '' }}">

                                Expense Sub Categories
                            </a>
                        @endif

                        @if($can('expenses'))
                            <a href="{{ route('expenses.index') }}"
                               class="sub-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">

                                Expenses
                            </a>
                        @endif

                    </div>

                </div>
            @endif

            <div class="nav-label">Sales & Customers</div>

            {{-- Customers --}}
            @if($can('customers'))
                <a href="{{ route('customers.index') }}"
                   class="side-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">

                    <i class="bi bi-person-lines-fill"></i>
                    <span>Customers</span>
                </a>
            @endif

            {{-- Invoices --}}
            @if($can('invoices'))
                <a href="{{ route('invoices.index') }}"
                   class="side-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}">

                    <i class="bi bi-receipt-cutoff"></i>
                    <span>Invoices</span>
                </a>
            @endif

            {{-- Payments --}}
            @if($can('payments'))
                <a href="{{ route('payments.index') }}"
                   class="side-link {{ request()->routeIs('payments.*') ? 'active' : '' }}">

                    <i class="bi bi-credit-card-fill"></i>
                    <span>Payments Received</span>
                </a>
            @endif

            {{-- Customer Ledger --}}
            @if($can('customer_ledgers'))
                <a href="{{ route('customer-ledgers.index') }}"
                   class="side-link {{ request()->routeIs('customer-ledgers.*') ? 'active' : '' }}">

                    <i class="bi bi-book-fill"></i>
                    <span>Customer Ledger</span>
                </a>
            @endif

            {{-- Reports --}}
            @if($can('reports'))
                <a href="{{ route('reports.profit-loss') }}"
                   class="side-link {{ request()->routeIs('reports.profit-loss') ? 'active' : '' }}">

                    <i class="bi bi-graph-up-arrow"></i>
                    <span>Profit & Loss</span>
                </a>
            @endif

        </nav>

        {{-- Logout --}}
        <form method="POST"
              action="{{ url('/logout') }}"
              class="logout-form">

            @csrf

            <button type="submit"
                    class="logout-button">

                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </button>

        </form>

    </aside>

    {{-- Main Content --}}
    <main class="main-content">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show page-alert"
                 role="alert">

                <i class="bi bi-check-circle-fill me-2"></i>

                {{ session('success') }}

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Close">
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show page-alert"
                 role="alert">

                <i class="bi bi-exclamation-circle-fill me-2"></i>

                {{ session('error') }}

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Close">
                </button>
            </div>
        @endif

        @yield('content')

    </main>

</div>

@stack('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {
    const body = document.body;

    const openSidebarButton =
        document.getElementById('openSidebarButton');

    const closeSidebarButton =
        document.getElementById('closeSidebarButton');

    const sidebarOverlay =
        document.getElementById('sidebarOverlay');

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
            link.addEventListener('click', function () {
                if (window.innerWidth <= 991) {
                    closeSidebar();
                }
            });
        });

    window.addEventListener('resize', function () {
        if (window.innerWidth > 991) {
            closeSidebar();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeSidebar();
        }
    });
});
</script>

</body>
</html>
