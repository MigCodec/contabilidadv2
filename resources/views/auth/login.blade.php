@extends('layouts.app', ['title' => 'Ingresar'])

@section('content')
    <div class="panel login-box">
        <h1>Ingresar</h1>
        <p class="muted">Acceso exclusivo mediante cuenta Google autorizada.</p>
        <a class="btn" href="{{ route('auth.google.redirect') }}">Ingresar con Google</a>
    </div>
@endsection
