@extends('layout')
@section('titulo', 'Ventas')
@section('contenido')
<style>
.venta-toolbar{display:flex;justify-content:space-between;align-items:end;gap:16px;flex-wrap:wrap;padding-bottom:20px;border-bottom:1px solid #e5e7eb}.cliente-select{display:flex;align-items:end;gap:10px;flex-wrap:wrap}.cliente-select label{margin:0 0 6px}.cliente-select select{min-width:220px}.venta-grid{display:grid;grid-template-columns:minmax(0,1fr) 310px;gap:24px;margin-top:24px}.store-title{font-size:20px;margin:0 0 4px}.store-subtitle{color:#687383;margin:0 0 18px;font-size:14px}.product-list{display:grid;gap:12px}.store-product{border:1px solid #e1e5eb;border-radius:6px;padding:18px;display:flex;align-items:center;gap:18px}.product-info{flex:1;min-width:0}.product-info h3{margin:0 0 8px;font-size:17px}.product-info p{margin:4px 0;color:#536173;font-size:14px}.product-price{font-size:20px;font-weight:bold;color:#202b38}.add-product{display:flex;align-items:center;gap:8px}.add-product input{width:70px}.cart{background:#f8fafc;border:1px solid #e1e5eb;border-radius:6px;padding:18px;align-self:start}.cart h2,.history h2{font-size:19px;margin:0 0 15px}.cart-item{padding:12px 0;border-bottom:1px solid #dee2e6}.cart-item strong{display:block;overflow-wrap:anywhere}.cart-item p{margin:6px 0;color:#536173}.remove{background:none;color:#b42332;padding:0;text-decoration:underline;font-size:14px}.cart-total{display:flex;justify-content:space-between;font-size:18px;font-weight:bold;margin:18px 0}.cart .primary{width:100%}.history{margin-top:36px}.history td{vertical-align:top}.history ul{padding-left:18px;margin:0}.muted{color:#657181;font-size:14px}.primary:disabled{opacity:.5;cursor:not-allowed}@media(max-width:800px){.venta-grid{grid-template-columns:1fr}.store-product{flex-wrap:wrap}.add-product{width:100%;justify-content:flex-end}}@media(max-width:500px){.cliente-select,.cliente-select select{width:100%}}
</style>
<div class="venta-toolbar">
<form class="cliente-select" method="post" action="{{ route('ventas.cliente') }}">@csrf
<div><label for="id_cliente">Cliente</label><select name="id_cliente" id="id_cliente" required onchange="this.form.requestSubmit()"><option value="" disabled @selected(!session('cliente_venta'))>Selecciona un cliente</option>@foreach($clientes as $cliente)<option value="{{ $cliente->id_cliente }}" @selected(session('cliente_venta') == $cliente->id_cliente)>{{ $cliente->nombre }}</option>@endforeach</select></div>
<noscript><button class="primary">Seleccionar</button></noscript></form>
<form method="post" action="{{ route('ventas.nueva') }}" onsubmit="return confirm('¿Iniciar una nueva venta y vaciar el carrito?')">@csrf<button type="submit">＋ Nueva venta</button></form>
</div>
<div class="venta-grid">
<section aria-labelledby="catalogo"><h2 class="store-title" id="catalogo">Productos disponibles</h2><p class="store-subtitle">Explora el catálogo y agrega productos a tu carrito.</p>
<div class="product-list">
@forelse($productos as $producto)
<article class="store-product"><div class="product-info"><h3>{{ $producto->nombre }}</h3><p>{{ $producto->existencias }} disponibles · {{ $producto->u_medida }}</p><span class="product-price">${{ number_format((float) $producto->precio, 2) }}</span></div>
<form class="add-product" action="{{ route('ventas.agregar') }}" method="post">@csrf<input type="hidden" name="id_producto" value="{{ $producto->id_producto }}"><label class="visually-hidden" for="cantidad-{{ $producto->id_producto }}">Cantidad de {{ $producto->nombre }}</label><input id="cantidad-{{ $producto->id_producto }}" name="cantidad" type="number" min="1" max="{{ $producto->existencias }}" value="1" required><button class="primary">Agregar</button></form></article>
@empty <p class="muted">No hay productos disponibles en este momento.</p> @endforelse
</div></section>
<aside class="cart" aria-labelledby="carrito"><h2 id="carrito">Carrito</h2>
@php($total = 0)
@forelse($articulos as $articulo)
@php($subtotal = (float) $articulo->precio * $carrito[$articulo->id_producto])
@php($total += $subtotal)
<div class="cart-item"><strong>{{ $articulo->nombre }}</strong><p>{{ $carrito[$articulo->id_producto] }} × ${{ number_format((float) $articulo->precio, 2) }} = ${{ number_format($subtotal, 2) }}</p><form method="post" action="{{ route('ventas.quitar', $articulo->id_producto) }}">@csrf @method('DELETE')<button class="remove">Quitar</button></form></div>
@empty <p class="muted">Tu carrito está vacío.</p> @endforelse
<div class="cart-total"><span>Total</span><span>${{ number_format($total, 2) }}</span></div>
<form method="post" action="{{ route('ventas.comprar') }}">@csrf<button class="primary" @disabled(!$articulos->count() || !session('cliente_venta'))>Comprar</button></form>
@if(!session('cliente_venta'))<p class="muted">Selecciona un cliente para registrar la compra.</p>@endif
</aside></div>
<section class="history" aria-labelledby="historial"><h2 id="historial">Historial de ventas</h2><div class="table-wrap"><table><thead><tr><th>ID</th><th>Cliente</th><th>Productos y cantidad</th><th>Total</th></tr></thead><tbody>
@forelse($ventas as $venta)
<tr><td>{{ $venta->id_venta }}</td><td>{{ $venta->cliente }}</td><td><ul>@foreach($detalles->get($venta->id_venta, collect()) as $detalle) <li>{{ $detalle->nombre }} × {{ $detalle->cantidad }} (${{ number_format((float) $detalle->precio_h, 2) }} c/u)</li>@endforeach</ul></td><td>${{ number_format((float) $venta->total, 2) }}</td></tr>
@empty <tr><td colspan="4">Aún no hay ventas registradas.</td></tr> @endforelse
</tbody></table></div></section>
@endsection
