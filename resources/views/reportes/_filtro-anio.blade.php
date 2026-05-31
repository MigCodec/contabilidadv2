<form class="panel grid" method="GET" action="{{ $action }}">
    <div>
        <label for="anio">Año</label>
        <input id="anio" type="number" name="anio" min="1900" max="2100" value="{{ $anio }}" required>
    </div>
    <div style="align-self: end;">
        <button class="btn" type="submit">Consultar</button>
    </div>
</form>
