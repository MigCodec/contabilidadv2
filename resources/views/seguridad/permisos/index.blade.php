@extends('layouts.app', ['title' => 'Seguridad'])

@section('content')
    <div class="toolbar">
        <h1>Seguridad</h1>
    </div>

    <div class="panel">
        <h2>Usuarios</h2>
        <table>
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Email</th>
                    <th>Roles</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($usuarios as $usuario)
                    <tr>
                        <td>{{ $usuario->name }}</td>
                        <td>{{ $usuario->email }}</td>
                        <td>
                            <form method="POST" action="{{ route('seguridad.usuarios.roles.update', $usuario) }}">
                                @csrf
                                @method('PUT')
                                @foreach ($roles as $role)
                                    <label class="checkbox">
                                        <input type="checkbox" name="roles[]" value="{{ $role->id }}" @checked($usuario->roles->contains($role))>
                                        {{ $role->nombre }}
                                    </label>
                                @endforeach
                                <button class="btn" type="submit">Guardar roles</button>
                            </form>
                        </td>
                        <td>
                            @foreach ($usuario->roles as $role)
                                <span class="pill">{{ $role->nombre }}</span>
                            @endforeach
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4">No hay usuarios registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="panel">
        <h2>Roles y permisos</h2>
        <table>
            <thead>
                <tr>
                    <th>Rol</th>
                    <th>Descripcion</th>
                    <th>Permisos</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($roles as $role)
                    <tr>
                        <td>{{ $role->nombre }}</td>
                        <td>{{ $role->descripcion }}</td>
                        <td>
                            <form method="POST" action="{{ route('seguridad.roles.permisos.update', $role) }}">
                                @csrf
                                @method('PUT')
                                @foreach ($permisos as $permiso)
                                    <label class="checkbox">
                                        <input type="checkbox" name="permissions[]" value="{{ $permiso->id }}" @checked($role->permissions->contains($permiso))>
                                        {{ $permiso->descripcion }}
                                    </label>
                                @endforeach
                                <button class="btn" type="submit">Guardar permisos</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
