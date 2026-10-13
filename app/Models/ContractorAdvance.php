<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractorAdvance extends Model
{
    protected $fillable = [
        'contractor_id',
        'amount',
        'advance_date',
        'advance_time',
        'remarks',
        'submission_key',
        'created_by',
        'eligible_for_bills',
        'allocation_limit',
    ];

    protected $casts = [
        'advance_date' => 'date',
        'amount' => 'decimal:2',
        'allocation_limit'=>'decimal:2',
        'eligible_for_bills'=>'boolean',
    ];

    public function contractor(): BelongsTo
    {
        return $this->belongsTo(Contractor::class);
    }
}
