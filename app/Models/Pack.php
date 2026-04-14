<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pack extends Model
{
    use HasFactory, SoftDeletes;

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
     * Relación: Un pack puede estar en muchos pedidos.
     */
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_packs')
                    ->withPivot('quantity')
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
