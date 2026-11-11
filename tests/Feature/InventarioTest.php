<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class InventarioTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Schema::create('proveedor', function (Blueprint $table) {
            $table->id('id_proveedor');
            $table->string('razon_social');
            $table->string('rfc');
            $table->string('direccion');
            $table->string('telefono');
            $table->boolean('estado');
        });
        Schema::create('cliente', function (Blueprint $table) {
            $table->id('id_cliente');
            $table->string('nombre');
            $table->string('direccion');
            $table->string('rfc');
            $table->boolean('estado');
        });
        Schema::create('producto', function (Blueprint $table) {
            $table->id('id_producto');
            $table->string('nombre');
            $table->decimal('precio', 10, 2);
            $table->integer('existencias');
            $table->boolean('estado');
            $table->unsignedBigInteger('id_proveedor');
            $table->string('u_medida');
        });
    }

    public function test_crud_y_desactivacion_logica(): void
    {
        foreach (['proveedor' => ['razon_social' => 'Proveedor', 'telefono' => '123'], 'cliente' => ['nombre' => 'Cliente']] as $tipo => $campos) {
            $ruta = $tipo === 'proveedor' ? 'proveedores' : 'clientes';
            $nombre = $tipo === 'proveedor' ? 'razon_social' : 'nombre';
            $datos = $campos + ['rfc' => 'XAXX010101000', 'direccion' => 'Dirección', 'estado' => '1'];
            $this->post('/'.$ruta, $datos)->assertRedirect('/'.$ruta);
            $id = DB::table($tipo)->value('id_'.$tipo);
            $this->put('/'.$ruta.'/'.$id, array_merge($datos, [$nombre => 'Editado']))->assertRedirect('/'.$ruta);
            $this->delete('/'.$ruta.'/'.$id)->assertRedirect('/'.$ruta);
            $this->assertDatabaseHas($tipo, ['id_'.$tipo => $id, $nombre => 'Editado', 'estado' => 0]);
            $this->get('/'.$ruta)->assertDontSee('Editado');
            $this->get('/'.$ruta.'?estado=inactivos')->assertSee('Editado')->assertSee('Activos (0)')->assertSee('Inactivos (1)');
            $this->put('/'.$ruta.'/'.$id, $datos)->assertRedirect('/'.$ruta);
            $this->assertDatabaseHas($tipo, ['id_'.$tipo => $id, 'estado' => 1]);
        }
        $producto = ['nombre' => 'Producto', 'precio' => '21.00', 'existencias' => 10, 'estado' => '1', 'id_proveedor' => DB::table('proveedor')->value('id_proveedor'), 'u_medida' => 'pieza'];
        $this->post('/productos', $producto)->assertRedirect('/');
        $id = DB::table('producto')->value('id_producto');
        $this->delete('/productos/'.$id)->assertRedirect('/');
        $this->assertDatabaseHas('producto', ['id_producto' => $id, 'estado' => 0]);
        $this->get('/')->assertDontSee('Producto</td>', false);
        $this->get('/?estado=inactivos')->assertSee('Producto</td>', false)->assertSee('Activos (0)')->assertSee('Inactivos (1)');
    }
}
