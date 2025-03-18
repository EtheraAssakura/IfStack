<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fournisseur extends Model
{
  protected $table = 'suppliers';

  protected $fillable = [
    'name',
    'catalog_url',
  ];

  public function fournitures(): BelongsToMany
  {
    return $this->belongsToMany(Fourniture::class, 'supply_supplier', 'supplier_id', 'supply_id')
      ->withPivot(['supplier_reference', 'unit_price', 'catalog_url'])
      ->withTimestamps();
  }

  public function commandes(): HasMany
  {
    return $this->hasMany(Commande::class, 'supplier_id');
  }
}
