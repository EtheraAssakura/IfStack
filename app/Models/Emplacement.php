<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Emplacement extends Model
{
  protected $table = 'emplacements';

  protected $fillable = [
    'nom',
    'description',
    'qr_code',
    'photo_url',
    'position_plan',
    'etablissement_id',
  ];

  protected $casts = [
    'position_plan' => 'array',
  ];

  public function etablissement(): BelongsTo
  {
    return $this->belongsTo(Etablissement::class);
  }

  public function stocks(): HasMany
  {
    return $this->hasMany(Stock::class);
  }

  public function detailsLivraison(): HasMany
  {
    return $this->hasMany(DetailLivraison::class);
  }
}
