@extends('layouts.app')

@section('titulo', 'Editoras')

@section('conteudo')
    <div class="cabecalho">
        <h1>Editoras</h1>
        <a href="{{ route('editoras.create') }}" class="botao">Nova editora</a>
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
            @forelse ($editoras as $editora)
                <tr>
                    <td>{{ $editora->id_editora }}</td>
                    <td>{{ $editora->nome }}</td>
                    <td>{{ $editora->livros_count }}</td>
                    <td class="acoes">
                        <a href="{{ route('editoras.edit', $editora) }}">Editar</a>
                        <form method="POST" action="{{ route('editoras.destroy', $editora) }}"
                              onsubmit="return confirm('Excluir esta editora?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="link perigo">Excluir</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Nenhuma editora encontrada.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $editoras->links() }}
@endsection