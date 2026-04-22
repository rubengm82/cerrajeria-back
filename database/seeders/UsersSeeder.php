<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create('es_ES');

        $users = [
            [
                'name' => 'Admin',
                'last_name_one' => 'Sistema',
                'last_name_second' => 'Serralleria',
                'dni' => '12345678A',
                'phone' => '612345678',
                'email' => 'admin@email.com',
                'shipping_address' => 'Carrer d\'Atenes, 27',
                'shipping_zip_code' => '08006',
                'shipping_province' => 'Barcelona',
                'shipping_country' => 'España',
                'billing_address' => 'Carrer d\'Atenes, 27',
                'billing_zip_code' => '08006',
                'billing_province' => 'Barcelona',
                'billing_country' => 'España',
                'password' => 'admin',
                'role' => 'admin',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Juan',
                'last_name_one' => 'García',
                'last_name_second' => 'Pérez',
                'dni' => '11111111A',
                'phone' => '600111222',
                'email' => 'user@email.com',
                'shipping_address' => 'Calle Mayor 10',
                'shipping_zip_code' => '28013',
                'shipping_province' => 'Madrid',
                'shipping_country' => 'España',
                'billing_address' => 'Calle Mayor 10',
                'billing_zip_code' => '28013',
                'billing_province' => 'Madrid',
                'billing_country' => 'España',
                'password' => 'user',
                'role' => 'user',
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        // Crear algunos usuarios adicionales con datos aleatorios de España
        for ($i = 0; $i < 5; $i++) {
            $province = $faker->state();
            $address = $faker->streetAddress();
            $postcode = $faker->postcode();

            User::create([
                'name' => $faker->firstName(),
                'last_name_one' => $faker->lastName(),
                'last_name_second' => $faker->lastName(),
                'dni' => $faker->dni(),
                'phone' => $faker->phoneNumber(),
                'email' => $faker->unique()->safeEmail(),
                'shipping_address' => $address,
                'shipping_zip_code' => $postcode,
                'shipping_province' => $province,
                'shipping_country' => 'España',
                'billing_address' => $address,
                'billing_zip_code' => $postcode,
                'billing_province' => $province,
                'billing_country' => 'España',
                'password' => 'password',
                'role' => 'user',
                'email_verified_at' => now(),
            ]);
        }
    }
}
