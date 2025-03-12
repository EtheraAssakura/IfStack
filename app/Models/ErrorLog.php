<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ErrorLog extends Model
{
    protected $fillable = [
        'user_id',
        'error_type',
        'message',
        'stack_trace',
        'context',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'context' => 'array'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
