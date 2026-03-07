<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImageFile extends Model
{
    use HasFactory;

    protected $table = 'product_images_files';

    protected $fillable = [
        'product_id',
        'path',
        'is_important',
    ];

    protected $casts = [
        'is_important' => 'boolean',
    ];

    /**
     * Relación: Una imagen pertenece a un producto.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
