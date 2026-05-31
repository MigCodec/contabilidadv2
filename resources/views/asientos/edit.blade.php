@extends('layouts.app', ['title' => 'Editar asiento'])

@section('content')
    <h1>Editar asiento #{{ $asiento->id }}</h1>

    <form class="panel stack" method="POST" action="{{ route('asientos.update', $asiento) }}">
        @include('asientos._form', [
            'method' => 'PUT',
            'submitLabel' => 'Actualizar asiento',
        ])
    </form>
@endsection
