<?php

namespace Tests\Feature;

use App\Models\Cuenta;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\RegistrarAsientoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportesContablesTest extends TestCase
{
    use RefreshDatabase;

    public function test_balance_general_muestra_ocho_columnas_calculadas_por_anio(): void
    {
        $user = $this->usuarioConReportes();
        [$caja, $ventas] = $this->cuentasBase();

        app(RegistrarAsientoService::class)->crear([
            'fecha' => '2026-03-10',
            'glosa' => 'Venta contado',
            'user_id' => $user->id,
            'detalles' => [
                ['cuenta_id' => $caja->id, 'debe' => '1500.00', 'haber' => '0'],
                ['cuenta_id' => $ventas->id, 'debe' => '0', 'haber' => '1500.00'],
            ],
        ]);

        app(RegistrarAsientoService::class)->crear([
            'fecha' => '2025-03-10',
            'glosa' => 'Venta otro periodo',
            'user_id' => $user->id,
            'detalles' => [
                ['cuenta_id' => $caja->id, 'debe' => '900.00', 'haber' => '0'],
                ['cuenta_id' => $ventas->id, 'debe' => '0', 'haber' => '900.00'],
            ],
        ]);

        $this->actingAs($user)
            ->get(route('reportes.balance-general', ['anio' => 2026]))
            ->assertOk()
            ->assertSee('Balance general 8 columnas')
            ->assertSee('1.500,00')
            ->assertDontSee('900,00');
    }

    public function test_libro_mayor_y_balance_extendido_requieren_permiso_de_reportes(): void
    {
        $sinPermisos = User::factory()->create();
        $conReportes = $this->usuarioConReportes();

        $this->actingAs($sinPermisos)
            ->get(route('reportes.libro-mayor', ['anio' => 2026]))
            ->assertForbidden();

        $this->actingAs($conReportes)
            ->get(route('reportes.balance-general-extendido', ['anio' => 2026]))
            ->assertOk()
            ->assertSee('Balance general extendido');
    }

    private function usuarioConReportes(): User
    {
        $user = User::factory()->create();
        $role = Role::create(['nombre' => 'Reportes']);
        $permission = Permission::firstOrCreate(
            ['nombre' => 'reportes.ver'],
            ['descripcion' => 'Ver reportes contables']
        );

        $role->permissions()->attach($permission);
        $user->roles()->attach($role);

        return $user;
    }

    private function cuentasBase(): array
    {
        return [
            Cuenta::create([
                'nombre' => 'Caja',
                'tipo' => 'activo',
                'subtipo_codigo' => 1,
            ]),
            Cuenta::create([
                'nombre' => 'Ventas',
                'tipo' => 'ganancia',
                'subtipo_codigo' => 4,
            ]),
        ];
    }
}
