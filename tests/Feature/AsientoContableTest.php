<?php

namespace Tests\Feature;

use App\Models\Cuenta;
use App\Services\RegistrarAsientoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AsientoContableTest extends TestCase
{
    use RefreshDatabase;

    public function test_genera_codigo_de_cuenta_automaticamente_sin_padre(): void
    {
        $cuenta = Cuenta::create([
            'nombre' => 'Caja',
            'tipo' => 'activo',
            'subtipo_codigo' => 0,
        ]);

        $this->assertSame("1.0.0.{$cuenta->id}", $cuenta->fresh()->codigo);
    }

    public function test_genera_codigo_de_cuenta_automaticamente_con_padre(): void
    {
        $padre = Cuenta::create([
            'nombre' => 'Activos fijos',
            'tipo' => 'activo',
            'subtipo_codigo' => 2,
            'acepta_movimientos' => false,
        ]);

        $hija = Cuenta::create([
            'cuenta_padre_id' => $padre->id,
            'nombre' => 'Vehiculos',
            'tipo' => 'activo',
            'subtipo_codigo' => 2,
        ]);

        $this->assertSame("1.2.{$padre->id}.{$hija->id}", $hija->fresh()->codigo);
    }

    public function test_registra_un_asiento_balanceado(): void
    {
        $caja = Cuenta::create([
            'nombre' => 'Caja',
            'tipo' => 'activo',
        ]);

        $ganancias = Cuenta::create([
            'nombre' => 'Ganancias por ventas',
            'tipo' => 'ganancia',
        ]);

        $asiento = app(RegistrarAsientoService::class)->crear([
            'fecha' => '2026-05-31',
            'glosa' => 'Venta contado',
            'user_id' => null,
            'detalles' => [
                ['cuenta_id' => $caja->id, 'debe' => '1000.00', 'haber' => '0'],
                ['cuenta_id' => $ganancias->id, 'debe' => '0', 'haber' => '1000.00'],
            ],
        ]);

        $this->assertDatabaseHas('asientos', [
            'id' => $asiento->id,
            'glosa' => 'Venta contado',
            'estado' => 'registrado',
        ]);
        $this->assertCount(2, $asiento->detalles);
    }

    public function test_rechaza_un_asiento_descuadrado(): void
    {
        $caja = Cuenta::create([
            'nombre' => 'Caja',
            'tipo' => 'activo',
        ]);

        $ganancias = Cuenta::create([
            'nombre' => 'Ganancias por ventas',
            'tipo' => 'ganancia',
        ]);

        $this->expectException(ValidationException::class);

        try {
            app(RegistrarAsientoService::class)->crear([
                'fecha' => '2026-05-31',
                'glosa' => 'Venta descuadrada',
                'user_id' => null,
                'detalles' => [
                    ['cuenta_id' => $caja->id, 'debe' => '1000.00', 'haber' => '0'],
                    ['cuenta_id' => $ganancias->id, 'debe' => '0', 'haber' => '900.00'],
                ],
            ]);
        } catch (ValidationException $exception) {
            $this->assertDatabaseCount('asientos', 0);

            throw $exception;
        }

        $this->fail('El asiento descuadrado debio ser rechazado.');
    }

    public function test_no_permite_movimientos_en_cuentas_inactivas(): void
    {
        $cuentaInactiva = Cuenta::create([
            'nombre' => 'Cuenta inactiva',
            'tipo' => 'activo',
            'activa' => false,
        ]);

        $ganancias = Cuenta::create([
            'nombre' => 'Ganancias por ventas',
            'tipo' => 'ganancia',
        ]);

        $this->expectException(ValidationException::class);

        try {
            app(RegistrarAsientoService::class)->crear([
                'fecha' => '2026-05-31',
                'glosa' => 'Movimiento no permitido',
                'user_id' => null,
                'detalles' => [
                    ['cuenta_id' => $cuentaInactiva->id, 'debe' => '1000.00', 'haber' => '0'],
                    ['cuenta_id' => $ganancias->id, 'debe' => '0', 'haber' => '1000.00'],
                ],
            ]);
        } catch (ValidationException $exception) {
            $this->assertDatabaseCount('asientos', 0);

            throw $exception;
        }

        $this->fail('El movimiento en cuenta inactiva debio ser rechazado.');
    }
}
