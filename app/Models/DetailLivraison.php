<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailLivraison extends Model
{
  protected $table = 'details_livraison';

  protected $fillable = [
    'livraison_id',
    'emplacement_id',
    'fourniture_id',
    'quantite',
  ];

  public function livraison(): BelongsTo
  {
    return $this->belongsTo(Livraison::class);
  }

  public function emplacement(): BelongsTo
  {
    return $this->belongsTo(Emplacement::class);
  }

  public function fourniture(): BelongsTo
  {
    return $this->belongsTo(Fourniture::class);
  }
}
