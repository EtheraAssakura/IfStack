<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'firstname',
        'email',
        'password',
        'role',
    ];

    protected $attributes = [
        'role' => '["user"]',
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
            'role' => 'array',
        ];
    }

    public function hasRole($role): bool
    {
        if (in_array('admin', $this->role)) {
            return true;
        }
        return in_array($role, $this->role);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permission');
    }
}
