<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'supply_id',
        'location_id',
        'quantity',
        'local_alert_threshold',
        'estimated_quantity',
        'qr_code'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'local_alert_threshold' => 'integer',
        'estimated_quantity' => 'integer'
    ];

    public function supply(): BelongsTo
    {
        return $this->belongsTo(Supply::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
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

    public function qrScans(): HasMany
    {
        return $this->hasMany(QrScan::class);
    }
}
