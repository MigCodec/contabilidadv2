<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeguridadTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_autenticado_sin_permisos_no_ve_modulos(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/')
            ->assertRedirect(route('sin-permisos'));

        $this->actingAs($user)
            ->get(route('cuentas.index'))
            ->assertForbidden();
    }

    public function test_usuario_con_permiso_de_cuentas_puede_ver_plan_de_cuentas(): void
    {
        $user = User::factory()->create();
        $role = Role::create(['nombre' => 'Contador']);
        $permission = Permission::create([
            'nombre' => 'cuentas.gestionar',
            'descripcion' => 'Gestionar plan de cuentas',
        ]);

        $role->permissions()->attach($permission);
        $user->roles()->attach($role);

        $this->actingAs($user)
            ->get(route('cuentas.index'))
            ->assertOk()
            ->assertSee('Plan de cuentas');
    }

    public function test_usuario_con_permiso_de_seguridad_puede_gestionar_roles(): void
    {
        $user = User::factory()->create();
        $role = Role::create(['nombre' => 'Administrador']);
        $permission = Permission::create([
            'nombre' => 'permisos.gestionar',
            'descripcion' => 'Gestionar permisos',
        ]);

        $role->permissions()->attach($permission);
        $user->roles()->attach($role);

        $this->actingAs($user)
            ->get(route('seguridad.permisos.index'))
            ->assertOk()
            ->assertSee('Seguridad');
    }
}
