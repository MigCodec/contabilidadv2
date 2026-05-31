@extends('layouts.app', ['title' => 'Balance general extendido'])

@section('content')
    <div class="toolbar">
        <h1>Balance general extendido</h1>
    </div>

    @include('reportes._filtro-anio', ['action' => route('reportes.balance-general-extendido'), 'anio' => $anio])

    <div class="panel table-scroll">
        <table>
            <thead>
                <tr>
                    <th>Codigo</th>
                    <th>Cuenta</th>
                    <th class="right">Suma debe</th>
                    <th class="right">Suma haber</th>
                    <th class="right">Saldo deudor</th>
                    <th class="right">Saldo acreedor</th>
                    <th class="right">Activo</th>
                    <th class="right">Pasivo</th>
                    <th class="right">Perdida</th>
                    <th class="right">Ganancia</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($grupos as $grupo)
                    <tr class="subtotal">
                        <th colspan="10">{{ $grupo['tipo_etiqueta'] }} / {{ $grupo['subtipo_codigo'] }} - {{ $grupo['subtipo_etiqueta'] }}</th>
                    </tr>
                    @foreach ($grupo['filas'] as $fila)
                        <tr>
                            <td>{{ $fila['codigo'] }}</td>
                            <td>{{ $fila['nombre'] }}</td>
                            <td class="right">@include('reportes._monto', ['valor' => $fila['suma_debe']])</td>
                            <td class="right">@include('reportes._monto', ['valor' => $fila['suma_haber']])</td>
                            <td class="right">@include('reportes._monto', ['valor' => $fila['saldo_deudor']])</td>
                            <td class="right">@include('reportes._monto', ['valor' => $fila['saldo_acreedor']])</td>
                            <td class="right">@include('reportes._monto', ['valor' => $fila['activo']])</td>
                            <td class="right">@include('reportes._monto', ['valor' => $fila['pasivo']])</td>
                            <td class="right">@include('reportes._monto', ['valor' => $fila['perdida']])</td>
                            <td class="right">@include('reportes._monto', ['valor' => $fila['ganancia']])</td>
                        </tr>
                    @endforeach
                    <tr class="subtotal">
                        <td colspan="2">Subtotal</td>
                        @foreach (['suma_debe', 'suma_haber', 'saldo_deudor', 'saldo_acreedor', 'activo', 'pasivo', 'perdida', 'ganancia'] as $columna)
                            <td class="right">@include('reportes._monto', ['valor' => $grupo['totales'][$columna] ?? 0])</td>
                        @endforeach
                    </tr>
                @empty
                    <tr><td colspan="10">No hay cuentas para mostrar.</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="subtotal">
                    <th colspan="2">Totales</th>
                    @foreach (['suma_debe', 'suma_haber', 'saldo_deudor', 'saldo_acreedor', 'activo', 'pasivo', 'perdida', 'ganancia'] as $columna)
                        <th class="right">@include('reportes._monto', ['valor' => $totales[$columna] ?? 0])</th>
                    @endforeach
                </tr>
            </tfoot>
        </table>
    </div>
@endsection
