<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class VentaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Schema::create('cliente', function (Blueprint $table) {
            $table->id('id_cliente');
            $table->string('nombre');
            $table->boolean('estado');
        });
        Schema::create('producto', function (Blueprint $table) {
            $table->id('id_producto');
            $table->string('nombre');
            $table->decimal('precio', 10, 2);
            $table->integer('existencias');
            $table->boolean('estado');
            $table->string('u_medida');
        });
        Schema::create('venta', function (Blueprint $table) {
            $table->id('id_venta');
            $table->dateTime('fecha_venta');
            $table->integer('id_cliente');
            $table->decimal('total', 20, 2)->default(0);
            $table->string('metodo_pago')->nullable();
            $table->decimal('monto_recibido', 20, 2)->nullable();
            $table->decimal('cambio', 20, 2)->nullable();
        });
        Schema::create('det_venta', function (Blueprint $table) {
            $table->id('id_detalle');
            $table->integer('id_venta');
            $table->integer('id_producto');
            $table->integer('cantidad');
            $table->decimal('precio_h', 10, 2);
        });
        DB::table('cliente')->insert(['id_cliente' => 1, 'nombre' => 'Ana', 'estado' => 1]);
        DB::table('producto')->insert(['id_producto' => 1, 'nombre' => 'Cuaderno', 'precio' => '12.50', 'existencias' => 5, 'estado' => 1, 'u_medida' => 'pieza']);
    }

    public function test_compra_registra_detalle_total_y_descuenta_existencias(): void
    {
        $this->post('/ventas/cliente', ['id_cliente' => 1])->assertRedirect('/ventas');
        $this->post('/ventas/carrito', ['id_producto' => 1, 'cantidad' => 2])->assertRedirect('/ventas');
        $this->post('/ventas/comprar', ['metodo_pago' => 'efectivo', 'monto_recibido' => '30.00'])->assertRedirect('/ventas/1/ticket');
        $this->assertDatabaseHas('venta', ['id_cliente' => 1, 'total' => 25, 'metodo_pago' => 'efectivo', 'monto_recibido' => 30, 'cambio' => 5]);
        $this->assertDatabaseHas('det_venta', ['id_producto' => 1, 'cantidad' => 2, 'precio_h' => 12.5]);
        $this->assertDatabaseHas('producto', ['id_producto' => 1, 'existencias' => 3]);
        $this->get('/ventas')->assertSee('Ana')->assertSee('25.00');
        $this->get('/ventas/1/ticket')->assertOk()->assertSee('Ana')->assertSee('Cambio: $5.00');
    }

    public function test_compra_sin_existencias_no_guarda_venta(): void
    {
        $this->post('/ventas/cliente', ['id_cliente' => 1]);
        $this->post('/ventas/carrito', ['id_producto' => 1, 'cantidad' => 2]);
        DB::table('producto')->where('id_producto', 1)->update(['existencias' => 1]);
        $this->post('/ventas/comprar', ['metodo_pago' => 'efectivo', 'monto_recibido' => '30.00'])->assertSessionHasErrors('venta');
        $this->assertDatabaseCount('venta', 0);
        $this->assertDatabaseCount('det_venta', 0);
        $this->assertDatabaseHas('producto', ['id_producto' => 1, 'existencias' => 1]);
    }
    public function test_pago_insuficiente_no_registra_venta_ni_descuenta_inventario(): void
    {
        $this->post('/ventas/cliente', ['id_cliente' => 1]);
        $this->post('/ventas/carrito', ['id_producto' => 1, 'cantidad' => 2]);
        $this->post('/ventas/comprar', ['metodo_pago' => 'efectivo', 'monto_recibido' => '24.99'])->assertSessionHasErrors('monto_recibido');
        $this->assertDatabaseCount('venta', 0);
        $this->assertDatabaseCount('det_venta', 0);
        $this->assertDatabaseHas('producto', ['id_producto' => 1, 'existencias' => 5]);
    }

    public function test_pago_con_tarjeta_no_exige_efectivo(): void
    {
        $this->post('/ventas/cliente', ['id_cliente' => 1]);
        $this->post('/ventas/carrito', ['id_producto' => 1, 'cantidad' => 1]);
        $this->post('/ventas/comprar', ['metodo_pago' => 'tarjeta'])->assertRedirect('/ventas/1/ticket');
        $this->assertDatabaseHas('venta', ['metodo_pago' => 'tarjeta', 'monto_recibido' => null, 'cambio' => null]);
    }

    public function test_cada_compra_del_mismo_cliente_tiene_su_propio_ticket(): void
    {
        foreach ([1, 2] as $cantidad) {
            $this->post('/ventas/cliente', ['id_cliente' => 1]);
            $this->post('/ventas/carrito', ['id_producto' => 1, 'cantidad' => $cantidad]);
            $this->post('/ventas/comprar', ['metodo_pago' => 'tarjeta'])->assertRedirect('/ventas/'.$cantidad.'/ticket');
        }
        $this->assertDatabaseCount('venta', 2);
        $this->get('/ventas/1/ticket')->assertOk()->assertSee('12.50')->assertDontSee('25.00');
        $this->get('/ventas/2/ticket')->assertOk()->assertSee('25.00');
    }

}
