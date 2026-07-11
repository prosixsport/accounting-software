<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiometricTemplate extends Model
{
    protected $fillable = [

        'employee_id',
        'finger_name',
        'template_data',
        'device_name',
        'is_active',

    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
