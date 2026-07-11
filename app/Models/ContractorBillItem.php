<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractorBillItem extends Model
{
    protected $fillable = [
        'contractor_bill_id',
        'contractor_item_id',
        'order_no',
        'item_name',
        'quantity',
        'rate',
        'total',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'rate' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function bill(): BelongsTo
    {
        return $this->belongsTo(
            ContractorBill::class,
            'contractor_bill_id'
        );
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(
            ContractorItem::class,
            'contractor_item_id'
        );
    }
}
