<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedor';
    protected $primaryKey = 'id_proveedor';
    public $timestamps = false;
    protected $fillable = ['razon_social', 'rfc', 'direccion', 'telefono', 'estado'];

    public static function totalesPorEstado()
    {
        return static::selectRaw('estado, COUNT(*) as total')->groupBy('estado')->pluck('total', 'estado');
    }

    public static function listarPorEstado(int $estado)
    {
        return static::where('estado', $estado)->orderBy('razon_social')->get();
    }

    public static function disponibles(?int $actual = null)
    {
        return static::where('estado', 1)->when($actual, fn ($query) => $query->orWhere('id_proveedor', $actual))->orderBy('razon_social')->get();
    }

    public function productos()
    {
        return $this->hasMany(Producto::class, 'id_proveedor', 'id_proveedor');
    }
}
