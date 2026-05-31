@extends('layouts.app', ['title' => 'Asientos'])

@section('content')
    <div class="toolbar">
        <h1>Asientos contables</h1>
        <a class="btn" href="{{ route('asientos.create') }}">Nuevo asiento</a>
    </div>

    <div class="panel">
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Glosa</th>
                    <th>Estado</th>
                    <th>Lineas</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($asientos as $asiento)
                    <tr>
                        <td>{{ $asiento->fecha->format('Y-m-d') }}</td>
                        <td>{{ $asiento->glosa }}</td>
                        <td>{{ ucfirst($asiento->estado) }}</td>
                        <td>{{ $asiento->detalles_count }}</td>
                        <td class="right">
                            <a href="{{ route('asientos.show', $asiento) }}">Ver</a> |
                            <a href="{{ route('asientos.edit', $asiento) }}">Editar</a>
                            <form class="inline-form" method="POST" action="{{ route('asientos.destroy', $asiento) }}" onsubmit="return confirm('Eliminar este asiento?');">
                                @csrf
                                @method('DELETE')
                                <button class="link-button danger-link" type="submit">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5">No hay asientos registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $asientos->links() }}
@endsection
