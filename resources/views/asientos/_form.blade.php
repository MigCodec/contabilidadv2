@csrf

@isset($method)
    @method($method)
@endisset

<div class="grid">
    <div>
        <label for="fecha">Fecha</label>
        <input id="fecha" type="date" name="fecha" value="{{ old('fecha', optional($asiento?->fecha)->format('Y-m-d') ?? now()->toDateString()) }}" required>
    </div>
    <div>
        <label for="glosa">Glosa</label>
        <input id="glosa" name="glosa" value="{{ old('glosa', $asiento?->glosa) }}" required>
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
        @php
            $lineas = old('detalles');

            if (! $lineas && $asiento?->exists) {
                $lineas = $asiento->detalles->map(fn ($detalle) => [
                    'cuenta_id' => $detalle->cuenta_id,
                    'descripcion' => $detalle->descripcion,
                    'debe' => $detalle->debe,
                    'haber' => $detalle->haber,
                ])->all();
            }

            $lineas = $lineas ?: [['debe' => '', 'haber' => ''], ['debe' => '', 'haber' => '']];
        @endphp

        @foreach ($lineas as $i => $linea)
            <tr>
                <td>
                    <select data-name="cuenta_id" name="detalles[{{ $i }}][cuenta_id]" required>
                        <option value="">Seleccione</option>
                        @foreach ($cuentas as $cuenta)
                            <option value="{{ $cuenta->id }}" @selected((int) ($linea['cuenta_id'] ?? 0) === $cuenta->id)>
                                {{ $cuenta->codigo }} - {{ $cuenta->nombre }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td><input data-name="descripcion" name="detalles[{{ $i }}][descripcion]" value="{{ $linea['descripcion'] ?? '' }}"></td>
                <td><input data-name="debe" type="number" step="0.01" min="0" name="detalles[{{ $i }}][debe]" value="{{ $linea['debe'] ?? '' }}"></td>
                <td><input data-name="haber" type="number" step="0.01" min="0" name="detalles[{{ $i }}][haber]" value="{{ $linea['haber'] ?? '' }}"></td>
                <td><button class="btn danger" type="button" data-remove-linea>Quitar</button></td>
            </tr>
        @endforeach
    </tbody>
</table>

<div>
    <button class="btn secondary" type="button" id="agregar-linea">Agregar linea</button>
    <button class="btn" type="submit">{{ $submitLabel }}</button>
</div>

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

    renumerar();
</script>
