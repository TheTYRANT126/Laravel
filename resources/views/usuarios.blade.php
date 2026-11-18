@extends('layout')
@section('titulo', 'Usuarios')
@section('contenido')
<h2>Crear empleado</h2><form class="formulario" method="post" action="{{ route('usuarios.store') }}">@csrf
<label for="name">Nombre</label><input id="name" name="name" value="{{ old('name') }}" maxlength="255" required>
<label for="username">Usuario</label><input id="username" name="username" value="{{ old('username') }}" maxlength="50" required>
<label for="email">Correo electrónico</label><input id="email" name="email" type="email" value="{{ old('email') }}" maxlength="255" required>
<label for="password">Contraseña (mínimo 6 caracteres)</label><input id="password" name="password" type="password" minlength="6" autocomplete="new-password" required>
<label for="password_confirmation">Confirmar contraseña</label><input id="password_confirmation" name="password_confirmation" type="password" minlength="6" autocomplete="new-password" required>
<button class="primary">Crear empleado</button></form>
<h2>Usuarios registrados</h2><div class="table-wrap"><table><thead><tr><th>Nombre</th><th>Usuario</th><th>Correo</th><th>Rol</th><th>Estado</th><th>Acciones</th></tr></thead><tbody>
@forelse($usuarios as $usuario)<tr><td>{{ $usuario->name }}</td><td>{{ $usuario->username ?? '—' }}</td><td>{{ $usuario->email }}</td><td>{{ $usuario->role === 'admin' ? 'Administrador' : 'Empleado' }}</td><td>{{ $usuario->activo ? 'Activo' : 'Inactivo' }}</td><td class="actions">@if($usuario->role === 'empleado')<a class="btn edit" href="{{ route('usuarios.edit', $usuario->id) }}">Editar</a><form method="post" action="{{ route('usuarios.estado', $usuario->id) }}" onsubmit="return confirm('¿Cambiar el estado de este empleado?')">@csrf @method('PATCH')<button class="{{ $usuario->activo ? 'delete' : 'secondary' }}">{{ $usuario->activo ? 'Desactivar' : 'Activar' }}</button></form>@endif</td></tr>@empty<tr><td colspan="6">No hay usuarios registrados.</td></tr>@endforelse
</tbody></table></div>
@endsection
