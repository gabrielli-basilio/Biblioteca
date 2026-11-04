@extends('layouts.app')
@section('titulo', 'Clientes')
@section('conteudo')
    <div class="cabecalho">
        <h1>Clientes</h1>
        <a href="{{ route('clientes.create') }}" class="botao">Novo
            cliente</a>
    </div>
    <form method="GET" class="busca">
        <input type="text" name="busca" value="{{ request('busca') }}" placeholder="Buscar por nome ou CPF">
        <button type="submit" class="botao secundario">Buscar</button>
    </form>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>CPF</th>
                <th>E-mail</th>
                <th>Empréstimos</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($clientes as $cliente)
                <tr>
                    <td>{{ $cliente->id_cliente }}</td>
                    <td><a href="{{ route('clientes.show', $cliente) }}">{{ $cliente->nome }}</a></td>
                    <td>{{ $cliente->cpf }}</td>
                    <td>{{ $cliente->email }}</td>
                    <td>{{ $cliente->emprestimos_count }}</td>
                    <td class="acoes">
                        <a href="{{ route('clientes.edit', $cliente) }}">Editar</a>
                        <form method="POST" action="{{ route('clientes.destroy', $cliente) }}"
                            onsubmit="return confirm('Excluir este cliente?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="linkperigo">Excluir</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Nenhum cliente encontrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    {{ $clientes->links() }}
@endsection
