<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Etablissement extends Model
{
  protected $table = 'etablissements';

  protected $fillable = [
    'nom',
    'adresse',
    'ville',
    'code_postal',
  ];

  public function emplacements(): HasMany
  {
    return $this->hasMany(Emplacement::class);
  }

  public function stocks()
  {
    return $this->hasManyThrough(Stock::class, Emplacement::class);
  }
}
