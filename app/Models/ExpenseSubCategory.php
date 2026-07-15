<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseSubCategory extends Model
{
    protected $fillable = [
        'expense_category_id',
        'name',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Subcategory ki main category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(
            ExpenseCategory::class,
            'expense_category_id'
        );
    }

    /**
     * Subcategory ke tamam expenses.
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(
            Expense::class,
            'expense_sub_category_id'
        );
    }
}
