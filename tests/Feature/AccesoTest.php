<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AccesoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('username')->nullable()->unique();
            $table->boolean('activo')->default(true);
            $table->string('password');
            $table->string('role');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function test_login_y_cierre_de_sesion(): void
    {
        User::create(['name' => 'Admin', 'username' => 'admin', 'email' => 'admin@example.com', 'password' => 'clave-segura-123', 'role' => 'admin', 'activo' => true]);
        $this->post('/login', ['identificador' => 'admin', 'password' => 'incorrecta'])->assertSessionHasErrors('identificador');
        $this->assertGuest();
        $this->post('/login', ['identificador' => 'admin', 'password' => 'clave-segura-123'])->assertRedirect('/');
        $this->assertAuthenticated();
        $this->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_solo_admin_puede_crear_usuarios(): void
    {
        $empleado = User::create(['name' => 'Empleado', 'username' => 'empleado', 'email' => 'empleado@example.com', 'password' => 'clave-segura-123', 'role' => 'empleado', 'activo' => true]);
        $this->actingAs($empleado)->get('/usuarios')->assertForbidden();
        $this->actingAs($empleado)->post('/usuarios', ['name' => 'Otro', 'username' => 'otro', 'email' => 'otro@example.com', 'role' => 'admin', 'password' => 'clave-segura-456', 'password_confirmation' => 'clave-segura-456'])->assertForbidden();
        $admin = User::create(['name' => 'Admin', 'username' => 'admin', 'email' => 'admin@example.com', 'password' => 'clave-segura-123', 'role' => 'admin', 'activo' => true]);
        $this->actingAs($admin)->post('/usuarios', ['name' => 'Otro', 'username' => 'otro', 'email' => 'otro@example.com', 'role' => 'empleado', 'password' => 'clave-segura-456', 'password_confirmation' => 'clave-segura-456'])->assertRedirect('/usuarios');
        $this->assertTrue(Hash::check('clave-segura-456', User::where('email', 'otro@example.com')->value('password')));
    }
    public function test_admin_puede_editar_y_desactivar_empleado(): void
    {
        $admin = User::create(['name' => 'Admin', 'username' => 'admin', 'email' => 'admin@example.com', 'password' => '123456', 'role' => 'admin', 'activo' => true]);
        $empleado = User::create(['name' => 'Empleado', 'username' => 'empleado', 'email' => 'empleado@example.com', 'password' => 'e123456', 'role' => 'empleado', 'activo' => true]);
        $this->actingAs($admin)->put('/usuarios/'.$empleado->id, ['name' => 'Empleado editado', 'username' => 'empleado', 'email' => 'empleado@example.com', 'password' => '', 'password_confirmation' => ''])->assertRedirect('/usuarios');
        $this->assertTrue(Hash::check('e123456', $empleado->fresh()->password));
        $this->actingAs($admin)->patch('/usuarios/'.$empleado->id.'/estado')->assertRedirect('/usuarios');
        $this->assertDatabaseHas('users', ['id' => $empleado->id, 'activo' => false]);
        $this->post('/logout');
        $this->post('/login', ['identificador' => 'empleado', 'password' => 'e123456'])->assertSessionHasErrors('identificador');
        $this->assertGuest();
    }

    public function test_seeder_no_modifica_cuentas_existentes(): void
    {
        $admin = User::create(['name' => 'Admin existente', 'username' => 'admin', 'email' => 'otro@example.com', 'password' => 'mi-clave', 'role' => 'admin', 'activo' => true]);
        $this->seed(\Database\Seeders\UsuariosDemoSeeder::class);
        $this->assertTrue(Hash::check('mi-clave', $admin->fresh()->password));
        $this->assertDatabaseHas('users', ['username' => 'empleado', 'role' => 'empleado']);
    }

}
