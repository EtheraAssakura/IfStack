<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stock extends Model
{
  protected $table = 'stock_items';

  protected $fillable = [
    'supply_id',
    'location_id',
    'estimated_quantity',
    'local_alert_threshold',
  ];

  protected $casts = [
    'estimated_quantity' => 'integer',
    'local_alert_threshold' => 'integer',
  ];

  public function fourniture(): BelongsTo
  {
    return $this->belongsTo(Fourniture::class, 'supply_id');
  }

  public function emplacement(): BelongsTo
  {
    return $this->belongsTo(Emplacement::class, 'location_id');
  }

  public function alertes(): HasMany
  {
    return $this->hasMany(Alerte::class, 'stock_id');
  }

  public function estEnRupture(): bool
  {
    return $this->estimated_quantity <= $this->local_alert_threshold;
  }
}
