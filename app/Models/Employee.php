<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'employee_code',
        'name',
        'father_name',
        'phone',
        'email',
        'cnic',
        'department',
        'designation',
        'basic_salary',
        'joining_date',
        'status',
        'address',
        'pictures',
        'cnic_pictures',
        'other_documents',
    ];

    protected $casts = [
        'pictures' => 'array',
        'cnic_pictures' => 'array',
        'other_documents' => 'array',
        'joining_date' => 'date',
        'basic_salary' => 'decimal:2',
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    public function biometric()
    {
        return $this->hasOne(BiometricTemplate::class);
    }

    public function advances()
    {
        return $this->hasMany(EmployeeAdvance::class);
    }
}

