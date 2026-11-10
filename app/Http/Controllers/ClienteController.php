<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $estado = $request->query('estado') === 'inactivos' ? 'inactivos' : 'activos';
        $clientes = DB::table('cliente')->where('estado', $estado === 'activos' ? 1 : 0)->orderBy('nombre')->get();
        return view('clientes', compact('clientes', 'estado'));
    }

    public function create()
    {
        return view('cliente-form', ['cliente' => null]);
    }

    public function edit(int $id)
    {
        $cliente = DB::table('cliente')->where('id_cliente', $id)->first();
        abort_unless($cliente, 404);
        return view('cliente-form', compact('cliente'));
    }

    private function datos(Request $request): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'direccion' => ['required', 'string', 'max:255'],
            'rfc' => ['required', 'string', 'max:13'],
            'estado' => ['required', Rule::in(['1', '0'])],
        ]);
    }

    public function store(Request $request)
    {
        DB::table('cliente')->insert($this->datos($request));
        return redirect()->route('clientes.index')->with('mensaje', 'Cliente creado.');
    }

    public function update(Request $request, int $id)
    {
        abort_unless(DB::table('cliente')->where('id_cliente', $id)->exists(), 404);
        DB::table('cliente')->where('id_cliente', $id)->update($this->datos($request));
        return redirect()->route('clientes.index')->with('mensaje', 'Cliente actualizado.');
    }

    public function destroy(int $id)
    {
        abort_unless(DB::table('cliente')->where('id_cliente', $id)->exists(), 404);
        DB::table('cliente')->where('id_cliente', $id)->update(['estado' => 0]);
        return redirect()->route('clientes.index')->with('mensaje', 'Cliente desactivado.');
    }
}
