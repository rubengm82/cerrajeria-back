<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommerceSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommerceSettingController extends Controller
{
    public function show(): JsonResponse
    {
        return response()->json(CommerceSetting::current()->load('installationPriceRules'));
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'shipping_price' => 'required|numeric|min:0',
            'installation_rules' => 'nullable|array',
            'installation_rules.*.min_subtotal' => 'required|numeric|min:0',
            'installation_rules.*.max_subtotal' => 'nullable|numeric|min:0',
            'installation_rules.*.price' => 'required|numeric|min:0',
        ]);

        $rules = collect($validated['installation_rules'] ?? [])
            ->map(fn ($rule) => [
                'min_subtotal' => round((float) $rule['min_subtotal'], 2),
                'max_subtotal' => isset($rule['max_subtotal']) && $rule['max_subtotal'] !== ''
                    ? round((float) $rule['max_subtotal'], 2)
                    : null,
                'price' => round((float) $rule['price'], 2),
            ])
            ->sortBy('min_subtotal')
            ->values()
            ->all();

        $setting = DB::transaction(function () use ($validated, $rules) {
            $setting = CommerceSetting::current();
            $setting->update([
                'shipping_price' => $validated['shipping_price'],
            ]);

            $setting->installationPriceRules()->delete();
            $setting->installationPriceRules()->createMany(
                collect($rules)
                    ->values()
                    ->map(fn (array $rule, int $index) => [
                        ...$rule,
                        'sort_order' => $index,
                    ])
                    ->all()
            );

            return $setting->fresh()->load('installationPriceRules');
        });

        return response()->json($setting);
    }
}
