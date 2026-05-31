@extends('layouts.app', ['title' => 'Libro mayor'])

@section('content')
    <div class="toolbar">
        <h1>Libro mayor</h1>
    </div>

    @include('reportes._filtro-anio', ['action' => route('reportes.libro-mayor'), 'anio' => $anio])

    @forelse ($cuentas as $cuenta)
        <div class="panel">
            <h2>{{ $cuenta['codigo'] }} - {{ $cuenta['nombre'] }}</h2>
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Asiento</th>
                            <th>Glosa</th>
                            <th>Descripcion</th>
                            <th class="right">Debe</th>
                            <th class="right">Haber</th>
                            <th class="right">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cuenta['lineas'] as $linea)
                            <tr>
                                <td>{{ \Illuminate\Support\Carbon::parse($linea['fecha'])->format('Y-m-d') }}</td>
                                <td>
                                    @if (auth()->user()->hasPermission('asientos.gestionar'))
                                        <a href="{{ route('asientos.show', $linea['asiento_id']) }}">#{{ $linea['asiento_id'] }}</a>
                                    @else
                                        #{{ $linea['asiento_id'] }}
                                    @endif
                                </td>
                                <td>{{ $linea['glosa'] }}</td>
                                <td>{{ $linea['descripcion'] }}</td>
                                <td class="right">@include('reportes._monto', ['valor' => $linea['debe']])</td>
                                <td class="right">@include('reportes._monto', ['valor' => $linea['haber']])</td>
                                <td class="right">@include('reportes._monto', ['valor' => $linea['saldo']])</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="subtotal">
                            <th colspan="4">Totales</th>
                            <th class="right">@include('reportes._monto', ['valor' => $cuenta['total_debe']])</th>
                            <th class="right">@include('reportes._monto', ['valor' => $cuenta['total_haber']])</th>
                            <th class="right">@include('reportes._monto', ['valor' => $cuenta['saldo']])</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @empty
        <div class="panel">No hay movimientos registrados para el año {{ $anio }}.</div>
    @endforelse
@endsection
