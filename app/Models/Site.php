<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
    protected $fillable = [
        'name',
        'address',
        'city',
        'postal_code',
        'is_headquarters'
    ];

    protected $casts = [
        'is_headquarters' => 'boolean'
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function stockItems(): HasMany
    {
        return $this->hasMany(StockItem::class);
    }

    public function usageStatistics(): HasMany
    {
        return $this->hasMany(UsageStatistic::class);
    }

    public function forecasts(): HasMany
    {
        return $this->hasMany(Forecast::class);
    }

    public function kpiMetrics(): HasMany
    {
        return $this->hasMany(KpiMetric::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }
}
