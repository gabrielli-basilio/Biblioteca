@extends('layouts.app')

@section('titulo', 'Novo autor')

@section('conteudo')
    <h1>Novo autor</h1>

    <form method="POST" action="{{ route('autores.store') }}" class="formulario">
        @csrf
        @include('autores._form')
    </form>
@endsection