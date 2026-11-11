@extends('layout')
@section('titulo', 'Gestión de Proveedores')
@section('contenido')
<div class="toolbar"><a class="btn primary" href="{{ route('proveedores.create') }}">＋ Crear proveedor</a>
<input class="search" type="search" id="buscar" placeholder="Buscar proveedor..." aria-label="Buscar proveedor"></div>
<nav class="tabs" aria-label="Filtrar proveedores">
<a class="{{ $estado === 'activos' ? 'active' : '' }}" href="{{ route('proveedores.index') }}" @if($estado === 'activos') aria-current="page" @endif>Activos ({{ $totales[1] ?? 0 }})</a>
<a class="{{ $estado === 'inactivos' ? 'active' : '' }}" href="{{ route('proveedores.index', ['estado' => 'inactivos']) }}" @if($estado === 'inactivos') aria-current="page" @endif>Inactivos ({{ $totales[0] ?? 0 }})</a>
</nav>
<div class="table-wrap"><table><thead><tr><th>Razón social</th><th>RFC</th><th>Dirección</th><th>Teléfono</th><th>Acciones</th></tr></thead><tbody>
@forelse($proveedores as $proveedor)
<tr class="registro"><td>{{ $proveedor->razon_social }}</td><td>{{ $proveedor->rfc }}</td><td>{{ $proveedor->direccion }}</td><td>{{ $proveedor->telefono }}</td><td class="actions"><a class="btn edit" href="{{ route('proveedores.edit', $proveedor->id_proveedor) }}">Editar</a>@if($estado === 'activos')<form method="post" action="{{ route('proveedores.destroy', $proveedor->id_proveedor) }}" onsubmit="return confirm('¿Desactivar este proveedor?')">@csrf @method('DELETE')<button class="delete">Desactivar</button></form>@endif</td></tr>
@empty <tr><td colspan="5">No hay proveedores {{ $estado }}.</td></tr> @endforelse
<tr id="sin-resultados" hidden><td colspan="5">No hay resultados para la búsqueda.</td></tr>
</tbody></table></div>
@endsection

