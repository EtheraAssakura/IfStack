<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alert extends Model
{
    protected $fillable = [
        'stock_id',
        'user_id',
        'type',
        'comment',
        'processed'
    ];

    protected $casts = [
        'processed' => 'boolean'
    ];

    public function stockItem(): BelongsTo
    {
        return $this->belongsTo(StockItem::class, 'stock_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
