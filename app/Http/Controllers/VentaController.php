<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class VentaController extends Controller
{
    public function index(Request $request)
    {
        $productos = Producto::disponibles();
        $clientes = Cliente::activos();
        $carrito = $request->session()->get('carrito', []);
        $articulos = Producto::delCarrito($carrito);
        $ventas = Venta::historial();
        return view('ventas', compact('productos', 'clientes', 'carrito', 'articulos', 'ventas'));
    }

    public function cliente(Request $request)
    {
        $datos = $request->validate(['id_cliente' => ['required', 'integer', Rule::exists(Cliente::class, 'id_cliente')->where('estado', 1)]]);
        $request->session()->put('cliente_venta', $datos['id_cliente']);
        return redirect()->route('ventas.index');
    }

    public function agregar(Request $request)
    {
        $datos = $request->validate(['id_producto' => 'required|integer', 'cantidad' => 'required|integer|min:1|max:4294967295']);
        $producto = Producto::activo($datos['id_producto']);
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
        $idVenta = Venta::registrar((int) $cliente, $carrito, $datos);
        $request->session()->forget(['carrito', 'cliente_venta']);
        return redirect()->route('ventas.ticket', $idVenta);
    }
    public function ticket(int $id)
    {
        $venta = Venta::ticket($id);
        $detalles = $venta->detalles;
        return view('ticket', compact('venta', 'detalles'));
    }

}
