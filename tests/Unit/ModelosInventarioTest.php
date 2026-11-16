<?php

namespace Tests\Unit;

use App\Models\Cliente;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Venta;
use Tests\TestCase;

class ModelosInventarioTest extends TestCase
{
    public function test_modelos_usan_tablas_y_claves_existentes(): void
    {
        foreach ([
            [new Producto, 'producto', 'id_producto'],
            [new Proveedor, 'proveedor', 'id_proveedor'],
            [new Cliente, 'cliente', 'id_cliente'],
            [new Venta, 'venta', 'id_venta'],
            [new DetalleVenta, 'det_venta', 'id_detalle'],
        ] as [$modelo, $tabla, $clave]) {
            $this->assertSame($tabla, $modelo->getTable());
            $this->assertSame($clave, $modelo->getKeyName());
            $this->assertFalse($modelo->usesTimestamps());
        }
    }

    public function test_relaciones_apuntan_a_las_claves_de_la_base_existente(): void
    {
        $this->assertSame('id_proveedor', (new Producto)->proveedor()->getForeignKeyName());
        $this->assertSame('id_producto', (new Proveedor)->productos()->getRelated()->getKeyName());
        $this->assertSame('id_cliente', (new Venta)->cliente()->getForeignKeyName());
        $this->assertSame('id_venta', (new Venta)->detalles()->getForeignKeyName());
        $this->assertSame('id_producto', (new DetalleVenta)->producto()->getForeignKeyName());
        $this->assertSame('id_venta', (new DetalleVenta)->venta()->getForeignKeyName());
    }
}
