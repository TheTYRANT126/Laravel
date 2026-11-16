<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proveedor;
use Illuminate\Validation\Rule;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        $estado = $request->query('estado') === 'inactivos' ? 'inactivos' : 'activos';
        $totales = Proveedor::totalesPorEstado();
        $proveedores = Proveedor::listarPorEstado($estado === 'activos' ? 1 : 0);
        return view('proveedores', compact('proveedores', 'estado', 'totales'));
    }

    public function create()
    {
        return view('proveedor-form', ['proveedor' => null]);
    }

    public function edit(int $id)
    {
        $proveedor = Proveedor::findOrFail($id);
        return view('proveedor-form', compact('proveedor'));
    }

    private function datos(Request $request): array
    {
        return $request->validate([
            'razon_social' => ['required', 'string', 'max:150'],
            'rfc' => ['required', 'string', 'max:13'],
            'direccion' => ['required', 'string', 'max:255'],
            'telefono' => ['required', 'string', 'max:25'],
            'estado' => ['required', Rule::in(['1', '0'])],
        ]);
    }

    public function store(Request $request)
    {
        Proveedor::create($this->datos($request));
        return redirect()->route('proveedores.index')->with('mensaje', 'Proveedor creado.');
    }

    public function update(Request $request, int $id)
    {
        Proveedor::findOrFail($id)->update($this->datos($request));
        return redirect()->route('proveedores.index')->with('mensaje', 'Proveedor actualizado.');
    }

    public function destroy(int $id)
    {
        Proveedor::findOrFail($id)->update(['estado' => 0]);
        return redirect()->route('proveedores.index')->with('mensaje', 'Proveedor desactivado.');
    }
}
