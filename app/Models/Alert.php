<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_item_id',
        'user_id',
        'type',
        'comment',
        'processed',
        'title'
    ];

    protected $casts = [
        'processed' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($alert) {
            if ($alert->stockItem) {
                $alert->title = ($alert->stockItem->estimated_quantity === 0 ? 'Alerte Rupture' : 'Alerte Stock') . ' - ' . $alert->stockItem->supply->name;
            }
        });
    }

    public function stockItem()
    {
        return $this->belongsTo(StockItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
