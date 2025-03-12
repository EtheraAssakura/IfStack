<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stock extends Model
{
  protected $table = 'stocks';

  protected $fillable = [
    'fourniture_id',
    'emplacement_id',
    'quantite_estimee',
    'seuil_alerte_local',
  ];

  public function fourniture(): BelongsTo
  {
    return $this->belongsTo(Fourniture::class);
  }

  public function emplacement(): BelongsTo
  {
    return $this->belongsTo(Emplacement::class);
  }

  public function alertes(): HasMany
  {
    return $this->hasMany(Alerte::class);
  }

  public function estEnRupture(): bool
  {
    return $this->quantite_estimee <= ($this->seuil_alerte_local ?? $this->fourniture->seuil_alerte_global);
  }
}
