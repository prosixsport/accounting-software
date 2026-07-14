<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseReceiptUpload extends Model
{
    protected $fillable = [
        'token',
        'file_path',
        'original_name',
        'mime_type',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
