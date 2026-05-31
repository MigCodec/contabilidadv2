<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('cuentas', 'subtipo_codigo')) {
            Schema::table('cuentas', function (Blueprint $table) {
                $table->unsignedSmallInteger('subtipo_codigo')->default(0)->after('tipo');
            });
        }

        DB::table('cuentas')
            ->orderBy('id')
            ->get(['id', 'tipo', 'cuenta_padre_id'])
            ->each(function (object $cuenta): void {
                DB::table('cuentas')
                    ->where('id', $cuenta->id)
                    ->update([
                        'codigo' => implode('.', [
                            $this->tipoCodigo((string) $cuenta->tipo),
                            0,
                            $cuenta->cuenta_padre_id ?: 0,
                            $cuenta->id,
                        ]),
                    ]);
            });
    }

    public function down(): void
    {
        if (Schema::hasColumn('cuentas', 'subtipo_codigo')) {
            Schema::table('cuentas', function (Blueprint $table) {
                $table->dropColumn('subtipo_codigo');
            });
        }
    }

    private function tipoCodigo(string $tipo): int
    {
        return match ($tipo) {
            'activo' => 1,
            'pasivo' => 2,
            'perdida' => 3,
            'ganancia' => 4,
            default => 0,
        };
    }
};
