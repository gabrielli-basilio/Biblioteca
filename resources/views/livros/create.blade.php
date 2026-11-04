@extends('layouts.app')

@section('titulo', 'Novo livro')

@section('conteudo')
    <h1>Novo livro</h1>

    <form method="POST" action="{{ route('livros.store') }}" class="formulario">
        @csrf
        @include('livros._form')
    </form>

@endsection
