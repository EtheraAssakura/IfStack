<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categorie extends Model
{
  protected $fillable = [
    'name',
    'description',
  ];

  public function fournitures(): HasMany
  {
    return $this->hasMany(Fourniture::class);
  }
}
