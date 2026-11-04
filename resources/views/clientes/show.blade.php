@extends('layouts.app')
@section('titulo', $cliente->nome)
@section('conteudo')
    <div class="cabecalho">
        <h1>{{ $cliente->nome }}</h1>
        <div class="botoes">
            <a href="{{ url('/emprestimos/create?cliente=' . $cliente->id_cliente) }}" class="botao">Novo empréstimo</a>
            <a href="{{ route('clientes.edit', $cliente) }}" class="botaosecundario">Editar</a>
        </div>
    </div>
    <div class="card">
        <dl class="detalhes">
            <div>
                <dt>CPF</dt>
                <dd>{{ $cliente->cpf }}</dd>
            </div>
            <div>
                <dt>Telefone</dt>
                <dd>{{ $cliente->telefone }}</dd>
            </div>
            <div>
                <dt>E-mail</dt>
                <dd>{{ $cliente->email }}</dd>
            </div>
            <div>
                <dt>Endereço</dt>
                <dd>{{ $cliente->logradouro }}, {{ $cliente->numero_residencial }}</dd>
            </div>
            <div>
                <dt>CEP</dt>
                <dd>{{ $cliente->cep }}</dd>
            </div>
        </dl>
    </div>
    <h2>Empréstimos recentes</h2>
    <table>
        <thead>
            <tr>
                <th>Empréstimo</th>
                <th>Data</th>
                <th>Devolução prevista</th>
                <th>Livros</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($emprestimos as $emprestimo)
                <tr>
                    <td><a
                            href="{{ url('/emprestimos/' . $emprestimo->id_emprestimo) }}">#{{ $emprestimo->id_emprestimo }}</a>
                    </td>
                    <td>{{ $emprestimo->data_emprestimo->format('d/m/Y') }}
                    </td>
                    <td>{{ $emprestimo->data_prevista_devolucao->format('d/m/Y') }}</td>
                    <td>{{ $emprestimo->livros_count }}</td>
                    <td><span class="status status-{{ $emprestimo->status }}">{{ $emprestimo->rotuloStatus() }}</span></td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Nenhum empréstimo.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
