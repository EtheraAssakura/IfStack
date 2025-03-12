<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    protected $fillable = [
        'name',
        'type',
        'parameters',
        'data',
        'user_id',
        'site_id',
        'generated_at'
    ];

    protected $casts = [
        'parameters' => 'array',
        'data' => 'array',
        'generated_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }
}
