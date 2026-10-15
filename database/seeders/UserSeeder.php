<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@dcemilinyuk.id'],
            [
                'name' => 'Hendra Wijaya',
                'phone' => '081234567890',
                'role' => User::ROLE_ADMIN,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
                'address' => 'Jakarta, Indonesia',
            ]
        );

        User::updateOrCreate(
            ['email' => 'superadmin@dcemilinyuk.id'],
            [
                'name' => 'Super Admin',
                'phone' => '081234567891',
                'role' => User::ROLE_SUPERADMIN,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
                'address' => 'Jakarta, Indonesia',
            ]
        );

        User::updateOrCreate(
            ['email' => 'pelanggan@dcemilinyuk.id'],
            [
                'name' => 'Budi Santoso',
                'phone' => '081234567892',
                'role' => User::ROLE_USER,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
                'address' => 'Jl. Merdeka No. 10, Jakarta',
            ]
        );
    }
}