<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyAlertSchedule extends Model
{
    protected $fillable = [
        'title',
        'alert_date',
        'alert_time',
        'month',
        'year',
        'status',
        'sent_at',
        'notes',
    ];

    protected $casts = [
        'alert_date' => 'date',
        'sent_at' => 'datetime',
    ];
}
