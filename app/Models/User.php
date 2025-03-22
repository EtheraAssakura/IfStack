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
        'site_id',
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

    public function hasRole($role): bool
    {
        return $this->roles()->where('name', $role)->exists();
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    public function hasPermission($permission)
    {
        return $this->roles()->whereJsonContains('permissions', $permission)->exists();
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
