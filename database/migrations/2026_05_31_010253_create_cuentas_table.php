<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cuentas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cuenta_padre_id')->nullable()->constrained('cuentas')->nullOnDelete();
            $table->string('codigo', 30)->unique();
            $table->string('nombre');
            $table->enum('tipo', ['activo', 'pasivo', 'perdida', 'ganancia']);
            $table->unsignedSmallInteger('subtipo_codigo')->default(0);
            $table->boolean('acepta_movimientos')->default(true);
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuentas');
    }
};
