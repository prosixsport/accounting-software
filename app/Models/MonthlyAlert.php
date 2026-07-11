<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyAlert extends Model
{
    protected $fillable = [
        'month',
        'year',
        'employees_salary',
        'contractor_bills',
        'factory_expenses',
        'total_required',
        'status',
        'email_sent_at',
        'arranged_at',
        'notes',
    ];

    protected $casts = [
        'email_sent_at' => 'datetime',
        'arranged_at' => 'datetime',
    ];
}
