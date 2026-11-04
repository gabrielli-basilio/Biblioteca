@extends('layouts.app')

@section('titulo', 'Editar editora')

@section('conteudo')
    <h1>Editar editora</h1>

    <form method="POST" action="{{ route('editoras.update', $editora) }}" class="formulario">
        @csrf
        @method('PUT')
        @include('editoras._form')
    </form>
@endsection