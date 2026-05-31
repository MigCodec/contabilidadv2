@extends('layouts.app', ['title' => 'Sin permisos'])

@section('content')
    <div class="panel login-box">
        <h1>Sin permisos asignados</h1>
        <p class="muted">Tu cuenta inicio sesion correctamente, pero no tiene permisos para ver secciones del sistema.</p>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn secondary" type="submit">Salir</button>
        </form>
    </div>
@endsection
