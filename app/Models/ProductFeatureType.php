<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductFeatureType extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
    ];

    /**
     * Relación: Un tipo de característica tiene muchas características.
     */
    public function features(): HasMany
    {
        return $this->hasMany(Feature::class, 'nombre');
    }
}
