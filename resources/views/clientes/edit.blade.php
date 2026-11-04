@extends('layouts.app')
@section('titulo', 'Editar cliente')
@section('conteudo')
    <h1>Editar cliente</h1>
    <form method="POST" action="{{ route('clientes.update', $cliente) }}" class="formulario">
        @csrf
        @method('PUT')
        @include('clientes._form')
    </form>
@endsection
