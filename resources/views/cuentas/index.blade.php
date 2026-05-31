@extends('layouts.app', ['title' => 'Plan de cuentas'])

@section('content')
    <div class="toolbar">
        <h1>Plan de cuentas</h1>
        <a class="btn" href="{{ route('cuentas.create') }}">Nueva cuenta</a>
    </div>

    <div class="panel">
        <table>
            <thead>
                <tr>
                    <th>Codigo</th>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Subtipo</th>
                    <th>Padre</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($cuentas as $cuenta)
                    <tr>
                        <td>{{ $cuenta->codigo }}</td>
                        <td>{{ $cuenta->nombre }}</td>
                        <td>{{ $cuenta->etiquetaTipo() }}</td>
                        <td>{{ $cuenta->subtipo_codigo }} - {{ $cuenta->etiquetaSubtipo() }}</td>
                        <td>{{ $cuenta->padre?->codigo ?? '-' }}</td>
                        <td>{{ $cuenta->activa ? 'Activa' : 'Inactiva' }}</td>
                        <td class="right">
                            <a href="{{ route('cuentas.show', $cuenta) }}">Ver</a> |
                            <a href="{{ route('cuentas.edit', $cuenta) }}">Editar</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7">No hay cuentas registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $cuentas->links() }}
@endsection
