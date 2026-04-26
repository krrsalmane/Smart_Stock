<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@smartstock.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        // Magasinier User
        User::create([
            'name' => 'Magasinier User',
            'email' => 'magasinier@smartstock.com',
            'password' => bcrypt('password123'),
            'role' => 'magasinier',
        ]);

        // Client User
        User::create([
            'name' => 'Client User',
            'email' => 'client@smartstock.com',
            'password' => bcrypt('password123'),
            'role' => 'client',
        ]);

        // Supplier User
        User::create([
            'name' => 'Supplier User',
            'email' => 'supplier@smartstock.com',
            'password' => bcrypt('password123'),
            'role' => 'supplier',
        ]);

        // Delivery Agent User
        User::create([
            'name' => 'Delivery Agent',
            'email' => 'delivery@smartstock.com',
            'password' => bcrypt('password123'),
            'role' => 'delivery_agent',
        ]);
    }
}
