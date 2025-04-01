<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'type',
        'is_read'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'data' => 'array',
        'read_at' => 'datetime'
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
