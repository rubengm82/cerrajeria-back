<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pack extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'total_price',
        'description',
    ];

    /**
     * Relación: Un pack tiene muchos productos.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'pack_products', 'pack_id', 'products_id')
                    ->withTimestamps();
    }

    /**
     * Relación: Un pack tiene muchas imágenes.
     */
    public function images(): HasMany
    {
        return $this->hasMany(PackImageFile::class, 'packs_id');
    }
}
