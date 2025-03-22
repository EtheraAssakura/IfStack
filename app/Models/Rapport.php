<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rapport extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'path',
        'created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime'
    ];
}
