<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseSubCategory extends Model
{
    protected $fillable = [
        'expense_category_id',
        'name',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }
}
