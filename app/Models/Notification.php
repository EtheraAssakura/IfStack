<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'message',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean'
    ];

    public function scopeLatestAlerts($query)
    {
        return $query->where('type', 'alert')
            ->latest()
            ->take(5);
    }

    public function scopeLatestRequests($query)
    {
        return $query->where('type', 'request')
            ->latest()
            ->take(5);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'notification_user')
            ->withTimestamps();
    }
}
