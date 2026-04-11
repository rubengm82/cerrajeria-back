<?php

namespace Database\Seeders;

use App\Models\Order;
use Illuminate\Database\Seeder;

class OrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orders = [
            [
                'status' => 'pending',
                'user_id' => 1,
                'installation_address' => 'Calle de Prueba 123, Barcelona',
                'shipping_address' => 'Calle de Prueba 123, Barcelona',
                'shipped_at' => '2026-04-10 14:20:09',
                'payment_method' => 'paypal',
                'created_at' => '2026-04-09 10:15:10',
                'updated_at' => '2026-04-09 12:20:05',
            ],
            [
                'status' => 'installation_confirmed',
                'user_id' => 2,
                'installation_address' => 'Avenida de la Libertad 456, Madrid',
                'shipping_address' => 'Avenida de la Libertad 456, Madrid',
                'shipped_at' => '2026-04-11 09:30:15',
                'payment_method' => 'card',
                'created_at' => '2026-04-10 08:45:20',
                'updated_at' => '2026-04-11 10:00:30',
            ],
            [
                'status' => 'pending',
                'user_id' => 1,
                'installation_address' => 'Plaza Mayor 789, Valencia',
                'shipping_address' => 'Plaza Mayor 789, Valencia',
                'shipped_at' => null,
                'payment_method' => 'bizum',
                'created_at' => '2026-04-11 14:20:45',
                'updated_at' => '2026-04-11 15:10:00',
            ],
            [
                'status' => 'pending',
                'user_id' => 2,
                'installation_address' => 'Rambla de Catalunya 321, Barcelona',
                'shipping_address' => 'Rambla de Catalunya 321, Barcelona',
                'shipped_at' => null,
                'payment_method' => 'paypal',
                'created_at' => '2026-04-12 11:30:00',
                'updated_at' => '2026-04-12 12:00:00',
            ],
            [
                'status' => 'shipped',
                'user_id' => 1,
                'installation_address' => 'Gran Vía 654, Bilbao',
                'shipping_address' => 'Gran Vía 654, Bilbao',
                'shipped_at' => '2026-04-13 16:45:20',
                'payment_method' => 'card',
                'created_at' => '2026-04-12 13:15:30',
                'updated_at' => '2026-04-13 17:00:00',
            ],
            [
                'status' => 'installation_confirmed',
                'user_id' => 2,
                'installation_address' => 'Paseo de Gracia 987, Barcelona',
                'shipping_address' => 'Paseo de Gracia 987, Barcelona',
                'shipped_at' => '2026-04-14 10:20:10',
                'payment_method' => 'paypal',
                'created_at' => '2026-04-13 09:00:00',
                'updated_at' => '2026-04-14 11:00:00',
            ],
            [
                'status' => 'pending',
                'user_id' => 1,
                'installation_address' => 'Calle de las Flores 147, Sevilla',
                'shipping_address' => 'Calle de las Flores 147, Sevilla',
                'shipped_at' => null,
                'payment_method' => 'bizum',
                'created_at' => '2026-04-14 15:30:45',
                'updated_at' => '2026-04-14 16:00:00',
            ],
            [
                'status' => 'pending',
                'user_id' => 2,
                'installation_address' => 'Avenida de la Constitución 258, Córdoba',
                'shipping_address' => 'Avenida de la Constitución 258, Córdoba',
                'shipped_at' => null,
                'payment_method' => 'card',
                'created_at' => '2026-04-15 10:45:20',
                'updated_at' => '2026-04-15 11:30:00',
            ],
            [
                'status' => 'shipped',
                'user_id' => 1,
                'installation_address' => 'Calle Real 369, Granada',
                'shipping_address' => 'Calle Real 369, Granada',
                'shipped_at' => '2026-04-16 14:15:30',
                'payment_method' => 'paypal',
                'created_at' => '2026-04-15 12:00:00',
                'updated_at' => '2026-04-16 14:30:00',
            ],
            [
                'status' => 'installation_confirmed',
                'user_id' => 2,
                'installation_address' => 'Plaza de España 741, Zaragoza',
                'shipping_address' => 'Plaza de España 741, Zaragoza',
                'shipped_at' => '2026-04-17 08:50:15',
                'payment_method' => 'bizum',
                'created_at' => '2026-04-16 07:20:00',
                'updated_at' => '2026-04-17 09:00:00',
            ],

        ];

        foreach ($orders as $orderData) {
            Order::create($orderData);
        }
    }
}
