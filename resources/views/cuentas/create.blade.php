@extends('layouts.app', ['title' => 'Nueva cuenta'])

@section('content')
    <h1>Nueva cuenta</h1>
    <form class="panel stack" method="POST" action="{{ route('cuentas.store') }}">
        @include('cuentas._form')
    </form>
@endsection
