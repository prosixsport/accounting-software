<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'expense_no',
        'expense_date',
        'expense_category_id',
        'expense_sub_category_id',
        'category',
        'account_id',
        'paid_from_account_id',
        'vendor_name',
        'paid_by',
        'amount',
        'receipt',
        'description',
        'payment_method',
    ];

    public function account()
    {
        return $this->belongsTo(
            Account::class,
            'account_id'
        );
    }

    public function paidFromAccount()
    {
        return $this->belongsTo(
            Account::class,
            'paid_from_account_id'
        );
    }

    public function category()
    {
        return $this->belongsTo(
            ExpenseCategory::class,
            'expense_category_id'
        );
    }

    public function subCategory()
    {
        return $this->belongsTo(
            ExpenseSubCategory::class,
            'expense_sub_category_id'
        );
    }
}
