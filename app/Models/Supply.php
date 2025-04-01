<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supply extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'reference',
        'packaging',
        'category_id',
        'image_url'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class, 'supplier_supply')
            ->withPivot('supplier_reference', 'unit_price', 'catalog_url')
            ->withTimestamps();
    }

    public function stockItems(): HasMany
    {
        return $this->hasMany(StockItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(StockItem::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function usageStatistics(): HasMany
    {
        return $this->hasMany(UsageStatistic::class);
    }

    public function forecasts(): HasMany
    {
        return $this->hasMany(Forecast::class);
    }
}
