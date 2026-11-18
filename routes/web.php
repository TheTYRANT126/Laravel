<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\ProveedorController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
});

Route::middleware(['auth', \App\Http\Middleware\VerificarUsuarioActivo::class])->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/usuarios', [UsuarioController::class, 'index'])->middleware('can:administrar')->name('usuarios.index');
    Route::post('/usuarios', [UsuarioController::class, 'store'])->middleware('can:administrar')->name('usuarios.store');
    Route::get('/usuarios/{id}/editar', [UsuarioController::class, 'edit'])->middleware('can:administrar')->name('usuarios.edit');
    Route::put('/usuarios/{id}', [UsuarioController::class, 'update'])->middleware('can:administrar')->name('usuarios.update');
    Route::patch('/usuarios/{id}/estado', [UsuarioController::class, 'estado'])->middleware('can:administrar')->name('usuarios.estado');
Route::get('/', [ProductoController::class, 'index'])->name('productos.index');
Route::get('/productos/crear', [ProductoController::class, 'create'])->middleware('can:administrar')->name('productos.create');
Route::post('/productos', [ProductoController::class, 'store'])->middleware('can:administrar')->name('productos.store');
Route::get('/productos/{id}/editar', [ProductoController::class, 'edit'])->middleware('can:administrar')->name('productos.edit');
Route::put('/productos/{id}', [ProductoController::class, 'update'])->middleware('can:administrar')->name('productos.update');
Route::delete('/productos/{id}', [ProductoController::class, 'destroy'])->middleware('can:administrar')->name('productos.destroy');
Route::get('/proveedores', [ProveedorController::class, 'index'])->middleware('can:administrar')->name('proveedores.index');
Route::get('/proveedores/crear', [ProveedorController::class, 'create'])->middleware('can:administrar')->name('proveedores.create');
Route::post('/proveedores', [ProveedorController::class, 'store'])->middleware('can:administrar')->name('proveedores.store');

Route::get('/proveedores/{id}/editar', [ProveedorController::class, 'edit'])->middleware('can:administrar')->name('proveedores.edit');
Route::put('/proveedores/{id}', [ProveedorController::class, 'update'])->middleware('can:administrar')->name('proveedores.update');
Route::delete('/proveedores/{id}', [ProveedorController::class, 'destroy'])->middleware('can:administrar')->name('proveedores.destroy');
Route::get('/clientes', [ClienteController::class, 'index'])->middleware('can:administrar')->name('clientes.index');
Route::get('/clientes/crear', [ClienteController::class, 'create'])->middleware('can:administrar')->name('clientes.create');
Route::post('/clientes', [ClienteController::class, 'store'])->middleware('can:administrar')->name('clientes.store');
Route::get('/clientes/{id}/editar', [ClienteController::class, 'edit'])->middleware('can:administrar')->name('clientes.edit');
Route::put('/clientes/{id}', [ClienteController::class, 'update'])->middleware('can:administrar')->name('clientes.update');
Route::delete('/clientes/{id}', [ClienteController::class, 'destroy'])->middleware('can:administrar')->name('clientes.destroy');

Route::get('/ventas', [VentaController::class, 'index'])->name('ventas.index');
Route::post('/ventas/cliente', [VentaController::class, 'cliente'])->name('ventas.cliente');
Route::post('/ventas/carrito', [VentaController::class, 'agregar'])->name('ventas.agregar');
Route::delete('/ventas/carrito/{id}', [VentaController::class, 'quitar'])->name('ventas.quitar');
Route::post('/ventas/nueva', [VentaController::class, 'nueva'])->name('ventas.nueva');
Route::post('/ventas/comprar', [VentaController::class, 'comprar'])->name('ventas.comprar');

Route::get('/ventas/{id}/ticket', [VentaController::class, 'ticket'])->name('ventas.ticket');

});
