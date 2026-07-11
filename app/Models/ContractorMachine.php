<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContractorMachine extends Model
{
    protected $fillable = [
        'contractor_department_id',
        'name',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(
            ContractorDepartment::class,
            'contractor_department_id'
        );
    }

    public function contractors(): HasMany
    {
        return $this->hasMany(
            Contractor::class,
            'contractor_machine_id'
        );
    }
}
