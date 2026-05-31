<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PermisoController extends Controller
{
    public function index(): View
    {
        return view('seguridad.permisos.index', [
            'usuarios' => User::query()->with('roles')->orderBy('name')->get(),
            'roles' => Role::query()->with('permissions')->orderBy('nombre')->get(),
            'permisos' => Permission::query()->orderBy('nombre')->get(),
        ]);
    }

    public function actualizarRoles(Request $request, User $user): RedirectResponse
    {
        $datos = $request->validate([
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer', 'exists:roles,id'],
        ]);

        $user->roles()->sync($datos['roles'] ?? []);

        return redirect()->route('seguridad.permisos.index')->with('status', 'Roles actualizados correctamente.');
    }

    public function actualizarPermisos(Request $request, Role $role): RedirectResponse
    {
        $datos = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $role->permissions()->sync($datos['permissions'] ?? []);

        return redirect()->route('seguridad.permisos.index')->with('status', 'Permisos del rol actualizados correctamente.');
    }
}
