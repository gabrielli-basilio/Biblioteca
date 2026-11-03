@extends('layouts.app')

@section('titulo', 'Empréstimos')

@section('conteudo')
    <div class="cabecalho">
        <h1>Empréstimos</h1>
        <a href="{{ route('emprestimos.create') }}" class="botao">Novo empréstimo</a>
    </div>

    <form method="GET" class="busca">
        <select name="status">
            <option value="">Todos os status</option>
            <option value="em_andamento" @selected(request('status') === 'em_andamento')>Em andamento</option>
            <option value="atrasado" @selected(request('status') === 'atrasado')>Atrasado</option>
            <option value="devolvido" @selected(request('status') === 'devolvido')>Devolvido</option>
        </select>
        <button type="submit" class="botao secundario">Filtrar</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Data</th>
                <th>Devolução prevista</th>
                <th>Livros</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($emprestimos as $emprestimo)
                <tr>
                    <td><a href="{{ route('emprestimos.show', $emprestimo) }}">#{{ $emprestimo->id_emprestimo }}</a></td>
                    <td>{{ $emprestimo->cliente->nome }}</td>
                    <td>{{ $emprestimo->data_emprestimo->format('d/m/Y') }}</td>
                    <td>{{ $emprestimo->data_prevista_devolucao->format('d/m/Y') }}</td>
                    <td>{{ $emprestimo->livros_count }}</td>
                    <td><span class="status status-{{ $emprestimo->status }}">{{ $emprestimo->rotuloStatus() }}</span></td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Nenhum empréstimo encontrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $emprestimos->links() }}
@endsection
