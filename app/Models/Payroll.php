<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'month',
        'basic_salary',
        'present_days',
        'absent_days',
        'leave_days',
        'overtime_hours',
        'overtime_amount',
        'bonus',
        'advance_amount',
        'deduction_amount',
        'gross_salary',
        'net_salary',
        'payment_status',
        'payment_date',
        'remarks',
    ];

    protected $casts = [
        'payment_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
