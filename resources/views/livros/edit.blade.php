@extends('layouts.app')

@section('titulo', 'Editar livro')

@section('conteudo')
    <h1>Editar livro</h1>

    <form method="POST" action="{{ route('livros.update', $livro) }}" class="formulario">
        @csrf
        @method('PUT')
        @include('livros._form')
    </form>
@endsection
