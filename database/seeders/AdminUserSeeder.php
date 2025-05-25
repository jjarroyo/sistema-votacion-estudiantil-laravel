<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         User::create([
            'name' => 'Administrador Electoral', // Nombre del administrador
            'email' => 'admin@tucolegio.edu.co', // Cambia esto por un email real y seguro
            'password' => Hash::make('12345678'), // Cambia esto por una contraseña segura
        ]);
    }
}
