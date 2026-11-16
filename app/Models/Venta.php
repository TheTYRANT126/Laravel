<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class Venta extends Model
{
    protected $table = 'venta';
    protected $primaryKey = 'id_venta';
    public $timestamps = false;
    protected $fillable = ['fecha_venta', 'id_cliente', 'total', 'metodo_pago', 'monto_recibido', 'cambio'];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'id_venta', 'id_venta');
    }

    public static function historial()
    {
        return static::with(['cliente', 'detalles.producto'])->orderByDesc('id_venta')->get();
    }

    public static function ticket(int $id)
    {
        return static::with(['cliente', 'detalles.producto'])->findOrFail($id);
    }

    public static function registrar(int $cliente, array $carrito, array $datos): int
    {
        return DB::transaction(function () use ($cliente, $carrito, $datos) {
            if (! Cliente::where('id_cliente', $cliente)->where('estado', 1)->lockForUpdate()->first()) {
                throw ValidationException::withMessages(['venta' => 'El cliente seleccionado ya no está disponible.']);
            }
            ksort($carrito);
            $productos = Producto::whereIn('id_producto', array_keys($carrito))->orderBy('id_producto')->lockForUpdate()->get()->keyBy('id_producto');
            foreach ($carrito as $id => $cantidad) {
                $producto = $productos->get($id);
                if (! $producto || ! $producto->estado || $cantidad < 1 || $cantidad > $producto->existencias) {
                    throw ValidationException::withMessages(['venta' => 'Un producto del carrito ya no está disponible o no tiene existencias suficientes.']);
                }
            }
            $venta = static::create(['fecha_venta' => now(), 'id_cliente' => $cliente]);
            foreach ($carrito as $id => $cantidad) {
                $venta->detalles()->create(['id_producto' => $id, 'cantidad' => $cantidad, 'precio_h' => $productos[$id]->precio]);
                $productos[$id]->decrement('existencias', $cantidad);
            }
            $total = $venta->detalles()->selectRaw('SUM(cantidad * precio_h) as total')->value('total');
            $recibido = $datos['metodo_pago'] === 'efectivo' ? $datos['monto_recibido'] : null;
            $cambio = null;
            if ($recibido !== null) {
                $pago = DB::selectOne('SELECT CAST(? AS DECIMAL(20,2)) >= CAST(? AS DECIMAL(20,2)) AS suficiente, CAST(? AS DECIMAL(20,2)) - CAST(? AS DECIMAL(20,2)) AS cambio', [$recibido, $total, $recibido, $total]);
                if (! $pago->suficiente) {
                    throw ValidationException::withMessages(['monto_recibido' => 'El efectivo recibido no cubre el total de la venta.']);
                }
                $cambio = $pago->cambio;
            }
            $venta->update([
                'total' => $total, 'metodo_pago' => $datos['metodo_pago'],
                'monto_recibido' => $recibido, 'cambio' => $cambio,
            ]);
            return $venta->id_venta;
        });
    }
}
