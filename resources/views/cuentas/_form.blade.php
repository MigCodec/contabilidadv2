@csrf

<div class="grid">
    <div>
        <label for="nombre">Nombre</label>
        <input id="nombre" name="nombre" value="{{ old('nombre', $cuenta->nombre) }}" required>
    </div>
    <div>
        <label for="tipo">Tipo</label>
        <select id="tipo" name="tipo" required>
            @foreach (\App\Models\Cuenta::ETIQUETAS_TIPOS as $tipo => $etiqueta)
                <option value="{{ $tipo }}" @selected(old('tipo', $cuenta->tipo) === $tipo)>{{ $etiqueta }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="subtipo_codigo">Subtipo</label>
        <select id="subtipo_codigo" name="subtipo_codigo" required>
            @foreach (\App\Models\Cuenta::SUBTIPOS as $subtipoCodigo => $subtipoEtiqueta)
                <option value="{{ $subtipoCodigo }}" @selected((int) old('subtipo_codigo', $cuenta->subtipo_codigo ?? 0) === $subtipoCodigo)>
                    {{ $subtipoCodigo }} - {{ $subtipoEtiqueta }}
                </option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="cuenta_padre_id">Cuenta padre</label>
        <select id="cuenta_padre_id" name="cuenta_padre_id">
            <option value="">Sin cuenta padre</option>
            @foreach ($cuentasPadre as $cuentaPadre)
                <option value="{{ $cuentaPadre->id }}" @selected((int) old('cuenta_padre_id', $cuenta->cuenta_padre_id) === $cuentaPadre->id)>
                    {{ $cuentaPadre->codigo }} - {{ $cuentaPadre->nombre }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<p class="muted">El codigo se genera automaticamente como tipo.subtipo.padre.id. Si no tiene padre, el tercer segmento sera 0.</p>

<p class="checkbox">
    <input type="checkbox" id="acepta_movimientos" name="acepta_movimientos" value="1" @checked(old('acepta_movimientos', $cuenta->acepta_movimientos))>
    <label for="acepta_movimientos">Acepta movimientos</label>
</p>
<p class="checkbox">
    <input type="checkbox" id="activa" name="activa" value="1" @checked(old('activa', $cuenta->activa))>
    <label for="activa">Cuenta activa</label>
</p>

<button class="btn" type="submit">Guardar</button>
<a class="btn secondary" href="{{ route('cuentas.index') }}">Volver</a>
