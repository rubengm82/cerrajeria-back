<?php

namespace Database\Seeders;

use App\Models\CommerceSetting;
use App\Models\Order;
use App\Models\Pack;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create('es_ES');
        $users = User::all();
        $products = Product::all();
        $settings = CommerceSetting::current();

        if ($users->isEmpty() || $products->isEmpty()) {
            return;
        }

        $statuses = ['pending', 'shipped', 'installation_confirmed', 'installation_pending', 'installation_finished'];
        $paymentMethods = ['paypal', 'card', 'bizum', 'bank_transfer'];

        // Crear 15 pedidos aleatorios
        for ($i = 0; $i < 15; $i++) {
            $user = $users->random();
            $status = $faker->randomElement($statuses);

            // Datos del cliente (facturación)
            $customerName = $user->name;
            $customerLastName1 = $user->last_name_one;
            $customerLastName2 = $user->last_name_second;
            $customerDni = $user->dni;
            $customerEmail = $user->email;
            $customerPhone = $user->phone;

            // Direcciones
            $billingAddress = $user->billing_address ?? $faker->streetAddress();
            $billingZip = $user->billing_zip_code ?? $faker->postcode();
            $billingProvince = $user->billing_province ?? $faker->state();

            $shippingAddress = $user->shipping_address ?? $faker->streetAddress();
            $shippingZip = $user->shipping_zip_code ?? $faker->postcode();
            $shippingProvince = $user->shipping_province ?? $faker->state();

            $installationAddress = $faker->streetAddress();
            $installationZip = $faker->postcode();
            $installationProvince = $faker->state();

            // Establecer shipped_at y installation_scheduled_at según el estado
            $shippedAt = null;
            $installingAt = null;

            if ($status === 'shipped') {
                $shippedAt = now()->subDays(rand(1, 5));
            }

            if ($status === 'installation_confirmed' || $status === 'installation_pending' || $status === 'installation_finished') {
                $installingAt = ($status === 'installation_confirmed' || $status === 'installation_finished')
                    ? now()->subDays(rand(1, 3))
                    : null;
            }

            // Crear orden
            $order = Order::create([
                'status' => $status,
                'user_id' => $user->id,
                'customer_name' => $customerName,
                'customer_last_name_one' => $customerLastName1,
                'customer_last_name_second' => $customerLastName2,
                'customer_dni' => $customerDni,
                'customer_phone' => $customerPhone,
                'customer_email' => $customerEmail,
                'customer_address' => $billingAddress,
                'customer_zip_code' => $billingZip,
                'customer_province' => $billingProvince,
                'customer_country' => 'España',
                'shipping_address' => $shippingAddress,
                'shipping_zip_code' => $shippingZip,
                'shipping_province' => $shippingProvince,
                'shipping_country' => 'España',
                'installation_address' => $installationAddress,
                'installation_zip_code' => $installationZip,
                'installation_province' => $installationProvince,
                'installation_country' => 'España',
                'shipped_at' => $shippedAt,
                'installation_scheduled_at' => $installingAt,
                'payment_method' => $faker->randomElement($paymentMethods),
                'created_at' => now()->subDays(rand(6, 30)),
                'updated_at' => now()->subDays(rand(0, 5)),
            ]);

            // Asignar productos aleatorios a la orden
            $numProducts = rand(1, 4);
            $selectedProducts = $products->random(min($numProducts, $products->count()));

            $subtotal = 0;

            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 3);
                $installationRequested = in_array($status, ['installation_confirmed', 'installation_pending', 'installation_finished']) ? 1 : 0;

                $order->products()->attach($product->id, [
                    'quantity' => $quantity,
                    'installation_requested' => $installationRequested,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $subtotal += $product->price * $quantity;
            }

            // Asignar packs aleatorios a la orden (si existen)
            $packs = Pack::all();
            if (! $packs->isEmpty()) {
                $numPacks = rand(0, 2); // 0 a 2 packs por orden
                if ($numPacks > 0) {
                    $selectedPacks = $packs->random(min($numPacks, $packs->count()));
                    foreach ($selectedPacks as $pack) {
                        $quantity = rand(1, 2);
                        $order->packs()->attach($pack->id, [
                            'quantity' => $quantity,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $subtotal += $pack->total_price * $quantity;
                    }
                }
            }

            $shippingPrice = 0;
            $installationPrice = 0;

            $onlineStatuses = ['pending', 'shipped'];
            $installationStatuses = ['installation_confirmed', 'installation_pending', 'installation_finished'];

            if (in_array($status, $onlineStatuses)) {
                $shippingPrice = $settings->shipping_price;
            } elseif (in_array($status, $installationStatuses)) {
                $installationPrice = $settings->resolveInstallationPrice((float) $subtotal);
            }

            // Actualizar precios en la orden
            $order->update([
                'shipping_price' => $shippingPrice,
                'installation_price' => $installationPrice,
                'subtotal' => $subtotal,
            ]);
        }
    }
}
