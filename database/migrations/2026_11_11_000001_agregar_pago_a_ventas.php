<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('venta', function (Blueprint $table) {
            $table->string('metodo_pago', 20)->nullable();
            $table->decimal('monto_recibido', 20, 2)->nullable();
            $table->decimal('cambio', 20, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('venta', function (Blueprint $table) {
            $table->dropColumn(['metodo_pago', 'monto_recibido', 'cambio']);
        });
    }
};
