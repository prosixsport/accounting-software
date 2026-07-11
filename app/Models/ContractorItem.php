<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractorItem extends Model
{
    protected $fillable = [
        'name',
        'description',
        'thumbnail',
        'unit',
        'rate',
        'contractor_machine_id',
        'status',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
    ];

    public function machine(): BelongsTo
    {
        return $this->belongsTo(
            ContractorMachine::class,
            'contractor_machine_id'
        );
    }
}
