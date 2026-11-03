<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo', 'Início') - Biblioteca</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header class="topo">
        <a href="{{ url('/') }}" class="marca">Biblioteca</a>
        <nav>
            <a href="{{ url('/livros') }}">Livros</a>
            <a href="{{ url('/clientes') }}">Clientes</a>
            <a href="{{ url('/emprestimos') }}">Empréstimos</a>
            <a href="{{ url('/autores') }}">Autores</a>
            <a href="{{ url('/editoras') }}">Editoras</a>
            <a href="{{ url('/categorias') }}">Categorias</a>
        </nav>
    </header>

    <main class="conteudo">
        @if (session('sucesso'))
            <div class="alerta sucesso">{{ session('sucesso') }}</div>
        @endif

        @if (session('erro'))
            <div class="alerta erro">{{ session('erro') }}</div>
        @endif

        @if ($errors->any())
            <div class="alerta erro">
                <ul>
                    @foreach ($errors->all() as $erro)
                        <li>{{ $erro }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('conteudo')
    </main>
</body>
</html>