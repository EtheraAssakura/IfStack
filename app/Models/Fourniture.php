<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fourniture extends Model
{
  protected $table = 'supplies';

  protected $fillable = [
    'name',
    'reference',
    'packaging',
    'image_url',
    'category_id',
  ];

  public function categorie(): BelongsTo
  {
    return $this->belongsTo(Categorie::class, 'category_id');
  }

  public function fournisseurs(): BelongsToMany
  {
    return $this->belongsToMany(Fournisseur::class, 'supply_supplier', 'supply_id', 'supplier_id')
      ->withPivot(['supplier_reference', 'unit_price', 'catalog_url'])
      ->withTimestamps();
  }

  public function stocks(): HasMany
  {
    return $this->hasMany(Stock::class, 'supply_id');
  }

  public function alertes()
  {
    return $this->hasManyThrough(Alerte::class, Stock::class, 'supply_id');
  }
}
