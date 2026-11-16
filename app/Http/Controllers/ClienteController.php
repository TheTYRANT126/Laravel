<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use Illuminate\Validation\Rule;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $estado = $request->query('estado') === 'inactivos' ? 'inactivos' : 'activos';
        $totales = Cliente::totalesPorEstado();
        $clientes = Cliente::listarPorEstado($estado === 'activos' ? 1 : 0);
        return view('clientes', compact('clientes', 'estado', 'totales'));
    }

    public function create()
    {
        return view('cliente-form', ['cliente' => null]);
    }

    public function edit(int $id)
    {
        $cliente = Cliente::findOrFail($id);
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
        Cliente::create($this->datos($request));
        return redirect()->route('clientes.index')->with('mensaje', 'Cliente creado.');
    }

    public function update(Request $request, int $id)
    {
        Cliente::findOrFail($id)->update($this->datos($request));
        return redirect()->route('clientes.index')->with('mensaje', 'Cliente actualizado.');
    }

    public function destroy(int $id)
    {
        Cliente::findOrFail($id)->update(['estado' => 0]);
        return redirect()->route('clientes.index')->with('mensaje', 'Cliente desactivado.');
    }
}
