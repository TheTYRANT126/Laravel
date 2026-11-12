<!doctype html>
<html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Ticket #{{ $venta->id_venta }}</title>
<style>
body{font:15px Arial,sans-serif;color:#1f2937;background:#f3f4f6;margin:0;padding:24px}.ticket{background:#fff;max-width:560px;margin:0 auto;padding:28px;border:1px solid #ddd}h1{margin:0 0 8px;font-size:24px}h2{font-size:17px;margin-top:28px}p{margin:8px 0}table{width:100%;border-collapse:collapse}th,td{padding:10px 4px;border-bottom:1px solid #ddd;text-align:left;vertical-align:top}.numero{text-align:right;white-space:nowrap}.total{font-size:18px;font-weight:bold}.actions{max-width:560px;margin:0 auto 16px;display:flex;gap:12px;align-items:center}button,.back{border:0;border-radius:4px;padding:10px 15px;background:#0d6efd;color:white;font:inherit;cursor:pointer}.back{background:#e9ecef;color:#212529;text-decoration:none}@media print{body{background:white;padding:0}.actions{display:none}.ticket{border:0;padding:0}}
</style></head><body>
<div class="actions"><button type="button" onclick="window.print()">Imprimir / Guardar como PDF</button><a class="back" href="{{ route('ventas.index') }}">Regresar a ventas</a></div>
<main class="ticket"><h1>Tienda</h1><p>Ticket de venta #{{ $venta->id_venta }}</p><p>Fecha: {{ $venta->fecha_venta }}</p><p>Cliente: {{ $venta->cliente }}</p>
<h2>Productos</h2><table><thead><tr><th>Producto</th><th class="numero">Cantidad</th><th class="numero">Precio unitario</th><th class="numero">Subtotal</th></tr></thead><tbody>
@foreach($detalles as $detalle)<tr><td>{{ $detalle->nombre }}</td><td class="numero">{{ $detalle->cantidad }}</td><td class="numero">${{ number_format((float) $detalle->precio_h, 2) }}</td><td class="numero">${{ number_format((float) $detalle->precio_h * $detalle->cantidad, 2) }}</td></tr>@endforeach
</tbody></table><p class="total">Total: ${{ number_format((float) $venta->total, 2) }}</p>
<p>Método de pago: {{ $venta->metodo_pago ? ucfirst($venta->metodo_pago) : 'No registrado' }}</p>
@if($venta->metodo_pago === 'efectivo')<p>Efectivo recibido: ${{ number_format((float) $venta->monto_recibido, 2) }}</p><p>Cambio: ${{ number_format((float) $venta->cambio, 2) }}</p>@endif
<p>Gracias por su compra.</p></main></body></html>
