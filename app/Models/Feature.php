<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Feature extends Model
{
    use HasFactory;

    protected $fillable = [
        'type_id',
        'value',
    ];

    /**
     * Relación: Una característica pertenece a un tipo de característica.
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(FeatureType::class, 'type_id');
    }

    /**
     * Relación: Una característica puede pertenecer a muchos productos.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_features')
                    ->withTimestamps();
    }
}
