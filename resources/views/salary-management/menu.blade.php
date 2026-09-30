{{-- Place inside your existing Salary Worker menu and existing permission condition. --}}
<a href="{{ route('payrolls.index') }}" class="nav-link {{ request()->routeIs('payrolls.*') ? 'active' : '' }}">Employees Payroll</a>
<a href="{{ route('salary-management.index') }}" class="nav-link {{ request()->routeIs('salary-management.*') ? 'active' : '' }}">Salary Management</a>
