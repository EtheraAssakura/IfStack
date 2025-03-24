<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'site_id',
        'photo_path',
        'qr_code',
    ];

    /**
     * Get the site that owns the location.
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Get the etablissement that owns the location.
     */
    public function etablissement(): BelongsTo
    {
        return $this->belongsTo(Etablissement::class, 'site_id');
    }

    /**
     * Get the stock items for the location.
     */
    public function stockItems(): HasMany
    {
        return $this->hasMany(StockItem::class);
    }

    /**
     * Generate QR code for this location
     */
    public function generateQrCode(): string
    {
        // Le QR code sera basé sur l'ID de l'emplacement
        return 'LOC-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }
}
