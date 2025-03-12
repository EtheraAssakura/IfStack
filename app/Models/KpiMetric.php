<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KpiMetric extends Model
{
    protected $fillable = [
        'name',
        'category',
        'value',
        'unit',
        'site_id',
        'measurement_date'
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'measurement_date' => 'date'
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }
}
