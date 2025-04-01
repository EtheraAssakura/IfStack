<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_name',
        'email',
        'phone',
        'address',
        'catalog_url'
    ];

    public function supplies(): BelongsToMany
    {
        return $this->belongsToMany(Supply::class, 'supplier_supply')
            ->withPivot('supplier_reference', 'unit_price', 'catalog_url')
            ->withTimestamps();
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
