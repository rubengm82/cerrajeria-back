<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstallationRule extends Model
{
    protected $fillable = [
        'commerce_setting_id',
        'min_subtotal',
        'max_subtotal',
        'price',
        'sort_order',
    ];

    protected $casts = [
        'min_subtotal' => 'decimal:2',
        'max_subtotal' => 'decimal:2',
        'price' => 'decimal:2',
    ];

    public function commerceSetting(): BelongsTo
    {
        return $this->belongsTo(CommerceSetting::class);
    }
}
