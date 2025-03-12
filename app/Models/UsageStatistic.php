<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsageStatistic extends Model
{
    protected $fillable = [
        'supply_id',
        'site_id',
        'quantity_used',
        'total_cost',
        'period_start',
        'period_end'
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'total_cost' => 'decimal:2'
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
