@extends('layouts.app')

@section('titulo', 'Novo empréstimo')

@section('conteudo')
    <h1>Novo empréstimo</h1>

    <form method="POST" action="{{ route('emprestimos.store') }}" class="formulario">
        @csrf

        <label for="cliente_id">Cliente</label>
        <select id="cliente_id" name="cliente_id" required>
            <option value="">Selecione</option>
            @foreach ($clientes as $cliente)
                <option value="{{ $cliente->id_cliente }}" @selected(old('cliente_id', request('cliente')) == $cliente->id_cliente)>
                    {{ $cliente->nome }} — {{ $cliente->cpf }}
                </option>
            @endforeach
        </select>

        <label>Livros (ID, até 5)</label>
        <div class="linha">
            @for ($i = 0; $i < 5; $i++)
                <input type="number" name="livros[]" min="1" placeholder="ID do livro {{ $i + 1 }}"
                       value="{{ old('livros.' . $i, $i === 0 ? request('livro') : '') }}">
            @endfor
        </div>

        <label for="dias_prazo">Prazo (dias)</label>
        <input type="number" id="dias_prazo" name="dias_prazo" min="1" max="60" value="{{ old('dias_prazo', 14) }}" required>

        <div class="botoes">
            <button type="submit" class="botao">Registrar</button>
            <a href="{{ route('emprestimos.index') }}" class="botao secundario">Cancelar</a>
        </div>
    </form>
@endsection
