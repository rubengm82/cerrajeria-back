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
         $users = [
             [
                 'name' => 'Admin',
                 'last_name_one' => 'Apellido1',
                 'last_name_second' => null,
                 'dni' => '12345678A',
                 'phone' => '612345678',
                 'email' => 'admin@email.com',
                 'shipping_address' => 'Calle Principal 1',
                 'shipping_zip_code' => '08001',
                 'shipping_province' => 'Barcelona',
                 'shipping_country' => 'España',
                 'billing_address' => 'Calle Principal 1',
                 'billing_zip_code' => '08001',
                 'billing_province' => 'Barcelona',
                 'billing_country' => 'España',
                 'password' => 'admin',
                 'role' => 'admin',
                 'email_verified_at' => now(),
             ],
             [
                 'name' => 'User',
                 'last_name_one' => 'Apellido1',
                 'last_name_second' => null,
                 'dni' => '12345679A',
                 'phone' => '612345679',
                 'email' => 'user@email.com',
                 'shipping_address' => 'Calle Principal 2',
                 'shipping_zip_code' => '08002',
                 'shipping_province' => 'Barcelona',
                 'shipping_country' => 'España',
                 'billing_address' => 'Calle Principal 2',
                 'billing_zip_code' => '08002',
                 'billing_province' => 'Barcelona',
                 'billing_country' => 'España',
                 'password' => 'user',
                 'role' => 'user',
                 'email_verified_at' => now(),
             ],
         ];
        
        foreach ($users as $user) {
            User::create($user);
        }
        
    }
}
