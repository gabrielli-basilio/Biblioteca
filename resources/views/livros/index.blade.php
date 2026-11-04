@extends('layouts.app')
@section('titulo', 'Livros')
@section('conteudo')
    <div class="cabecalho">
        <h1>Livros</h1>
        <a href="{{ route('livros.create') }}" class="botao">Novo livro</a>
    </div>
    <form method="GET" class="busca">
        <input type="text" name="busca" value="{{ request('busca') }}" placeholder="Buscar por título ou ISBN">
        <select name="categoria_id">
            <option value="">Todas as categorias</option>
            @foreach ($categorias as $categoria)
                <option value="{{ $categoria->id_categoria }}" @selected(request('categoria_id') == $categoria->id_categoria)>
                    {{ $categoria->nome }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="botao secundario">Filtrar</button>
    </form>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Autor</th>
                <th>Editora</th>
                <th>Categoria</th>
                <th>Ano</th>
                <th>Exemplares</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($livros as $livro)
                <tr>
                    <td>{{ $livro->id_livro }}</td>
                    <td><a href="{{ route('livros.show', $livro) }}">{{ $livro->titulo }}</a></td>
                    <td>{{ $livro->autor->nome }}</td>
                    <td>{{ $livro->editora->nome }}</td>
                    <td>{{ $livro->categoria->nome ?? 'Sem categoria' }}</td>
                    <td>{{ $livro->ano_publicacao }}</td>
                    <td>{{ $livro->quantidade_exemplar }}</td>
                    <td class="acoes">
                        <a href="{{ route('livros.edit', $livro) }}">Editar</a>
                        <form method="POST" action="{{ route('livros.destroy', $livro) }}"
                            onsubmit="return confirm('Excluir este livro?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="linkperigo">Excluir</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">Nenhum livro encontrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    {{ $livros->links() }}
@endsection
