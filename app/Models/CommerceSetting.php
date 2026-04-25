<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class CommerceSetting extends Model
{
    protected $fillable = [
        'shipping_price',
    ];

    protected $casts = [
        'shipping_price' => 'decimal:2',
    ];

    protected $appends = [
        'installation_rules',
    ];

    protected $hidden = [
        'installation_price_rules',
    ];

    public function installationPriceRules(): HasMany
    {
        return $this->hasMany(InstallationRule::class)->orderBy('sort_order')->orderBy('min_subtotal');
    }

    public function getInstallationRulesAttribute(): array
    {
        $rules = $this->relationLoaded('installationPriceRules')
            ? $this->getRelation('installationPriceRules')
            : $this->installationPriceRules()->get();

        return $rules
            ->map(fn (InstallationRule $rule) => [
                'id' => $rule->id,
                'min_subtotal' => (float) $rule->min_subtotal,
                'max_subtotal' => $rule->max_subtotal !== null ? (float) $rule->max_subtotal : null,
                'price' => (float) $rule->price,
            ])
            ->values()
            ->all();
    }

    public static function current(): self
    {
        return static::query()->with('installationPriceRules')->firstOrCreate([], [
            'shipping_price' => 0,
        ]);
    }

    public function resolveInstallationPrice(float $subtotal): float
    {
        foreach ($this->installationPriceRules as $rule) {
            $min = (float) $rule->min_subtotal;
            $max = $rule->max_subtotal !== null ? (float) $rule->max_subtotal : null;

            if ($subtotal >= $min && ($max === null || $subtotal <= $max)) {
                return (float) $rule->price;
            }
        }

        return 0;
    }
}
