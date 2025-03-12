<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alerte extends Model
{
  protected $table = 'alertes';

  protected $fillable = [
    'stock_id',
    'user_id',
    'type',
    'commentaire',
    'traitee',
  ];

  protected $casts = [
    'traitee' => 'boolean',
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
