<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'customer_code',
        'customer_name',
        'company_name',
        'phone',
        'email',
        'address',
        'opening_balance',
        'status',
        'notes',
    ];
public function invoices()
{
    return $this->hasMany(Invoice::class);
}

public function payments()
{
    return $this->hasMany(Payment::class);
}
}
