<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $permisos = collect([
            'cuentas.gestionar' => 'Gestionar plan de cuentas',
            'asientos.gestionar' => 'Gestionar asientos contables',
            'reportes.ver' => 'Ver reportes contables',
            'permisos.gestionar' => 'Gestionar usuarios, roles y permisos',
        ])->map(fn (string $descripcion, string $nombre) => Permission::updateOrCreate(
            ['nombre' => $nombre],
            ['descripcion' => $descripcion],
        ));

        $administrador = Role::updateOrCreate(
            ['nombre' => 'Administrador'],
            ['descripcion' => 'Acceso completo al sistema']
        );

        Role::updateOrCreate(['nombre' => 'Contador'], ['descripcion' => 'Gestion contable operativa'])
            ->permissions()
            ->sync($permisos->only(['cuentas.gestionar', 'asientos.gestionar', 'reportes.ver'])->pluck('id'));

        Role::updateOrCreate(['nombre' => 'Consulta'], ['descripcion' => 'Sin permisos asignados por defecto']);

        $administrador->permissions()->sync($permisos->pluck('id'));
    }
}
