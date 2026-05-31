<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('cuentas')) {
            return;
        }

        DB::table('cuentas')->where('tipo', 'ingreso')->update(['tipo' => 'ganancia']);
        DB::table('cuentas')->where('tipo', 'egreso')->update(['tipo' => 'perdida']);
        DB::table('cuentas')->where('tipo', 'patrimonio')->update(['tipo' => 'pasivo']);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE cuentas MODIFY tipo ENUM('activo', 'pasivo', 'perdida', 'ganancia') NOT NULL");
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('cuentas')) {
            return;
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE cuentas MODIFY tipo ENUM('activo', 'pasivo', 'patrimonio', 'ingreso', 'egreso') NOT NULL");
        }

        DB::table('cuentas')->where('tipo', 'ganancia')->update(['tipo' => 'ingreso']);
        DB::table('cuentas')->where('tipo', 'perdida')->update(['tipo' => 'egreso']);
    }
};
