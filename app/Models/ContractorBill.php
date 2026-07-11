<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContractorBill extends Model
{
   protected $fillable = [
    'bill_no',
    'order_no',
    'contractor_id',
    'bill_date',
    'grand_total',
    'paid_amount',
    'balance',
    'status',
    'notes',
];

    protected $casts = [
        'bill_date' => 'date',
        'grand_total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    public function contractor(): BelongsTo
    {
        return $this->belongsTo(Contractor::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(
            ContractorBillItem::class,
            'contractor_bill_id'
        );
    }

    public function payments(): HasMany
    {
        return $this->hasMany(
            ContractorBillPayment::class,
            'contractor_bill_id'
        );
    }
}
