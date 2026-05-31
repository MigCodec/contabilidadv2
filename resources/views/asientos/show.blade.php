@extends('layouts.app', ['title' => 'Asiento'])

@section('content')
    <div class="toolbar">
        <h1>Asiento #{{ $asiento->id }}</h1>
        <a class="btn secondary" href="{{ route('asientos.index') }}">Volver</a>
    </div>

    <div class="panel">
        <p><strong>Fecha:</strong> {{ $asiento->fecha->format('Y-m-d') }}</p>
        <p><strong>Glosa:</strong> {{ $asiento->glosa }}</p>
        <p><strong>Estado:</strong> {{ ucfirst($asiento->estado) }}</p>
        <p><strong>Responsable:</strong> {{ $asiento->usuario?->name ?? 'No autenticado' }}</p>
    </div>

    <div class="panel">
        <table>
            <thead>
                <tr>
                    <th>Cuenta</th>
                    <th>Descripcion</th>
                    <th class="right">Debe</th>
                    <th class="right">Haber</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($asiento->detalles as $detalle)
                    <tr>
                        <td>{{ $detalle->cuenta->codigo }} - {{ $detalle->cuenta->nombre }}</td>
                        <td>{{ $detalle->descripcion }}</td>
                        <td class="right">{{ number_format((float) $detalle->debe, 2, ',', '.') }}</td>
                        <td class="right">{{ number_format((float) $detalle->haber, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2">Totales</th>
                    <th class="right">{{ number_format((float) $asiento->total_debe, 2, ',', '.') }}</th>
                    <th class="right">{{ number_format((float) $asiento->total_haber, 2, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
@endsection
