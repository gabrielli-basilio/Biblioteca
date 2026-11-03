@extends('layouts.app')

@section('titulo', 'Categorias')

@section('conteudo')
    <div class="cabecalho">
        <h1>Categorias</h1>
        <a href="{{ route('categorias.create') }}" class="botao">Nova categoria</a>
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
            @forelse ($categorias as $categoria)
                <tr>
                    <td>{{ $categoria->id_categoria }}</td>
                    <td>{{ $categoria->nome }}</td>
                    <td>{{ $categoria->livros_count }}</td>
                    <td class="acoes">
                        <a href="{{ route('categorias.edit', $categoria) }}">Editar</a>
                        <form method="POST" action="{{ route('categorias.destroy', $categoria) }}"
                              onsubmit="return confirm('Excluir esta categoria?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="link perigo">Excluir</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Nenhuma categoria encontrada.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $categorias->links() }}
@endsection