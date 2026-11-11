<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (config('session.driver') === 'database' || config('queue.default') !== 'sync') {
            throw new RuntimeException('Configura SESSION_DRIVER=file y QUEUE_CONNECTION=sync antes de eliminar las tablas.');
        }

        $tablas = ['users', 'password_reset_tokens', 'sessions', 'jobs', 'job_batches', 'failed_jobs'];
        foreach ($tablas as $tabla) {
            if (Schema::hasTable($tabla) && DB::table($tabla)->exists()) {
                throw new RuntimeException("La tabla {$tabla} contiene datos; revisa y respáldalos antes de eliminarla.");
            }
        }
        foreach ($tablas as $tabla) {
            Schema::dropIfExists($tabla);
        }
    }

    public function down(): void
    {
        (require __DIR__.'/0001_01_01_000000_create_users_table.php')->up();
        (require __DIR__.'/0001_01_01_000002_create_jobs_table.php')->up();
    }
};
