@extends('layout')
@section('contenido')
<h1>Clientes</h1>
<a class="btn secondary" href="{{ route('clientes.create') }}">Crear cliente</a>
<a class="btn secondary" href="{{ route('productos.index') }}">Volver a productos</a>
<nav aria-label="Filtrar clientes" style="margin-top: 20px">
<a class="btn {{ $estado === 'activos' ? 'primary' : '' }}" href="{{ route('clientes.index') }}" @if($estado === 'activos') aria-current="page" @endif>Activos</a>
<a class="btn {{ $estado === 'inactivos' ? 'primary' : '' }}" href="{{ route('clientes.index', ['estado' => 'inactivos']) }}" @if($estado === 'inactivos') aria-current="page" @endif>Inactivos</a>
</nav>
<div style="overflow-x:auto"><table><thead><tr><th>Nombre</th><th>Dirección</th><th>RFC</th><th>Acciones</th></tr></thead><tbody>
@forelse($clientes as $cliente)
<tr><td>{{ $cliente->nombre }}</td><td>{{ $cliente->direccion }}</td><td>{{ $cliente->rfc }}</td><td class="actions"><a class="btn edit" href="{{ route('clientes.edit', $cliente->id_cliente) }}">Editar</a>@if($estado === 'activos')<form method="post" action="{{ route('clientes.destroy', $cliente->id_cliente) }}" onsubmit="return confirm('¿Desactivar este cliente?')">@csrf @method('DELETE')<button class="delete">Desactivar</button></form>@endif</td></tr>
@empty <tr><td colspan="4">No hay clientes {{ $estado }}.</td></tr> @endforelse
</tbody></table></div>
@endsection
