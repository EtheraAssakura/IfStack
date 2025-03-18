<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Livraison extends Model
{
  protected $table = 'livraisons';

  protected $fillable = [
    'commande_id',
    'user_id',
    'date_prevue',
    'date_effective',
    'statut',
  ];

  protected $casts = [
    'date_prevue' => 'date',
    'date_effective' => 'date',
  ];

  public function commande(): BelongsTo
  {
    return $this->belongsTo(Commande::class);
  }

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  public function details(): HasMany
  {
    return $this->hasMany(DetailLivraison::class);
  }

  public function getEtablissementsAttribute(): array
  {
    return $this->details()
      ->with('emplacement.etablissement')
      ->get()
      ->pluck('emplacement.etablissement')
      ->unique('id')
      ->toArray();
  }
}
