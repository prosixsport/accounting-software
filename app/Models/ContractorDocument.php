<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractorDocument extends Model
{
    protected $fillable = [
        'contractor_id',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
    ];

    public function contractor(): BelongsTo
    {
        return $this->belongsTo(Contractor::class);
    }
}
