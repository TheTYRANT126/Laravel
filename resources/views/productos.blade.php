@extends('layout')
@section('titulo', 'Gestión de Productos')
@section('contenido')
<div class="toolbar">@can('administrar')<a class="btn primary" href="{{ route('productos.create') }}">＋ Crear producto</a>@endcan
<input class="search" type="search" id="buscar" placeholder="Buscar producto..." aria-label="Buscar producto"></div>
<nav class="tabs" aria-label="Filtrar productos">
<a class="{{ $estado === 'activos' ? 'active' : '' }}" href="{{ route('productos.index') }}" @if($estado === 'activos') aria-current="page" @endif>Activos ({{ $totales[1] ?? 0 }})</a>
<a class="{{ $estado === 'inactivos' ? 'active' : '' }}" href="{{ route('productos.index', ['estado' => 'inactivos']) }}" @if($estado === 'inactivos') aria-current="page" @endif>Inactivos ({{ $totales[0] ?? 0 }})</a>
</nav>
<div class="table-wrap"><table><thead><tr><th>Nombre</th><th>Precio</th><th>Existencias</th><th>Proveedor</th>@can('administrar')<th>Acciones</th>@endcan</tr></thead><tbody>
@forelse($productos as $producto)
<tr class="registro"><td>{{ $producto->nombre }}</td><td>{{ number_format((float) $producto->precio, 2, '.', '') }}</td><td>{{ $producto->existencias }}</td><td>{{ $producto->proveedor->razon_social }}</td>@can('administrar')<td class="actions"><a class="btn edit" href="{{ route('productos.edit', $producto->id_producto) }}">Editar</a>@if($estado === 'activos')<form method="post" action="{{ route('productos.destroy', $producto->id_producto) }}" onsubmit="return confirm('¿Desactivar este producto?')">@csrf @method('DELETE')<button class="delete">Desactivar</button></form>@endif</td>@endcan</tr>
@empty <tr><td colspan="{{ auth()->user()->can('administrar') ? 5 : 4 }}">No hay productos {{ $estado }}.</td></tr> @endforelse
<tr id="sin-resultados" hidden><td colspan="{{ auth()->user()->can('administrar') ? 5 : 4 }}">No hay resultados para la búsqueda.</td></tr>
</tbody></table></div>
@endsection

