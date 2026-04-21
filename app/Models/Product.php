<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'is_stock_break',
        'code',
        'discount',
        'category_id',
        'is_installable',
        'is_important_to_show',
        'price_keys',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount' => 'decimal:2',
        'price_keys' => 'decimal:2',
        'is_installable' => 'boolean',
        'is_important_to_show' => 'boolean',
        'is_active' => 'boolean',
        'is_stock_break' => 'boolean',
    ];

    /**
     * Relación: Un producto pertenece a una categoría.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Relación: Un producto tiene muchas imágenes.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImageFile::class, 'product_id');
    }

    /**
     * Relación: Un producto puede estar en muchos pedidos.
     */
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_products')
            ->withPivot('quantity', 'installation_requested')
            ->withTimestamps();
    }

    /**
     * Relación: Un producto puede estar en muchos packs.
     */
    public function packs(): BelongsToMany
    {
        return $this->belongsToMany(Pack::class, 'pack_products', 'products_id', 'pack_id')
            ->withTimestamps();
    }

    /**
     * Relación: Un producto tiene muchas características.
     */
    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 'product_features')
            ->withTimestamps();
    }
}
