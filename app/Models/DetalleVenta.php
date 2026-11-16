<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    protected $table = 'det_venta';
    protected $primaryKey = 'id_detalle';
    public $timestamps = false;
    protected $fillable = ['id_producto', 'id_venta', 'cantidad', 'precio_h'];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_venta', 'id_venta');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }
}
