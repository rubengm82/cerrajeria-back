<?php

namespace Database\Seeders;

use App\Models\Order;
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

        if ($users->isEmpty()) {
            return;
        }

        $statuses = ['in_cart', 'pending', 'shipped', 'installation_confirmed', 'installation_pending'];
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

            if ($status === 'shipped' || $status === 'installation_confirmed') {
                $shippedAt = now()->subDays(rand(1, 5));
            }

            if ($status === 'installation_confirmed' || $status === 'installation_pending') {
                $installingAt = $status === 'installation_confirmed'
                    ? now()->subDays(rand(1, 3))
                    : null;
            }

            Order::create([
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
        }
    }
}
