<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $estado = $request->query('estado') === 'inactivos' ? 'inactivos' : 'activos';
        $productos = DB::table('producto')->join('proveedor', 'producto.id_proveedor', '=', 'proveedor.id_proveedor')
            ->where('producto.estado', $estado === 'activos' ? 1 : 0)
            ->orderBy('producto.id_producto')->select('producto.*', 'proveedor.razon_social as proveedor')->get();
        return view('productos', compact('productos', 'estado'));
    }

    public function create()
    {
        return view('producto-form', ['producto' => null, 'proveedores' => DB::table('proveedor')->where('estado', 1)->orderBy('razon_social')->get()]);
    }

    public function edit(int $id)
    {
        $producto = DB::table('producto')->where('id_producto', $id)->first();
        abort_unless($producto, 404);
        return view('producto-form', ['producto' => $producto, 'proveedores' => DB::table('proveedor')->where('estado', 1)->orWhere('id_proveedor', $producto->id_proveedor)->orderBy('razon_social')->get()]);
    }

    private function datos(Request $request, ?int $proveedorActual = null): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'precio' => ['required', 'regex:/^\d{1,8}(\.\d{1,2})?$/'],
            'existencias' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'estado' => ['required', Rule::in(['1', '0'])],
            'id_proveedor' => ['required', 'integer', Rule::exists('proveedor', 'id_proveedor')->where(fn ($query) => $query->where('estado', 1)->orWhere('id_proveedor', $proveedorActual))],
            'u_medida' => ['required', 'string', 'max:30'],
        ]);
    }

    public function store(Request $request)
    {
        DB::table('producto')->insert($this->datos($request));
        return redirect()->route('productos.index')->with('mensaje', 'Producto creado.');
    }

    public function update(Request $request, int $id)
    {
        $producto = DB::table('producto')->where('id_producto', $id)->first();
        abort_unless($producto, 404);
        DB::table('producto')->where('id_producto', $id)->update($this->datos($request, $producto->id_proveedor));
        return redirect()->route('productos.index')->with('mensaje', 'Producto actualizado.');
    }

    public function destroy(int $id)
    {
        abort_unless(DB::table('producto')->where('id_producto', $id)->exists(), 404);
        DB::table('producto')->where('id_producto', $id)->update(['estado' => 0]);
        return redirect()->route('productos.index')->with('mensaje', 'Producto desactivado.');
    }
}
