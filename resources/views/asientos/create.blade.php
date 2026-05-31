@extends('layouts.app', ['title' => 'Nuevo asiento'])

@section('content')
    <h1>Nuevo asiento</h1>

    <form class="panel stack" method="POST" action="{{ route('asientos.store') }}">
        @csrf

        <div class="grid">
            <div>
                <label for="fecha">Fecha</label>
                <input id="fecha" type="date" name="fecha" value="{{ old('fecha', now()->toDateString()) }}" required>
            </div>
            <div>
                <label for="glosa">Glosa</label>
                <input id="glosa" name="glosa" value="{{ old('glosa') }}" required>
            </div>
        </div>

        <table id="detalles">
            <thead>
                <tr>
                    <th>Cuenta</th>
                    <th>Descripcion</th>
                    <th>Debe</th>
                    <th>Haber</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @php($lineas = old('detalles', [['debe' => '', 'haber' => ''], ['debe' => '', 'haber' => '']]))
                @foreach ($lineas as $i => $linea)
                    <tr>
                        <td>
                            <select name="detalles[{{ $i }}][cuenta_id]" required>
                                <option value="">Seleccione</option>
                                @foreach ($cuentas as $cuenta)
                                    <option value="{{ $cuenta->id }}" @selected((int) ($linea['cuenta_id'] ?? 0) === $cuenta->id)>
                                        {{ $cuenta->codigo }} - {{ $cuenta->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td><input name="detalles[{{ $i }}][descripcion]" value="{{ $linea['descripcion'] ?? '' }}"></td>
                        <td><input type="number" step="0.01" min="0" name="detalles[{{ $i }}][debe]" value="{{ $linea['debe'] ?? '' }}"></td>
                        <td><input type="number" step="0.01" min="0" name="detalles[{{ $i }}][haber]" value="{{ $linea['haber'] ?? '' }}"></td>
                        <td><button class="btn danger" type="button" data-remove-linea>Quitar</button></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div>
            <button class="btn secondary" type="button" id="agregar-linea">Agregar linea</button>
            <button class="btn" type="submit">Registrar asiento</button>
        </div>
    </form>

    <template id="linea-template">
        <tr>
            <td>
                <select data-name="cuenta_id" required>
                    <option value="">Seleccione</option>
                    @foreach ($cuentas as $cuenta)
                        <option value="{{ $cuenta->id }}">{{ $cuenta->codigo }} - {{ $cuenta->nombre }}</option>
                    @endforeach
                </select>
            </td>
            <td><input data-name="descripcion"></td>
            <td><input type="number" step="0.01" min="0" data-name="debe"></td>
            <td><input type="number" step="0.01" min="0" data-name="haber"></td>
            <td><button class="btn danger" type="button" data-remove-linea>Quitar</button></td>
        </tr>
    </template>

    <script>
        const cuerpo = document.querySelector('#detalles tbody');
        const plantilla = document.querySelector('#linea-template');
        const renumerar = () => {
            cuerpo.querySelectorAll('tr').forEach((fila, indice) => {
                fila.querySelectorAll('[data-name]').forEach((campo) => {
                    campo.name = `detalles[${indice}][${campo.dataset.name}]`;
                });
            });
        };

        document.querySelector('#agregar-linea').addEventListener('click', () => {
            cuerpo.appendChild(plantilla.content.cloneNode(true));
            renumerar();
        });

        cuerpo.addEventListener('click', (evento) => {
            if (! evento.target.matches('[data-remove-linea]')) {
                return;
            }

            if (cuerpo.querySelectorAll('tr').length > 2) {
                evento.target.closest('tr').remove();
                renumerar();
            }
        });
    </script>
@endsection
