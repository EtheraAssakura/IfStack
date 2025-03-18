<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LigneCommande extends Model
{
  protected $table = 'lignes_commande';

  protected $fillable = [
    'commande_id',
    'fourniture_id',
    'quantite',
    'prix_unitaire',
  ];

  protected $casts = [
    'prix_unitaire' => 'decimal:2',
  ];

  public function commande(): BelongsTo
  {
    return $this->belongsTo(Commande::class);
  }

  public function fourniture(): BelongsTo
  {
    return $this->belongsTo(Fourniture::class);
  }

  public function getMontantTotalAttribute(): float
  {
    return $this->quantite * $this->prix_unitaire;
  }
}
