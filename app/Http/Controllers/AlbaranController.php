<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\CommerceSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class AlbaranController extends Controller
{
    private const VAT_RATE = 0.21;

    public function download($id)
    {
        $order = Order::with('user', 'products', 'packs')->findOrFail($id);
        $user = Auth::user();

        if ($user->role !== 'admin' && $user->role !== 1 && $order->user_id !== $user->id) {
            abort(403, 'No tienes permiso para acceder a este albarán.');
        }

        $albaran = $this->prepareAlbaranData($order);
        $pdf = Pdf::loadView('albaranes.albaran', compact('albaran'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('albaran-'.$order->id.'.pdf');
    }

    private function prepareAlbaranData($order)
    {
        $subtotal = 0;
        $subtotalExcludingVat = 0;
        $taxAmount = 0;
        $items = [];

        foreach ($order->products as $product) {
            $quantity = $product->pivot->quantity;
            $grossUnitPrice = (float) $product->price;
            $discount = (float) ($product->discount ?? 0);
            $unitPrice = $discount > 0
                ? $grossUnitPrice * (1 - ($discount / 100))
                : $grossUnitPrice;
            $unitPriceExcludingVat = $this->getPriceExcludingVat($unitPrice);
            $unitTaxAmount = $unitPrice - $unitPriceExcludingVat;
            $total = $quantity * $unitPrice;
            $totalExcludingVat = $quantity * $unitPriceExcludingVat;
            $totalTaxAmount = $quantity * $unitTaxAmount;
            $subtotal += $total;
            $subtotalExcludingVat += $totalExcludingVat;
            $taxAmount += $totalTaxAmount;
            $items[] = (object) [
                'description' => $product->name,
                'quantity' => $quantity,
                'unit_price' => $unitPriceExcludingVat,
                'total' => $totalExcludingVat,
            ];
        }

        foreach ($order->packs as $pack) {
            $quantity = $pack->pivot->quantity;
            $unitPrice = (float) $pack->total_price;
            $unitPriceExcludingVat = $this->getPriceExcludingVat($unitPrice);
            $unitTaxAmount = $unitPrice - $unitPriceExcludingVat;
            $total = $quantity * $unitPrice;
            $totalExcludingVat = $quantity * $unitPriceExcludingVat;
            $totalTaxAmount = $quantity * $unitTaxAmount;
            $subtotal += $total;
            $subtotalExcludingVat += $totalExcludingVat;
            $taxAmount += $totalTaxAmount;
            $items[] = (object) [
                'description' => 'Pack: '.$pack->name,
                'quantity' => $quantity,
                'unit_price' => $unitPriceExcludingVat,
                'total' => $totalExcludingVat,
            ];
        }

        $settings = CommerceSetting::current();
        $shippingPrice = (float) ($order->shipping_price ?? 0);
        $installationPrice = (float) ($order->installation_price ?? 0);

        $onlineStatuses = ['pending', 'shipped'];
        $installationStatuses = ['installation_confirmed', 'installation_pending', 'installation_finished'];

        if (in_array($order->status, $onlineStatuses)) {
            if ($shippingPrice === 0.0) {
                $shippingPrice = (float) $settings->shipping_price;
            }
        } elseif (in_array($order->status, $installationStatuses) && $installationPrice === 0.0) {
            $installationPrice = $settings->resolveInstallationPrice((float) $subtotal);
        }

        $taxRate = (int) (self::VAT_RATE * 100);

        return (object) [
            'number' => 'ALB-'.str_pad($order->id, 6, '0', STR_PAD_LEFT),
            'date' => $order->created_at,
            'customer' => (object) [
                'name' => $order->customer_name ?? $order->user->name ?? '',
                'last_name_one' => $order->customer_last_name_one ?? $order->user->last_name_one ?? '',
                'last_name_second' => $order->customer_last_name_second ?? $order->user->last_name_second ?? '',
                'dni' => $order->customer_dni ?? $order->user->dni ?? '',
                'address' => $order->customer_address ?? $order->user->billing_address ?? '',
                'zip_code' => $order->customer_zip_code ?? $order->user->billing_zip_code ?? '',
                'province' => $order->customer_province ?? $order->user->billing_province ?? '',
                'country' => $order->customer_country ?? $order->user->billing_country ?? 'España',
                'email' => $order->customer_email ?? $order->user->email ?? '',
                'phone' => $order->customer_phone ?? $order->user->phone ?? '',
            ],
            'billing' => (object) [
                'address' => $order->billing_address,
                'zip_code' => $order->billing_zip_code,
                'province' => $order->billing_province,
                'country' => $order->billing_country,
            ],
            'items' => $items,
            'subtotal' => $subtotalExcludingVat,
            'shipping_price' => $shippingPrice,
            'installation_price' => $installationPrice,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'total' => $subtotal + $shippingPrice + $installationPrice,
        ];
    }

    private function getPriceExcludingVat(float $price): float
    {
        return $price / (1 + self::VAT_RATE);
    }
}
