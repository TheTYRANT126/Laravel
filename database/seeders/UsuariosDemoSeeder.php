<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsuariosDemoSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(['username' => 'admin'], [
            'name' => 'Administrador', 'email' => 'admin@tienda.test',
            'password' => '123456', 'role' => 'admin', 'activo' => true,
        ]);
        User::firstOrCreate(['username' => 'empleado'], [
            'name' => 'Empleado', 'email' => 'empleado@tienda.test',
            'password' => 'e123456', 'role' => 'empleado', 'activo' => true,
        ]);
    }
}
