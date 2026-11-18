<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class AccesoSinBaseTest extends TestCase
{
    public function test_invitado_debe_iniciar_sesion(): void
    {
        $this->get('/')->assertRedirect('/login');
        $this->get('/ventas')->assertRedirect('/login');
        $this->get('/login')->assertOk()->assertSee('Iniciar sesión');
    }

    public function test_solo_el_administrador_puede_administrar(): void
    {
        $admin = (new User)->forceFill(['role' => 'admin', 'activo' => true]);
        $empleado = (new User)->forceFill(['role' => 'empleado', 'activo' => true]);
        $this->assertTrue(Gate::forUser($admin)->allows('administrar'));
        $this->assertFalse(Gate::forUser($empleado)->allows('administrar'));
        $this->actingAs($empleado)->get('/usuarios')->assertForbidden();
        $this->actingAs($empleado)->get('/productos/crear')->assertForbidden();
        $this->actingAs($empleado)->post('/productos', [])->assertForbidden();
        $this->actingAs($empleado)->get('/clientes')->assertForbidden();
        $this->actingAs($empleado)->get('/proveedores')->assertForbidden();
    }
    public function test_usuario_desactivado_pierde_el_acceso(): void
    {
        $empleado = (new User)->forceFill(['id' => 1, 'role' => 'empleado', 'activo' => false]);
        $this->actingAs($empleado)->get('/ventas')->assertRedirect('/login');
        $this->assertGuest();
    }

}
