<?php

namespace App\Http\Controllers;

use App\Models\Autor;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class AutorController extends Controller
{
    public function index(Request $request)
    {
        $autores = Autor::withCount('livros')
            ->when($request->busca, fn ($q, $busca) => $q->where('nome', 'ilike', "%{$busca}%"))
            ->orderBy('nome')
            ->paginate(15)
            ->withQueryString();

        return view('autores.index', compact('autores'));
    }

    public function create()
    {
        return view('autores.create');
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:150'],
        ]);

        Autor::create($dados);

        return redirect()->route('autores.index')->with('sucesso', 'Autor cadastrado.');
    }

    public function edit(Autor $autor)
    {
        return view('autores.edit', compact('autor'));
    }

    public function update(Request $request, Autor $autor)
    {
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:150'],
        ]);

        $autor->update($dados);

        return redirect()->route('autores.index')->with('sucesso', 'Autor atualizado.');
    }

    public function destroy(Autor $autor)
    {
        try {
            $autor->delete();
        } catch (QueryException $e) {
            if ($e->getCode() !== '23503') {
                throw $e;
            }

            return back()->with('erro', 'Este autor tem livros cadastrados e não pode ser excluído.');
        }

        return redirect()->route('autores.index')->with('sucesso', 'Autor excluído.');
    }
}