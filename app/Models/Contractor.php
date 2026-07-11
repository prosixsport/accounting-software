<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contractor extends Model
{
    protected $fillable = [
        'name',
        'cnic',
        'phone',
        'photo',
        'contractor_department_id',
        'contractor_machine_id',
        'address',
        'status',
        'notes',
        'cnic_front',
        'cnic_back',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(
            ContractorDepartment::class,
            'contractor_department_id'
        );
    }

    public function machine(): BelongsTo
    {
        return $this->belongsTo(
            ContractorMachine::class,
            'contractor_machine_id'
        );
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ContractorDocument::class);
    }


}
