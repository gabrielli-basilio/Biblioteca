@extends('layouts.app')
@section('titulo', $livro->titulo)
@section('conteudo')
    <div class="cabecalho">
        <h1>{{ $livro->titulo }}</h1>
        <div class="botoes">
            <a href="{{ url('/emprestimos/create?livro=' . $livro->id_livro) }}" class="botao">Emprestar</a>
            <a href="{{ route('livros.edit', $livro) }}" class="botao
secundario">Editar</a>
        </div>
    </div>
    <div class="card">
        <dl class="detalhes">
            <div>
                <dt>ISBN</dt>
                <dd>{{ $livro->isbn }}</dd>
            </div>
            <div>
                <dt>Autor</dt>
                <dd>{{ $livro->autor->nome }}</dd>
            </div>
            <div>
                <dt>Editora</dt>
                <dd>{{ $livro->editora->nome }}</dd>
            </div>
            <div>
                <dt>Categoria</dt>
                <dd>{{ $livro->categoria->nome ??
                    'Sem
                categoria' }}</dd>
            </div>
            <div>
                <dt>Ano de publicação</dt>
                <dd>{{ $livro->ano_publicacao }}
                </dd>
            </div>
            <div>
                <dt>Exemplares</dt>
                <dd>{{ $livro->quantidade_exemplar }}
                </dd>
            </div>
            <div>
                <dt>Disponíveis agora</dt>
                <dd>{{ $disponiveis }}</dd>
            </div>
        </dl>
    </div>
    <h2>Últimos empréstimos</h2>
    <table>
        <thead>
            <tr>
                <th>Empréstimo</th>
                <th>Cliente</th>
                <th>Data</th>
                <th>Devolvido em</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($historico as $emprestimo)
                <tr>
                    <td><a
                            href="{{ url('/emprestimos/' . $emprestimo->id_emprestimo) }}">#{{ $emprestimo->id_emprestimo }}</a>
                    </td>
                    <td>{{ $emprestimo->cliente->nome }}</td>
                    <td>{{ $emprestimo->data_emprestimo->format('d/m/Y') }}
                    </td>
                    <td>
                        {{ $emprestimo->pivot->data_devolucao
                            ? \Carbon\Carbon::parse($emprestimo->pivot->data_devolucao)->format('d/m/Y')
                            : 'Pendente' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Este livro nunca foi emprestado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
