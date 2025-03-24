<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_id',
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
            if ($alert->stock) {
                $alert->title = ($alert->stock->estimated_quantity === 0 ? 'Alerte Rupture' : 'Alerte Stock') . ' - ' . $alert->stock->fourniture->name;
            }
        });
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
