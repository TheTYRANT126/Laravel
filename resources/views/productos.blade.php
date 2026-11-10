@extends('layout')
@section('contenido')
<a class="btn primary" href="{{ route('productos.create') }}">Crear Producto</a>
<a class="btn secondary" href="{{ route('proveedores.create') }}">Crear proveedor</a>
<a class="btn secondary" href="{{ route('proveedores.index') }}">Ver proveedores</a>
<a class="btn secondary" href="{{ route('clientes.index') }}">Ver clientes</a>
<nav aria-label="Filtrar productos" style="margin-top: 20px">
<a class="btn {{ $estado === 'activos' ? 'primary' : '' }}" href="{{ route('productos.index') }}" @if($estado === 'activos') aria-current="page" @endif>Activos</a>
<a class="btn {{ $estado === 'inactivos' ? 'primary' : '' }}" href="{{ route('productos.index', ['estado' => 'inactivos']) }}" @if($estado === 'inactivos') aria-current="page" @endif>Inactivos</a>
</nav>
<table><thead><tr><th>Nombre</th><th>Precio</th><th>Existencias</th><th>Proveedor</th><th>Acciones</th></tr></thead><tbody>
@forelse($productos as $producto)
<tr><td>{{ $producto->nombre }}</td><td>{{ number_format((float) $producto->precio, 2, '.', '') }}</td><td>{{ $producto->existencias }}</td><td>{{ $producto->proveedor }}</td><td class="actions"><a class="btn edit" href="{{ route('productos.edit', $producto->id_producto) }}">Editar</a>@if($estado === 'activos')<form method="post" action="{{ route('productos.destroy', $producto->id_producto) }}" onsubmit="return confirm('¿Desactivar este producto?')">@csrf @method('DELETE')<button class="delete">Desactivar</button></form>@endif</td></tr>
@empty <tr><td colspan="5">No hay productos {{ $estado }}.</td></tr> @endforelse
</tbody></table>
@endsection
