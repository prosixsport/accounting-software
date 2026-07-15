<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'employee_id',
        'attendance_date',
        'status',
        'check_in',
        'check_out',
        'overtime_hours',
        'remarks',
    ];

   
    public function employee()
{
    return $this->belongsTo(Employee::class);
}
}
