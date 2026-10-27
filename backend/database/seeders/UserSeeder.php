<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@turing-ia.com'],
            ['name' => 'Administrador Turing', 'password' => 'password', 'role' => 'admin']
        );

        User::updateOrCreate(
            ['email' => 'user@turing-ia.com'],
            ['name' => 'Usuario Demo', 'password' => 'password', 'role' => 'user']
        );
    }
}