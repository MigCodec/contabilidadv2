<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;

class AsignarRolUsuario extends Command
{
    protected $signature = 'usuario:asignar-rol {email} {rol=Administrador}';

    protected $description = 'Asigna un rol existente a un usuario por email.';

    public function handle(): int
    {
        $user = User::query()->where('email', $this->argument('email'))->first();
        $role = Role::query()->where('nombre', $this->argument('rol'))->first();

        if (! $user) {
            $this->error('Usuario no encontrado.');

            return self::FAILURE;
        }

        if (! $role) {
            $this->error('Rol no encontrado. Ejecute primero php artisan db:seed.');

            return self::FAILURE;
        }

        $user->roles()->syncWithoutDetaching([$role->id]);

        $this->info("Rol {$role->nombre} asignado a {$user->email}.");

        return self::SUCCESS;
    }
}
