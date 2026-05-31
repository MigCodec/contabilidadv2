@extends('layouts.app', ['title' => 'Editar cuenta'])

@section('content')
    <h1>Editar cuenta</h1>
    <form class="panel stack" method="POST" action="{{ route('cuentas.update', $cuenta) }}">
        @method('PUT')
        @include('cuentas._form')
    </form>
@endsection
