<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Tests\TestCase;

class LimpiezaTablasTest extends TestCase
{
    public function test_no_elimina_tablas_si_contienen_datos(): void
    {
        config(['session.driver' => 'file', 'queue.default' => 'sync']);
        Schema::create('users', fn (Blueprint $table) => $table->id());
        DB::table('users')->insert(['id' => 1]);
        $migracion = require database_path('migrations/2026_11_09_000001_eliminar_tablas_no_utilizadas.php');

        try {
            $migracion->up();
            $this->fail('La migración debe proteger los datos existentes.');
        } catch (RuntimeException $error) {
            $this->assertStringContainsString('users', $error->getMessage());
            $this->assertDatabaseHas('users', ['id' => 1]);
        }
    }
}
