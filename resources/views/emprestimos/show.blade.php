@extends('layouts.app')

@section('titulo', 'Empréstimo #' . $emprestimo->id_emprestimo)

@section('conteudo')
    <div class="cabecalho">
        <h1>Empréstimo #{{ $emprestimo->id_emprestimo }}</h1>

        @if ($pendentes > 0)
            <form method="POST" action="{{ route('emprestimos.devolver', $emprestimo) }}"
                  onsubmit="return confirm('Devolver todos os livros pendentes?')">
                @csrf
                @method('PATCH')
                <button type="submit" class="botao">Devolver todos</button>
            </form>
        @endif
    </div>

    <div class="card">
        <dl class="detalhes">
            <div>
                <dt>Cliente</dt>
                <dd><a href="{{ route('clientes.show', $emprestimo->cliente) }}">{{ $emprestimo->cliente->nome }}</a></dd>
            </div>
            <div><dt>Data do empréstimo</dt><dd>{{ $emprestimo->data_emprestimo->format('d/m/Y') }}</dd></div>
            <div><dt>Devolução prevista</dt><dd>{{ $emprestimo->data_prevista_devolucao->format('d/m/Y') }}</dd></div>
            <div>
                <dt>Status</dt>
                <dd><span class="status status-{{ $emprestimo->status }}">{{ $emprestimo->rotuloStatus() }}</span></dd>
            </div>
            <div><dt>Multa</dt><dd>R$ {{ number_format($multa, 2, ',', '.') }}</dd></div>
        </dl>
    </div>

    <h2>Livros</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Devolvido em</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($emprestimo->livros as $livro)
                <tr>
                    <td>{{ $livro->id_livro }}</td>
                    <td><a href="{{ route('livros.show', $livro) }}">{{ $livro->titulo }}</a></td>
                    <td>
                        {{ $livro->pivot->data_devolucao
                            ? \Carbon\Carbon::parse($livro->pivot->data_devolucao)->format('d/m/Y')
                            : 'Pendente' }}
                    </td>
                    <td class="acoes">
                        @if (is_null($livro->pivot->data_devolucao))
                            <form method="POST" action="{{ route('emprestimos.devolver', $emprestimo) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="livro_id" value="{{ $livro->id_livro }}">
                                <button type="submit" class="link">Devolver</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
