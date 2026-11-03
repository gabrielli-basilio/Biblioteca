@extends('layouts.app')

@section('titulo', 'Nova categoria')

@section('conteudo')
    <h1>Nova categoria</h1>

    <form method="POST" action="{{ route('categorias.store') }}" class="formulario">
        @csrf
        @include('categorias._form')
    </form>
@endsection