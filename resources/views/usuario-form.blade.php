@extends('layout')
@section('titulo', 'Editar empleado')
@section('contenido')
<form class="formulario" method="post" action="{{ route('usuarios.update', $usuario->id) }}">@csrf @method('PUT')
<label for="name">Nombre</label><input id="name" name="name" value="{{ old('name', $usuario->name) }}" maxlength="255" required>
<label for="username">Usuario</label><input id="username" name="username" value="{{ old('username', $usuario->username) }}" maxlength="50" required>
<label for="email">Correo electrónico</label><input id="email" name="email" type="email" value="{{ old('email', $usuario->email) }}" maxlength="255" required>
<label for="password">Nueva contraseña (déjala vacía para conservar la actual)</label><input id="password" name="password" type="password" minlength="6" autocomplete="new-password">
<label for="password_confirmation">Confirmar nueva contraseña</label><input id="password_confirmation" name="password_confirmation" type="password" minlength="6" autocomplete="new-password">
<button class="primary">Guardar</button> <a class="btn" href="{{ route('usuarios.index') }}">Cancelar</a></form>
@endsection
