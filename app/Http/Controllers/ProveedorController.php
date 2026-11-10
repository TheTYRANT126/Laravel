<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        $estado = $request->query('estado') === 'inactivos' ? 'inactivos' : 'activos';
        $proveedores = DB::table('proveedor')->where('estado', $estado === 'activos' ? 1 : 0)->orderBy('razon_social')->get();
        return view('proveedores', compact('proveedores', 'estado'));
    }

    public function create()
    {
        return view('proveedor-form', ['proveedor' => null]);
    }

    public function edit(int $id)
    {
        $proveedor = DB::table('proveedor')->where('id_proveedor', $id)->first();
        abort_unless($proveedor, 404);
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
        DB::table('proveedor')->insert($this->datos($request));
        return redirect()->route('proveedores.index')->with('mensaje', 'Proveedor creado.');
    }

    public function update(Request $request, int $id)
    {
        abort_unless(DB::table('proveedor')->where('id_proveedor', $id)->exists(), 404);
        DB::table('proveedor')->where('id_proveedor', $id)->update($this->datos($request));
        return redirect()->route('proveedores.index')->with('mensaje', 'Proveedor actualizado.');
    }

    public function destroy(int $id)
    {
        abort_unless(DB::table('proveedor')->where('id_proveedor', $id)->exists(), 404);
        DB::table('proveedor')->where('id_proveedor', $id)->update(['estado' => 0]);
        return redirect()->route('proveedores.index')->with('mensaje', 'Proveedor desactivado.');
    }
}
