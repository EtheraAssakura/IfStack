<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Forecast extends Model
{
    protected $fillable = [
        'supply_id',
        'site_id',
        'predicted_quantity',
        'predicted_cost',
        'prediction_date',
        'target_date',
        'calculation_parameters'
    ];

    protected $casts = [
        'prediction_date' => 'date',
        'target_date' => 'date',
        'predicted_cost' => 'decimal:2',
        'calculation_parameters' => 'array'
    ];

    public function supply(): BelongsTo
    {
        return $this->belongsTo(Supply::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }
}
