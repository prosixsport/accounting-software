<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseSubCategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CustomerLedgerController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ContractorController;
use App\Http\Controllers\ContractorItemController;
use App\Http\Controllers\ContractorBillController;
use App\Http\Controllers\ContractorBillItemController;
use App\Http\Controllers\ContractorDepartmentController;
use App\Http\Controllers\ContractorMachineController;
use App\Http\Controllers\UserAccessController;
use App\Http\Controllers\BiometricTemplateController;
use App\Http\Controllers\SalaryVerificationController;
use App\Http\Controllers\MonthlyAlertController;
use App\Http\Controllers\MonthlyAlertScheduleController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get(
    '/login',
    [AuthController::class, 'loginPage']
)->name('login');

Route::post(
    '/login',
    [AuthController::class, 'login']
);

Route::get(
    '/register',
    [AuthController::class, 'registerPage']
)->name('register');

Route::post(
    '/register',
    [AuthController::class, 'register']
);

Route::post(
    '/logout',
    [AuthController::class, 'logout']
)
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/dashboard',
        [DashboardController::class, 'index']
    )->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | User Access
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/user-access',
        [UserAccessController::class, 'index']
    )->name('user-access.index');

    Route::post(
        '/user-access',
        [UserAccessController::class, 'store']
    )->name('user-access.store');

    Route::get(
        '/user-access/{user}/edit',
        [UserAccessController::class, 'edit']
    )->name('user-access.edit');

    Route::put(
        '/user-access/{user}',
        [UserAccessController::class, 'update']
    )->name('user-access.update');

    Route::delete(
        '/user-access/{user}',
        [UserAccessController::class, 'destroy']
    )->name('user-access.destroy');

    /*
    |--------------------------------------------------------------------------
    | Monthly Alerts
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/monthly-alerts',
        [MonthlyAlertController::class, 'index']
    )->name('monthly-alerts.index');

    Route::post(
        '/monthly-alerts/generate',
        [MonthlyAlertController::class, 'generate']
    )->name('monthly-alerts.generate');

    Route::get(
        '/monthly-alerts/{monthlyAlert}',
        [MonthlyAlertController::class, 'show']
    )->name('monthly-alerts.show');

    Route::post(
        '/monthly-alerts/{monthlyAlert}/arranged',
        [MonthlyAlertController::class, 'markArranged']
    )->name('monthly-alerts.arranged');

    Route::resource(
        'monthly-alert-schedules',
        MonthlyAlertScheduleController::class
    );

    /*
    |--------------------------------------------------------------------------
    | Accounts
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'accounts',
        AccountController::class
    );

    /*
    |--------------------------------------------------------------------------
    | Employees
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'employees',
        EmployeeController::class
    );

    /*
    |--------------------------------------------------------------------------
    | Attendance
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'attendances',
        AttendanceController::class
    );

    /*
    |--------------------------------------------------------------------------
    | Payroll
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/payrolls/print-slips',
        [PayrollController::class, 'printSlips']
    )->name('payrolls.print.slips');

    Route::post(
        '/payrolls/advance',
        [PayrollController::class, 'storeAdvance']
    )->name('payrolls.advance.store');

    Route::post(
        '/payrolls/payment-status',
        [PayrollController::class, 'updatePaymentStatus']
    )->name('payrolls.payment.status');

    Route::get(
        '/payrolls/{payroll}/slip',
        [PayrollController::class, 'slip']
    )->name('payrolls.slip');

    Route::resource(
        'payrolls',
        PayrollController::class
    )->except([
        'show',
    ]);

    /*
    |--------------------------------------------------------------------------
    | Biometric
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'biometric',
        BiometricTemplateController::class
    );

    Route::resource(
        'salary-verifications',
        SalaryVerificationController::class
    );

    /*
    |--------------------------------------------------------------------------
    | Expenses
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'expense-categories',
        ExpenseCategoryController::class
    );

    Route::resource(
        'expense-sub-categories',
        ExpenseSubCategoryController::class
    );

    Route::resource(
        'expenses',
        ExpenseController::class
    );

    /*
    |--------------------------------------------------------------------------
    | Customers
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'customers',
        CustomerController::class
    );

    Route::resource(
        'invoices',
        InvoiceController::class
    );

    Route::resource(
        'payments',
        PaymentController::class
    );

    /*
    |--------------------------------------------------------------------------
    | Contractors
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'contractors',
        ContractorController::class
    );

    /*
    |--------------------------------------------------------------------------
    | Contractor Departments
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/contractor-departments',
        [ContractorDepartmentController::class, 'store']
    )->name('contractor-departments.store');

    Route::put(
        '/contractor-departments/{department}',
        [ContractorDepartmentController::class, 'update']
    )->name('contractor-departments.update');

    Route::delete(
        '/contractor-departments/{department}',
        [ContractorDepartmentController::class, 'destroy']
    )->name('contractor-departments.destroy');

    /*
    |--------------------------------------------------------------------------
    | Contractor Machines
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/contractor-machines',
        [ContractorMachineController::class, 'index']
    )->name('contractor-machines.index');

    Route::post(
        '/contractor-machines',
        [ContractorMachineController::class, 'store']
    )->name('contractor-machines.store');

    Route::put(
        '/contractor-machines/{machine}',
        [ContractorMachineController::class, 'update']
    )->name('contractor-machines.update');

    Route::delete(
        '/contractor-machines/{machine}',
        [ContractorMachineController::class, 'destroy']
    )->name('contractor-machines.destroy');

    /*
    |--------------------------------------------------------------------------
    | Contractor Items
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'contractor-items',
        ContractorItemController::class
    );

    /*
    |--------------------------------------------------------------------------
    | Contractor Bills
    |--------------------------------------------------------------------------
    |
    | Custom routes resource route se pehle honi chahiye.
    |
    */

    Route::post(
        '/contractor-bills/advance',
        [ContractorBillController::class, 'storePayment']
    )->name('contractor-bills.advance.store');

    Route::delete(
        '/contractor-bill-payments/{payment}',
        [ContractorBillController::class, 'destroyPayment']
    )->name('contractor-bill-payments.destroy');

    Route::resource(
        'contractor-bills',
        ContractorBillController::class
    );

    Route::delete(
        '/contractor-bill-items/{contractorBillItem}',
        [ContractorBillItemController::class, 'destroy']
    )->name('contractor-bill-items.destroy');

    /*
    |--------------------------------------------------------------------------
    | Customer Ledgers
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/customer-ledgers',
        [CustomerLedgerController::class, 'index']
    )->name('customer-ledgers.index');

    Route::get(
        '/customer-ledgers/{customer}',
        [CustomerLedgerController::class, 'show']
    )->name('customer-ledgers.show');

    /*
    |--------------------------------------------------------------------------
    | Reports
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/reports/profit-loss',
        [ReportController::class, 'profitLoss']
    )->name('reports.profit-loss');
});
