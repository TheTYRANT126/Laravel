@extends('layout')
@section('contenido')
<h1>{{ $producto ? 'Editar producto' : 'Crear producto' }}</h1>
@if($proveedores->isEmpty()) <p class="notice">Primero <a href="{{ route('proveedores.create') }}">crea un proveedor</a>.</p> @endif
<form class="formulario" method="post" action="{{ $producto ? route('productos.update', $producto->id_producto) : route('productos.store') }}">@csrf @if($producto) @method('PUT') @endif
<label for="nombre">Nombre</label><input id="nombre" name="nombre" maxlength="150" required value="{{ old('nombre', $producto->nombre ?? '') }}">
<label for="precio">Precio</label><input id="precio" name="precio" type="number" min="0" max="99999999.99" step="0.01" required value="{{ old('precio', $producto->precio ?? '') }}">
<label for="existencias">Existencias</label><input id="existencias" name="existencias" type="number" min="0" max="4294967295" step="1" required value="{{ old('existencias', $producto->existencias ?? '') }}">
<label for="id_proveedor">Proveedor</label><select id="id_proveedor" name="id_proveedor" required><option value="">Selecciona un proveedor</option>@foreach($proveedores as $proveedor)<option value="{{ $proveedor->id_proveedor }}" @selected(old('id_proveedor', $producto->id_proveedor ?? '') == $proveedor->id_proveedor)>{{ $proveedor->razon_social }}</option>@endforeach</select>
<label for="u_medida">Unidad de medida</label><input id="u_medida" name="u_medida" maxlength="30" required value="{{ old('u_medida', $producto->u_medida ?? '') }}">
<label for="estado">Estado</label><select id="estado" name="estado"><option value="1" @selected((string) old('estado', $producto->estado ?? 1) === '1')>Activo</option><option value="0" @selected((string) old('estado', $producto->estado ?? 1) === '0')>Inactivo</option></select>
<button class="primary" @disabled($proveedores->isEmpty())>Guardar</button> <a class="btn" href="{{ route('productos.index') }}">Cancelar</a></form>
@endsection
