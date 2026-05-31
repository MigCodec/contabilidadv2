<?php

namespace App\Console\Commands;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;

class AsignarAdministrador extends Command
{
    protected $signature = 'usuario:administrador {email}';

    protected $description = 'Asigna permisos de administrador a un usuario por correo.';

    public function handle(): int
    {
        $user = User::query()->where('email', $this->argument('email'))->first();

        if (! $user) {
            $this->error('Usuario no encontrado. Primero debe iniciar sesion con Google.');

            return self::FAILURE;
        }

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

        $administrador->permissions()->sync($permisos->pluck('id'));
        $user->roles()->syncWithoutDetaching([$administrador->id]);

        $this->info("El usuario {$user->email} ahora tiene permisos de administrador.");

        return self::SUCCESS;
    }
}
