<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        foreach (['FundReceipt','SalaryManagementAdvance','SalaryManagementRow','EmployeeAdvance','Payroll','ContractorAdvance','ContractorBillPayment','Expense','Payment'] as $name) {
            $model = 'App\\Models\\'.$name;
            $model::observe(\App\Observers\FundActivityObserver::class);
        }
    }
}
