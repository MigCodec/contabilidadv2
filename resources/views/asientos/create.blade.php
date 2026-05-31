@extends('layouts.app', ['title' => 'Nuevo asiento'])

@section('content')
    <h1>Nuevo asiento</h1>

    <form class="panel stack" method="POST" action="{{ route('asientos.store') }}">
        @include('asientos._form', [
            'asiento' => new \App\Models\Asiento(),
            'submitLabel' => 'Registrar asiento',
        ])
    </form>
@endsection
