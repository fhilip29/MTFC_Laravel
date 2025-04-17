<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'full_name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'), // You can change this password
            'role' => 'admin',
            'gender' => 'other',
            'fitness_goal' => 'maintain',
            'is_agreed_to_terms' => true,
        ]);
    }
}
