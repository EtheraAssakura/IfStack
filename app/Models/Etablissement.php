<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Etablissement extends Model
{
  protected $table = 'sites';

  protected $fillable = [
    'name',
    'address',
    'city',
    'postal_code',
    'phone',
    'email',
    'plan_path',
    'slug'
  ];

  public function emplacements(): HasMany
  {
    return $this->hasMany(Emplacement::class, 'site_id');
  }

  public function stocks()
  {
    return $this->hasManyThrough(Stock::class, Emplacement::class, 'site_id', 'location_id');
  }
}
