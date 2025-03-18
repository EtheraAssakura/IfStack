<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Commande extends Model
{
  protected $table = 'commandes';

  protected $fillable = [
    'reference',
    'fournisseur_id',
    'user_id',
    'statut',
    'date_commande',
    'date_livraison_prevue',
  ];

  protected $casts = [
    'date_commande' => 'date',
    'date_livraison_prevue' => 'date',
  ];

  public function fournisseur(): BelongsTo
  {
    return $this->belongsTo(Fournisseur::class);
  }

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  public function lignes(): HasMany
  {
    return $this->hasMany(LigneCommande::class);
  }

  public function livraisons(): HasMany
  {
    return $this->hasMany(Livraison::class);
  }

  public function getMontantTotalAttribute(): float
  {
    return $this->lignes->sum(function ($ligne) {
      return $ligne->quantite * $ligne->prix_unitaire;
    });
  }
}
