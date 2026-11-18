<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UsuarioController extends Controller
{
    public function index()
    {
        return view('usuarios', ['usuarios' => User::listado()]);
    }

    public function store(Request $request)
    {
        $datos = $this->datos($request);
        User::create($datos + ['role' => 'empleado', 'activo' => true]);
        return redirect()->route('usuarios.index')->with('mensaje', 'Empleado creado.');
    }

    public function edit(int $id)
    {
        $usuario = $this->empleado($id);
        return view('usuario-form', compact('usuario'));
    }

    public function update(Request $request, int $id)
    {
        $usuario = $this->empleado($id);
        $datos = $this->datos($request, $usuario);
        if (empty($datos['password'])) {
            unset($datos['password']);
        }
        $usuario->update($datos);
        return redirect()->route('usuarios.index')->with('mensaje', 'Empleado actualizado.');
    }

    public function estado(int $id)
    {
        $usuario = $this->empleado($id);
        $usuario->update(['activo' => ! $usuario->activo]);
        return redirect()->route('usuarios.index')->with('mensaje', $usuario->activo ? 'Empleado activado.' : 'Empleado desactivado.');
    }

    private function empleado(int $id): User
    {
        $usuario = User::findOrFail($id);
        abort_unless($usuario->role === 'empleado', 403);
        return $usuario;
    }

    private function datos(Request $request, ?User $usuario = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'alpha_dash:ascii', 'max:50', Rule::unique('users')->ignore($usuario?->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($usuario?->id)],
            'password' => [$usuario ? 'nullable' : 'required', 'confirmed', Password::min(6)],
        ]);
    }
}
