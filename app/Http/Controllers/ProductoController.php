<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Validation\Rule;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $estado = $request->query('estado') === 'inactivos' ? 'inactivos' : 'activos';
        $totales = Producto::totalesPorEstado();
        $productos = Producto::listarPorEstado($estado === 'activos' ? 1 : 0);
        return view('productos', compact('productos', 'estado', 'totales'));
    }

    public function create()
    {
        return view('producto-form', ['producto' => null, 'proveedores' => Proveedor::disponibles()]);
    }

    public function edit(int $id)
    {
        $producto = Producto::findOrFail($id);
        return view('producto-form', ['producto' => $producto, 'proveedores' => Proveedor::disponibles($producto->id_proveedor)]);
    }

    private function datos(Request $request, ?int $proveedorActual = null): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'precio' => ['required', 'regex:/^\d{1,8}(\.\d{1,2})?$/'],
            'existencias' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'estado' => ['required', Rule::in(['1', '0'])],
            'id_proveedor' => ['required', 'integer', Rule::exists(Proveedor::class, 'id_proveedor')->where(fn ($query) => $query->where('estado', 1)->orWhere('id_proveedor', $proveedorActual))],
            'u_medida' => ['required', 'string', 'max:30'],
        ]);
    }

    public function store(Request $request)
    {
        Producto::create($this->datos($request));
        return redirect()->route('productos.index')->with('mensaje', 'Producto creado.');
    }

    public function update(Request $request, int $id)
    {
        $producto = Producto::findOrFail($id);
        $producto->update($this->datos($request, $producto->id_proveedor));
        return redirect()->route('productos.index')->with('mensaje', 'Producto actualizado.');
    }

    public function destroy(int $id)
    {
        Producto::findOrFail($id)->update(['estado' => 0]);
        return redirect()->route('productos.index')->with('mensaje', 'Producto desactivado.');
    }
}
