<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContractorDepartment extends Model
{
    protected $fillable = [
        'name',
    ];

    public function machines(): HasMany
    {
        return $this->hasMany(
            ContractorMachine::class,
            'contractor_department_id'
        );
    }

    public function contractors(): HasMany
    {
        return $this->hasMany(
            Contractor::class,
            'contractor_department_id'
        );
    }
}
