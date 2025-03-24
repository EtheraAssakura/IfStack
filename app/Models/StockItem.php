<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockItem extends Model
{
    protected $fillable = [
        'supply_id',
        'location_id',
        'estimated_quantity',
        'local_alert_threshold'
    ];

    public function supply(): BelongsTo
    {
        return $this->belongsTo(Supply::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function emplacement(): BelongsTo
    {
        return $this->location();
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }
}
