@extends('layouts.app')

@section('titulo', 'Início')

@section('conteudo')
    <div class="cabecalho">
        <h1>Painel da biblioteca</h1>
        <form method="POST" action="{{ route('atrasados.atualizar') }}">
            @csrf
            <button type="submit" class="botao secundario">Atualizar atrasos</button>
        </form>
    </div>

    <div class="indicadores">
        <div class="indicador"><span>Livros</span><strong>{{ $totais['livros'] }}</strong></div>
        <div class="indicador"><span>Clientes</span><strong>{{ $totais['clientes'] }}</strong></div>
        <div class="indicador"><span>Em andamento</span><strong>{{ $totais['em_andamento'] }}</strong></div>
        <div class="indicador"><span>Atrasados</span><strong>{{ $totais['atrasados'] }}</strong></div>
    </div>

    <h2>Livros mais emprestados</h2>

    <table>
        <thead>
            <tr>
                <th>Livro</th>
                <th>Autor</th>
                <th>Categoria</th>
                <th>Empréstimos</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($maisEmprestados as $item)
                <tr>
                    <td><a href="{{ route('livros.show', $item->id_livro) }}">{{ $item->livro }}</a></td>
                    <td>{{ $item->autor }}</td>
                    <td>{{ $item->categoria }}</td>
                    <td>{{ $item->total_emprestimos }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Maiores atrasos</h2>

    <table>
        <thead>
            <tr>
                <th>Empréstimo</th>
                <th>Cliente</th>
                <th>Telefone</th>
                <th>Livro</th>
                <th>Dias de atraso</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($atrasados as $item)
                <tr>
                    <td><a href="{{ route('emprestimos.show', $item->id_emprestimo) }}">#{{ $item->id_emprestimo }}</a></td>
                    <td>{{ $item->cliente }}</td>
                    <td>{{ $item->telefone }}</td>
                    <td>{{ $item->livro }}</td>
                    <td>{{ $item->dias_atraso }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Nenhum empréstimo atrasado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
