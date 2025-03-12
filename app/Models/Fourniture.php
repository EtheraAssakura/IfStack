<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fourniture extends Model
{
  protected $fillable = [
    'nom',
    'reference_isfac',
    'conditionnement',
    'url_catalogue',
    'seuil_alerte_global',
    'image_url',
    'categorie_id',
  ];

  public function categorie(): BelongsTo
  {
    return $this->belongsTo(Categorie::class);
  }

  public function fournisseurs(): BelongsToMany
  {
    return $this->belongsToMany(Fournisseur::class, 'fourniture_fournisseur')
      ->withPivot(['reference_fournisseur', 'prix_unitaire'])
      ->withTimestamps();
  }

  public function stocks(): HasMany
  {
    return $this->hasMany(Stock::class);
  }

  public function alertes()
  {
    return $this->hasManyThrough(Alerte::class, Stock::class);
  }
}
