<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'username' => 'admin',
            'name' => 'Administrator',
            'email' => 'admin@inventory.com',
            'password' => Hash::make('admin123'),
        ]);
    }
}