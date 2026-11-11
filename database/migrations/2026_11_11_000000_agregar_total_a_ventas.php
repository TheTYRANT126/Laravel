<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('venta', 'total')) {
            Schema::table('venta', fn (Blueprint $table) => $table->decimal('total', 20, 2)->default(0));
            DB::statement('UPDATE venta SET total = COALESCE((SELECT SUM(cantidad * precio_h) FROM det_venta WHERE det_venta.id_venta = venta.id_venta), 0)');
        }
    }

    public function down(): void
    {
        Schema::table('venta', fn (Blueprint $table) => $table->dropColumn('total'));
    }
};
