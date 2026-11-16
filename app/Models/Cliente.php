<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'cliente';
    protected $primaryKey = 'id_cliente';
    public $timestamps = false;
    protected $fillable = ['nombre', 'direccion', 'rfc', 'estado'];

    public static function totalesPorEstado()
    {
        return static::selectRaw('estado, COUNT(*) as total')->groupBy('estado')->pluck('total', 'estado');
    }

    public static function listarPorEstado(int $estado)
    {
        return static::where('estado', $estado)->orderBy('nombre')->get();
    }

    public static function activos()
    {
        return static::where('estado', 1)->orderBy('nombre')->get();
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'id_cliente', 'id_cliente');
    }
}
