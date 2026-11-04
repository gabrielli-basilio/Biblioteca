<?php

namespace App\Http\Controllers;

use App\Exceptions\RegraNegocioException;
use App\Http\Requests\LivroRequest;
use App\Models\Autor;
use App\Models\Categoria;
use App\Models\Editora;
use App\Models\Livro;
use App\Services\LivroService;
use Illuminate\Http\Request;

class LivroController extends Controller
{
    public function __construct(private LivroService $service)
    {
    }

    public function index(Request $request)
    {
        $livros = $this->service->listar($request->busca, $request->integer('categoria_id') ?: null);
        $categorias = Categoria::orderBy('nome')->get();

        return view('livros.index', compact('livros', 'categorias'));
    }

    public function create()
    {
        return view('livros.create', $this->opcoes());
    }

    public function store(LivroRequest $request)
    {
        $livro = $this->service->criar($request->validated());

        return redirect()->route('livros.show', $livro)->with('sucesso', 'Livro cadastrado.');
    }

    public function show(Livro $livro)
    {
        $livro->load(['categoria', 'editora', 'autor']);
        $disponiveis = $this->service->exemplaresDisponiveis($livro);
        $historico = $livro->emprestimos()
            ->with('cliente')
            ->orderByDesc('emprestimos.data_emprestimo')
            ->limit(10)
            ->get();

        return view('livros.show', compact('livro', 'disponiveis', 'historico'));
    }

    public function edit(Livro $livro)
    {
        return view('livros.edit', ['livro' => $livro] + $this->opcoes());
    }

    public function update(LivroRequest $request, Livro $livro)
    {
        $this->service->atualizar($livro, $request->validated());

        return redirect()->route('livros.show', $livro)->with('sucesso', 'Livro atualizado.');
    }

    public function destroy(Livro $livro)
    {
        try {
            $this->service->excluir($livro);
        } catch (RegraNegocioException $e) {
            return back()->with('erro', $e->getMessage());
        }

        return redirect()->route('livros.index')->with('sucesso', 'Livro excluído.');
    }

    private function opcoes(): array
    {
        return [
            'categorias' => Categoria::orderBy('nome')->get(),
            'editoras' => Editora::orderBy('nome')->get(),
            'autores' => Autor::orderBy('nome')->get(),
        ];
    }
}