@extends('layouts.app')

@section('titulo', 'Autores')

@section('conteudo')
    <div class="cabecalho">
        <h1>Autores</h1>
        <a href="{{ route('autores.create') }}" class="botao">Novo autor</a>
    </div>

    <form method="GET" class="busca">
        <input type="text" name="busca" value="{{ request('busca') }}" placeholder="Buscar por nome">
        <button type="submit" class="botao secundario">Buscar</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Livros</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($autores as $autor)
                <tr>
                    <td>{{ $autor->id_autor }}</td>
                    <td>{{ $autor->nome }}</td>
                    <td>{{ $autor->livros_count }}</td>
                    <td class="acoes">
                        <a href="{{ route('autores.edit', $autor) }}">Editar</a>
                        <form method="POST" action="{{ route('autores.destroy', $autor) }}"
                              onsubmit="return confirm('Excluir este autor?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="link perigo">Excluir</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Nenhum autor encontrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $autores->links() }}
@endsection