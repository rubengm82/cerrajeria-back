<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Order;

class AlbaranController extends Controller
{
    public function download($id)
    {
        // Get the order with related data
        $order = Order::with('user', 'products', 'packs')->findOrFail($id);

        $user = Auth::user();

        // Check if user can access this order
        if ($user->role !== 'admin' && $user->role !== 1 && $order->user_id !== $user->id) {
            abort(403, 'No tienes permiso para acceder a este albarán.');
        }

        // Prepare albaran data from order
        $albaran = $this->prepareAlbaranData($order);

        // Generate PDF
        $pdf = Pdf::loadView('albaranes.albaran', compact('albaran'));

        // Set paper size and orientation
        $pdf->setPaper('a4', 'portrait');

        // Return PDF download
        return $pdf->download('albaran-' . $order->id . '.pdf');
    }

    private function prepareAlbaranData($order)
    {
        $subtotal = 0;
        $items = [];

        foreach ($order->products as $product) {
            $quantity = $product->pivot->quantity;
            $unitPrice = $product->price;
            $total = $quantity * $unitPrice;
            $subtotal += $total;

            $items[] = (object) [
                'description' => $product->name,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total' => $total,
            ];
        }

        foreach ($order->packs as $pack) {
            $quantity = $pack->pivot->quantity;
            $unitPrice = $pack->total_price;
            $total = $quantity * $unitPrice;
            $subtotal += $total;

            $items[] = (object) [
                'description' => 'Pack: ' . $pack->name,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total' => $total,
            ];
        }

        $taxRate = 21;
        $taxAmount = $subtotal * ($taxRate / 100);

        return (object) [
            'number' => 'ALB-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
            'date' => $order->created_at,
            'due_date' => $order->created_at->addDays(30),

            'customer' => (object) [
                'name' => $order->customer_name ?? $order->user->name ?? '',
                'last_name_one' => $order->customer_last_name_one ?? $order->user->last_name_one ?? '',
                'last_name_second' => $order->customer_last_name_second ?? $order->user->last_name_second ?? '',
                'dni' => $order->customer_dni ?? $order->user->dni ?? '',
                'address' => $order->shipping_address ?? $order->customer_address ?? $order->user->address ?? '',
                'zip_code' => $order->customer_zip_code ?? $order->user->zip_code ?? '',
                'city' => '',
                'country' => $order->user->country ?? 'España',
                'email' => $order->customer_email ?? $order->user->email ?? '',
            ],

            'items' => $items,
            'subtotal' => $subtotal,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'total' => $subtotal + $taxAmount,
        ];
    }
}
