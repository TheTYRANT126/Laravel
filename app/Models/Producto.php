<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'producto';
    protected $primaryKey = 'id_producto';
    public $timestamps = false;
    protected $fillable = ['nombre', 'precio', 'existencias', 'estado', 'id_proveedor', 'u_medida'];

    public static function totalesPorEstado()
    {
        return static::selectRaw('estado, COUNT(*) as total')->groupBy('estado')->pluck('total', 'estado');
    }

    public static function listarPorEstado(int $estado)
    {
        return static::with('proveedor')->where('estado', $estado)->orderBy('id_producto')->get();
    }

    public static function disponibles()
    {
        return static::where('estado', 1)->where('existencias', '>', 0)->orderBy('nombre')->get();
    }

    public static function delCarrito(array $carrito)
    {
        return static::whereIn('id_producto', array_keys($carrito))->orderBy('nombre')->get();
    }

    public static function activo(int $id)
    {
        return static::where('id_producto', $id)->where('estado', 1)->first();
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor', 'id_proveedor');
    }

    public function detallesVenta()
    {
        return $this->hasMany(DetalleVenta::class, 'id_producto', 'id_producto');
    }
}
