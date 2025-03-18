<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Emplacement extends Model
{
  protected $table = 'locations';

  protected $fillable = [
    'name',
    'description',
    'qr_code',
    'photo_url',
    'plan_position',
    'site_id',
  ];

  protected $casts = [
    'plan_position' => 'array',
  ];

  public function etablissement(): BelongsTo
  {
    return $this->belongsTo(Etablissement::class, 'site_id');
  }

  public function stocks(): HasMany
  {
    return $this->hasMany(Stock::class, 'location_id');
  }

  public function detailsLivraison(): HasMany
  {
    return $this->hasMany(DetailLivraison::class, 'location_id');
  }
}
