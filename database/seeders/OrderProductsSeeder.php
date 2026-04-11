<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orderProducts = [
            // Order 1
            [
                'order_id' => 1,
                'product_id' => 1,
                'quantity' => 2,
                'created_at' => '2026-04-09 10:15:10',
                'updated_at' => '2026-04-09 10:15:10',
            ],
            [
                'order_id' => 1,
                'product_id' => 3,
                'quantity' => 1,
                'created_at' => '2026-04-09 10:15:10',
                'updated_at' => '2026-04-09 10:15:10',
            ],
            // Order 2
            [
                'order_id' => 2,
                'product_id' => 2,
                'quantity' => 3,
                'created_at' => '2026-04-10 08:45:20',
                'updated_at' => '2026-04-10 08:45:20',
            ],
            [
                'order_id' => 2,
                'product_id' => 4,
                'quantity' => 2,
                'created_at' => '2026-04-10 08:45:20',
                'updated_at' => '2026-04-10 08:45:20',
            ],
            // Order 3
            [
                'order_id' => 3,
                'product_id' => 5,
                'quantity' => 1,
                'created_at' => '2026-04-11 14:20:45',
                'updated_at' => '2026-04-11 14:20:45',
            ],
            // Order 4
            [
                'order_id' => 4,
                'product_id' => 6,
                'quantity' => 4,
                'created_at' => '2026-04-12 11:30:00',
                'updated_at' => '2026-04-12 11:30:00',
            ],
            [
                'order_id' => 4,
                'product_id' => 7,
                'quantity' => 1,
                'created_at' => '2026-04-12 11:30:00',
                'updated_at' => '2026-04-12 11:30:00',
            ],
            // Order 5
            [
                'order_id' => 5,
                'product_id' => 8,
                'quantity' => 2,
                'created_at' => '2026-04-12 13:15:30',
                'updated_at' => '2026-04-12 13:15:30',
            ],
            // Order 6
            [
                'order_id' => 6,
                'product_id' => 9,
                'quantity' => 3,
                'created_at' => '2026-04-13 09:00:00',
                'updated_at' => '2026-04-13 09:00:00',
            ],
            [
                'order_id' => 6,
                'product_id' => 10,
                'quantity' => 1,
                'created_at' => '2026-04-13 09:00:00',
                'updated_at' => '2026-04-13 09:00:00',
            ],
            // Order 7
            [
                'order_id' => 7,
                'product_id' => 11,
                'quantity' => 2,
                'created_at' => '2026-04-14 15:30:45',
                'updated_at' => '2026-04-14 15:30:45',
            ],
            // Order 8
            [
                'order_id' => 8,
                'product_id' => 12,
                'quantity' => 1,
                'created_at' => '2026-04-15 10:45:20',
                'updated_at' => '2026-04-15 10:45:20',
            ],
            [
                'order_id' => 8,
                'product_id' => 13,
                'quantity' => 2,
                'created_at' => '2026-04-15 10:45:20',
                'updated_at' => '2026-04-15 10:45:20',
            ],
            // Order 9
            [
                'order_id' => 9,
                'product_id' => 14,
                'quantity' => 1,
                'created_at' => '2026-04-15 12:00:00',
                'updated_at' => '2026-04-15 12:00:00',
            ],
            // Order 10
            [
                'order_id' => 10,
                'product_id' => 15,
                'quantity' => 3,
                'created_at' => '2026-04-16 07:20:00',
                'updated_at' => '2026-04-16 07:20:00',
            ],
            [
                'order_id' => 10,
                'product_id' => 16,
                'quantity' => 1,
                'created_at' => '2026-04-16 07:20:00',
                'updated_at' => '2026-04-16 07:20:00',
            ],

        ];

        foreach ($orderProducts as $orderProduct) {
            DB::table('order_products')->insert($orderProduct);
        }
    }
}
