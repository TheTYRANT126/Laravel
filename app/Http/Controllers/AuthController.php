<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function create()
    {
        return view('login');
    }

    public function store(Request $request)
    {
        $credenciales = $request->validate([
            'identificador' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ]);
        $campo = str_contains($credenciales['identificador'], '@') ? 'email' : 'username';
        if (! Auth::attempt([$campo => $credenciales['identificador'], 'password' => $credenciales['password'], 'activo' => true])) {
            throw ValidationException::withMessages(['identificador' => 'El usuario o la contraseña son incorrectos, o la cuenta está desactivada.']);
        }
        $request->session()->regenerate();
        return redirect()->intended(route('productos.index'));
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
