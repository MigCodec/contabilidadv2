@extends('layouts.app', ['title' => 'Cuenta'])

@section('content')
    <div class="toolbar">
        <h1>{{ $cuenta->codigo }} - {{ $cuenta->nombre }}</h1>
        <a class="btn secondary" href="{{ route('cuentas.index') }}">Volver</a>
    </div>

    <div class="panel">
        <p><strong>Tipo:</strong> {{ $cuenta->etiquetaTipo() }}</p>
        <p><strong>Subtipo:</strong> {{ $cuenta->subtipo_codigo }} - {{ $cuenta->etiquetaSubtipo() }}</p>
        <p><strong>Codigo:</strong> {{ $cuenta->codigo }}</p>
        <p><strong>Cuenta padre:</strong> {{ $cuenta->padre ? $cuenta->padre->codigo.' - '.$cuenta->padre->nombre : 'Sin cuenta padre' }}</p>
        <p><strong>Acepta movimientos:</strong> {{ $cuenta->acepta_movimientos ? 'Si' : 'No' }}</p>
        <p><strong>Estado:</strong> {{ $cuenta->activa ? 'Activa' : 'Inactiva' }}</p>
    </div>
@endsection
