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
  ];

  protected $casts = [
    'processed' => 'boolean',
  ];

  public function stock(): BelongsTo
  {
    return $this->belongsTo(Stock::class);
  }

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }
}
