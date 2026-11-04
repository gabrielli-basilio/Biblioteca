<?php

namespace App\Http\Controllers;

use App\Models\Editora;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class EditoraController extends Controller
{
    public function index(Request $request)
    {
        $editoras = Editora::withCount('livros')
            ->when($request->busca, fn ($q, $busca) => $q->where('nome', 'ilike', "%{$busca}%"))
            ->orderBy('nome')
            ->paginate(15)
            ->withQueryString();

        return view('editoras.index', compact('editoras'));
    }

    public function create()
    {
        return view('editoras.create');
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:150'],
        ]);

        Editora::create($dados);

        return redirect()->route('editoras.index')->with('sucesso', 'Editora cadastrada.');
    }

    public function edit(Editora $editora)
    {
        return view('editoras.edit', compact('editora'));
    }

    public function update(Request $request, Editora $editora)
    {
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:150'],
        ]);

        $editora->update($dados);

        return redirect()->route('editoras.index')->with('sucesso', 'Editora atualizada.');
    }

    public function destroy(Editora $editora)
    {
        try {
            $editora->delete();
        } catch (QueryException $e) {
            if ($e->getCode() !== '23503') {
                throw $e;
            }

            return back()->with('erro', 'Esta editora tem livros cadastrados e não pode ser excluída.');
        }

        return redirect()->route('editoras.index')->with('sucesso', 'Editora excluída.');
    }
}