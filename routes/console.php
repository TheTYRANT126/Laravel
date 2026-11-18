<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('usuario:crear-admin', function () {
    if (\App\Models\User::where('role', 'admin')->exists()) {
        $this->error('Ya existe un administrador. Crea otros usuarios desde el sistema.');
        return 1;
    }
    $nombre = $this->ask('Nombre del administrador');
    $email = $this->ask('Correo del administrador');
    $password = $this->secret('Contraseña (mínimo 12 caracteres)');
    $confirmacion = $this->secret('Confirma la contraseña');
    $datos = validator(['name' => $nombre, 'email' => $email, 'password' => $password, 'password_confirmation' => $confirmacion], [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::min(12)],
    ])->validate();
    \App\Models\User::create($datos + ['role' => 'admin']);
    $this->info('Administrador creado.');
    return 0;
})->purpose('Crear la primera cuenta administradora');
