<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['proveedor', 'cliente'] as $tabla) {
            $valores = DB::table($tabla)->distinct()->pluck('estado');
            foreach ($valores as $valor) {
                if (! in_array(mb_strtolower(trim((string) $valor)), ['activo', 'inactivo', '1', '0'], true)) {
                    throw new RuntimeException("Estado desconocido en {$tabla}: {$valor}. Corrígelo antes de migrar.");
                }
            }
        }
        foreach (['proveedor', 'cliente'] as $tabla) {
            DB::table($tabla)->whereRaw("LOWER(TRIM(estado)) IN ('activo', '1')")->update(['estado' => '1']);
            DB::table($tabla)->whereRaw("LOWER(TRIM(estado)) IN ('inactivo', '0')")->update(['estado' => '0']);
            Schema::table($tabla, function (Blueprint $table) {
                $table->boolean('estado')->default(true)->change();
            });
        }
    }

    public function down(): void
    {
        foreach (['proveedor', 'cliente'] as $tabla) {
            Schema::table($tabla, function (Blueprint $table) {
                $table->string('estado', 30)->change();
            });
            DB::table($tabla)->where('estado', '1')->update(['estado' => 'Activo']);
            DB::table($tabla)->where('estado', '0')->update(['estado' => 'Inactivo']);
        }
    }
};
