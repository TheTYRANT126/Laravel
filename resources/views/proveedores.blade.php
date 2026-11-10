@extends('layout')
@section('contenido')
<h1>Proveedores</h1>
<a class="btn secondary" href="{{ route('proveedores.create') }}">Crear proveedor</a>
<a class="btn secondary" href="{{ route('productos.index') }}">Volver a productos</a>
<nav aria-label="Filtrar proveedores" style="margin-top: 20px">
<a class="btn {{ $estado === 'activos' ? 'primary' : '' }}" href="{{ route('proveedores.index') }}" @if($estado === 'activos') aria-current="page" @endif>Activos</a>
<a class="btn {{ $estado === 'inactivos' ? 'primary' : '' }}" href="{{ route('proveedores.index', ['estado' => 'inactivos']) }}" @if($estado === 'inactivos') aria-current="page" @endif>Inactivos</a>
</nav>
<div style="overflow-x:auto"><table><thead><tr><th>Razón social</th><th>RFC</th><th>Dirección</th><th>Teléfono</th><th>Acciones</th></tr></thead><tbody>
@forelse($proveedores as $proveedor)
<tr><td>{{ $proveedor->razon_social }}</td><td>{{ $proveedor->rfc }}</td><td>{{ $proveedor->direccion }}</td><td>{{ $proveedor->telefono }}</td><td class="actions"><a class="btn edit" href="{{ route('proveedores.edit', $proveedor->id_proveedor) }}">Editar</a>@if($estado === 'activos')<form method="post" action="{{ route('proveedores.destroy', $proveedor->id_proveedor) }}" onsubmit="return confirm('¿Desactivar este proveedor?')">@csrf @method('DELETE')<button class="delete">Desactivar</button></form>@endif</td></tr>
@empty <tr><td colspan="5">No hay proveedores {{ $estado }}.</td></tr> @endforelse
</tbody></table></div>
@endsection
