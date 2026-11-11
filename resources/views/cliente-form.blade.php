@extends('layout')
@section('titulo', $cliente ? 'Editar cliente' : 'Crear cliente')
@section('contenido')
<form class="formulario" method="post" action="{{ $cliente ? route('clientes.update', $cliente->id_cliente) : route('clientes.store') }}">@csrf @if($cliente) @method('PUT') @endif
<label for="nombre">Nombre</label><input id="nombre" name="nombre" maxlength="150" required value="{{ old('nombre', $cliente->nombre ?? '') }}">
<label for="direccion">Dirección</label><input id="direccion" name="direccion" maxlength="255" required value="{{ old('direccion', $cliente->direccion ?? '') }}">
<label for="rfc">RFC</label><input id="rfc" name="rfc" maxlength="13" required value="{{ old('rfc', $cliente->rfc ?? '') }}">
<label for="estado">Estado</label><select id="estado" name="estado"><option value="1" @selected((string) old('estado', $cliente->estado ?? 1) === '1')>Activo</option><option value="0" @selected((string) old('estado', $cliente->estado ?? 1) === '0')>Inactivo</option></select>
<button class="primary">Guardar</button> <a class="btn secondary" href="{{ route('clientes.index') }}">Cancelar</a></form>
@endsection
