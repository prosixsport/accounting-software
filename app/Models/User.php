<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\UserPermission;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function permissions()
    {
        return $this->hasMany(UserPermission::class);
    }

   public function hasPermission($key)
{
    if ($this->role === 'super_admin') {
        return true;
    }

    $permissionsCount = $this->permissions()->count();

    if ($permissionsCount === 0) {
        return true;
    }

    return $this->permissions()
        ->where('permission_key', $key)
        ->exists();
}
}
