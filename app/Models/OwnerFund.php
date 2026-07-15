<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OwnerFund extends Model
{
    protected $fillable = [
        'fund_date',
        'owner_name',
        'amount',
        'received_in',
        'purpose',
        'description',
        'reference_number',
        'attachment',
        'is_active',
    ];

    protected $casts = [
        'fund_date' => 'date',
        'amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}
