<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fournisseur extends Model
{
  protected $table = 'fournisseurs';

  protected $fillable = [
    'nom',
    'url_catalogue',
  ];

  public function fournitures(): BelongsToMany
  {
    return $this->belongsToMany(Fourniture::class, 'fourniture_fournisseur')
      ->withPivot(['reference_fournisseur', 'prix_unitaire'])
      ->withTimestamps();
  }

  public function commandes(): HasMany
  {
    return $this->hasMany(Commande::class);
  }
}
