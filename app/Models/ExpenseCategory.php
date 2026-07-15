<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseCategory extends Model
{
    protected $fillable = [
        'name',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Category ki active aur inactive sab subcategories.
     */
    public function subCategories(): HasMany
    {
        return $this->hasMany(
            ExpenseSubCategory::class,
            'expense_category_id'
        );
    }

    /**
     * Sirf active subcategories.
     */
    public function activeSubCategories(): HasMany
    {
        return $this->hasMany(
            ExpenseSubCategory::class,
            'expense_category_id'
        )
            ->where('status', true)
            ->orderBy('name');
    }

    /**
     * Category ke tamam expenses.
     *
     * Subcategory ho ya na ho, category ke against
     * save hone wale sab expenses is relation mein aayenge.
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(
            Expense::class,
            'expense_category_id'
        );
    }

    /**
     * Category ke direct expenses.
     *
     * Ye un categories ke liye hain jin ki
     * koi subcategory nahi hai.
     */
    public function directExpenses(): HasMany
    {
        return $this->hasMany(
            Expense::class,
            'expense_category_id'
        )
            ->whereNull('expense_sub_category_id');
    }

    /**
     * Check category ke andar active subcategories hain ya nahi.
     */
    public function getHasSubCategoriesAttribute(): bool
    {
        if (array_key_exists(
            'sub_categories_count',
            $this->attributes
        )) {
            return (int) $this->attributes[
                'sub_categories_count'
            ] > 0;
        }

        return $this->activeSubCategories()->exists();
    }
}
