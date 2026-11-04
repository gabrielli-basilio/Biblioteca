@extends('layouts.app')

@section('titulo', 'Nova editora')

@section('conteudo')
    <h1>Nova editora</h1>

    <form method="POST" action="{{ route('editoras.store') }}" class="formulario">
        @csrf
        @include('editoras._form')
    </form>
@endsection