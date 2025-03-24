<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alerte extends Model
{
  protected $table = 'alerts';

  protected $fillable = [
    'stock_id',
    'user_id',
    'type',
    'comment',
    'processed',
    'title'
  ];

  protected $casts = [
    'processed' => 'boolean',
  ];

  protected static function boot()
  {
    parent::boot();

    static::creating(function ($alerte) {
      if ($alerte->stock) {
        $alerte->title = ($alerte->stock->estimated_quantity === 0 ? 'Alerte Rupture' : 'Alerte Stock') . ' - ' . $alerte->stock->fourniture->name;
      }
    });
  }

  public function stock(): BelongsTo
  {
    return $this->belongsTo(Stock::class);
  }

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }
}
