<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryVerification extends Model
{
    protected $fillable = [
        'employee_id',
        'payroll_id',
        'verified_by',
        'verification_status',
        'verified_at',
        'device_name',
        'remarks',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
