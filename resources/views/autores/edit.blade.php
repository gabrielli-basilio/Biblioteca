@extends('layouts.app')

@section('titulo', 'Editar autor')

@section('conteudo')
    <h1>Editar autor</h1>

    <form method="POST" action="{{ route('autores.update', $autor) }}" class="formulario">
        @csrf
        @method('PUT')
        @include('autores._form')
    </form>
@endsection