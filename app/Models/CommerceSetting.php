<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommerceSetting extends Model
{
    protected $fillable = [
        'shipping_price',
        'installation_rules',
    ];

    protected $casts = [
        'shipping_price' => 'decimal:2',
        'installation_rules' => 'array',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'shipping_price' => 0,
            'installation_rules' => [],
        ]);
    }
}
