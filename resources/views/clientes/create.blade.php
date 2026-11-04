@extends('layouts.app')
@section('titulo', 'Novo cliente')
@section('conteudo')
    <h1>Novo cliente</h1>
    <form method="POST" action="{{ route('clientes.store') }}" class="formulario">
        @csrf
        @include('clientes._form')
    </form>
@endsection
