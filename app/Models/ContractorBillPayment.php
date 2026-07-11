<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractorBillPayment extends Model
{
    protected $fillable = [
        'contractor_bill_id',
        'amount',
        'payment_date',
        'payment_time',
        'remarks',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function bill(): BelongsTo
    {
        return $this->belongsTo(
            ContractorBill::class,
            'contractor_bill_id'
        );
    }
}
