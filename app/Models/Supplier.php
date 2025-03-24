<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'contact_name',
        'email',
        'phone',
        'address',
        'city',
        'postal_code',
        'catalog_url'
    ];

    public function supplies(): BelongsToMany
    {
        return $this->belongsToMany(Supply::class)
            ->withPivot(['supplier_reference', 'unit_price', 'catalog_url'])
            ->withTimestamps();
    }
}
