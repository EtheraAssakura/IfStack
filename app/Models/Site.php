<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Site extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'address',
        'city',
        'postal_code',
        'phone',
        'email',
        'plan_path',
        'is_headquarters'
    ];

    protected $casts = [
        'is_headquarters' => 'boolean'
    ];

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function stockItems(): HasManyThrough
    {
        return $this->hasManyThrough(StockItem::class, Location::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
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
