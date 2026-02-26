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
                'last_name_one' => 'Administrador',
                'last_name_second' => null,
                'dni' => '12345678A',
                'phone' => '612345678',
                'email' => 'admin@email.com',
                'address' => 'Calle Principal 1',
                'zip_code' => '08001',
                'password' => 'admin',
                'role' => 'admin'
            ],
        ];
        
        foreach ($users as $user) {
            User::create($user);
        }
        
    }
}
