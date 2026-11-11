@extends('layout')
@section('titulo', $proveedor ? 'Editar proveedor' : 'Crear proveedor')
@section('contenido')
<form class="formulario" method="post" action="{{ $proveedor ? route('proveedores.update', $proveedor->id_proveedor) : route('proveedores.store') }}">@csrf @if($proveedor) @method('PUT') @endif
<label for="razon_social">Razón social</label><input id="razon_social" name="razon_social" maxlength="150" required value="{{ old('razon_social', $proveedor->razon_social ?? '') }}">
<label for="rfc">RFC</label><input id="rfc" name="rfc" maxlength="13" required value="{{ old('rfc', $proveedor->rfc ?? '') }}">
<label for="direccion">Dirección</label><input id="direccion" name="direccion" maxlength="255" required value="{{ old('direccion', $proveedor->direccion ?? '') }}">
<label for="telefono">Teléfono</label><input id="telefono" name="telefono" maxlength="25" required value="{{ old('telefono', $proveedor->telefono ?? '') }}">
<label for="estado">Estado</label><select id="estado" name="estado"><option value="1" @selected((string) old('estado', $proveedor->estado ?? 1) === '1')>Activo</option><option value="0" @selected((string) old('estado', $proveedor->estado ?? 1) === '0')>Inactivo</option></select>
<button class="primary">Guardar</button> <a class="btn secondary" href="{{ route('proveedores.index') }}">Cancelar</a></form>
@endsection
