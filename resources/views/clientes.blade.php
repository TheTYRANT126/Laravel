@extends('layout')
@section('titulo', 'Gestión de Clientes')
@section('contenido')
<div class="toolbar"><a class="btn primary" href="{{ route('clientes.create') }}">＋ Crear cliente</a>
<input class="search" type="search" id="buscar" placeholder="Buscar cliente..." aria-label="Buscar cliente"></div>
<nav class="tabs" aria-label="Filtrar clientes">
<a class="{{ $estado === 'activos' ? 'active' : '' }}" href="{{ route('clientes.index') }}" @if($estado === 'activos') aria-current="page" @endif>Activos ({{ $totales[1] ?? 0 }})</a>
<a class="{{ $estado === 'inactivos' ? 'active' : '' }}" href="{{ route('clientes.index', ['estado' => 'inactivos']) }}" @if($estado === 'inactivos') aria-current="page" @endif>Inactivos ({{ $totales[0] ?? 0 }})</a>
</nav>
<div class="table-wrap"><table><thead><tr><th>Nombre</th><th>Dirección</th><th>RFC</th><th>Acciones</th></tr></thead><tbody>
@forelse($clientes as $cliente)
<tr class="registro"><td>{{ $cliente->nombre }}</td><td>{{ $cliente->direccion }}</td><td>{{ $cliente->rfc }}</td><td class="actions"><a class="btn edit" href="{{ route('clientes.edit', $cliente->id_cliente) }}">Editar</a>@if($estado === 'activos')<form method="post" action="{{ route('clientes.destroy', $cliente->id_cliente) }}" onsubmit="return confirm('¿Desactivar este cliente?')">@csrf @method('DELETE')<button class="delete">Desactivar</button></form>@endif</td></tr>
@empty <tr><td colspan="4">No hay clientes {{ $estado }}.</td></tr> @endforelse
<tr id="sin-resultados" hidden><td colspan="4">No hay resultados para la búsqueda.</td></tr>
</tbody></table></div>
@endsection

