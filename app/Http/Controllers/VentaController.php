<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class VentaController extends Controller
{
    public function index(Request $request)
    {
        $productos = DB::table('producto')->where('estado', 1)->where('existencias', '>', 0)->orderBy('nombre')->get();
        $clientes = DB::table('cliente')->where('estado', 1)->orderBy('nombre')->get();
        $carrito = $request->session()->get('carrito', []);
        $articulos = DB::table('producto')->whereIn('id_producto', array_keys($carrito))->orderBy('nombre')->get();
        $ventas = DB::table('venta')->join('cliente', 'venta.id_cliente', '=', 'cliente.id_cliente')
            ->orderByDesc('venta.id_venta')->select('venta.*', 'cliente.nombre as cliente')->get();
        $detalles = DB::table('det_venta')->join('producto', 'det_venta.id_producto', '=', 'producto.id_producto')
            ->select('det_venta.*', 'producto.nombre')->get()->groupBy('id_venta');
        return view('ventas', compact('productos', 'clientes', 'carrito', 'articulos', 'ventas', 'detalles'));
    }

    public function cliente(Request $request)
    {
        $datos = $request->validate(['id_cliente' => ['required', 'integer', \Illuminate\Validation\Rule::exists('cliente', 'id_cliente')->where('estado', 1)]]);
        $request->session()->put('cliente_venta', $datos['id_cliente']);
        return redirect()->route('ventas.index');
    }

    public function agregar(Request $request)
    {
        $datos = $request->validate(['id_producto' => 'required|integer', 'cantidad' => 'required|integer|min:1|max:4294967295']);
        $producto = DB::table('producto')->where('id_producto', $datos['id_producto'])->where('estado', 1)->first();
        $carrito = $request->session()->get('carrito', []);
        $cantidad = ($carrito[$datos['id_producto']] ?? 0) + (int) $datos['cantidad'];
        if (! $producto || $cantidad > $producto->existencias) {
            throw ValidationException::withMessages(['cantidad' => 'La cantidad solicitada supera las existencias disponibles.']);
        }
        $carrito[$datos['id_producto']] = $cantidad;
        $request->session()->put('carrito', $carrito);
        return redirect()->route('ventas.index')->with('mensaje', 'Producto agregado al carrito.');
    }

    public function quitar(Request $request, int $id)
    {
        $carrito = $request->session()->get('carrito', []);
        unset($carrito[$id]);
        $request->session()->put('carrito', $carrito);
        return redirect()->route('ventas.index');
    }

    public function nueva(Request $request)
    {
        $request->session()->forget(['carrito', 'cliente_venta']);
        return redirect()->route('ventas.index')->with('mensaje', 'Nueva venta iniciada.');
    }

    public function comprar(Request $request)
    {
        $datos = $request->validate([
            'metodo_pago' => ['required', Rule::in(['efectivo', 'tarjeta', 'transferencia'])],
            'monto_recibido' => ['required_if:metodo_pago,efectivo', 'nullable', 'regex:/^\d{1,12}(\.\d{1,2})?$/'],
        ]);
        $carrito = $request->session()->get('carrito', []);
        $cliente = $request->session()->get('cliente_venta');
        if (! $cliente || ! $carrito) {
            throw ValidationException::withMessages(['venta' => 'Selecciona un cliente y agrega productos al carrito.']);
        }
        $idVenta = DB::transaction(function () use ($cliente, $carrito, $datos) {
            if (! DB::table('cliente')->where('id_cliente', $cliente)->where('estado', 1)->lockForUpdate()->exists()) {
                throw ValidationException::withMessages(['venta' => 'El cliente seleccionado ya no está disponible.']);
            }
            ksort($carrito);
            $productos = DB::table('producto')->whereIn('id_producto', array_keys($carrito))->orderBy('id_producto')->lockForUpdate()->get()->keyBy('id_producto');
            foreach ($carrito as $id => $cantidad) {
                $producto = $productos->get($id);
                if (! $producto || ! $producto->estado || $cantidad < 1 || $cantidad > $producto->existencias) {
                    throw ValidationException::withMessages(['venta' => 'Un producto del carrito ya no está disponible o no tiene existencias suficientes.']);
                }
            }
            $idVenta = DB::table('venta')->insertGetId(['fecha_venta' => now(), 'id_cliente' => $cliente]);
            foreach ($carrito as $id => $cantidad) {
                DB::table('det_venta')->insert(['id_venta' => $idVenta, 'id_producto' => $id, 'cantidad' => $cantidad, 'precio_h' => $productos[$id]->precio]);
                DB::table('producto')->where('id_producto', $id)->decrement('existencias', $cantidad);
            }
            $total = DB::table('det_venta')->where('id_venta', $idVenta)->selectRaw('SUM(cantidad * precio_h) as total')->value('total');
            $recibido = $datos['metodo_pago'] === 'efectivo' ? $datos['monto_recibido'] : null;
            $cambio = null;
            if ($recibido !== null) {
                $pago = DB::selectOne('SELECT CAST(? AS DECIMAL(20,2)) >= CAST(? AS DECIMAL(20,2)) AS suficiente, CAST(? AS DECIMAL(20,2)) - CAST(? AS DECIMAL(20,2)) AS cambio', [$recibido, $total, $recibido, $total]);
                if (! $pago->suficiente) {
                    throw ValidationException::withMessages(['monto_recibido' => 'El efectivo recibido no cubre el total de la venta.']);
                }
                $cambio = $pago->cambio;
            }
            DB::table('venta')->where('id_venta', $idVenta)->update([
                'total' => $total, 'metodo_pago' => $datos['metodo_pago'],
                'monto_recibido' => $recibido, 'cambio' => $cambio,
            ]);
            return $idVenta;
        });
        $request->session()->forget(['carrito', 'cliente_venta']);
        return redirect()->route('ventas.ticket', $idVenta);
    }
    public function ticket(int $id)
    {
        $venta = DB::table('venta')->join('cliente', 'venta.id_cliente', '=', 'cliente.id_cliente')
            ->where('venta.id_venta', $id)->select('venta.*', 'cliente.nombre as cliente')->first();
        abort_unless($venta, 404);
        $detalles = DB::table('det_venta')->join('producto', 'det_venta.id_producto', '=', 'producto.id_producto')
            ->where('det_venta.id_venta', $id)->select('det_venta.*', 'producto.nombre')->get();
        return view('ticket', compact('venta', 'detalles'));
    }

}
