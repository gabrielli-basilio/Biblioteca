<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoriaController extends Controller
{
    public function index(Request $request)
    {
        $categorias = Categoria::withCount('livros')
            ->when($request->busca, fn ($q, $busca) => $q->where('nome', 'ilike', "%{$busca}%"))
            ->orderBy('nome')
            ->paginate(15)
            ->withQueryString();

        return view('categorias.index', compact('categorias'));
    }

    public function create()
    {
        return view('categorias.create');
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:150', 'unique:categorias,nome'],
        ]);

        Categoria::create($dados);

        return redirect()->route('categorias.index')->with('sucesso', 'Categoria cadastrada.');
    }

    public function edit(Categoria $categoria)
    {
        return view('categorias.edit', compact('categoria'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        $dados = $request->validate([
            'nome' => [
                'required', 'string', 'max:150',
                Rule::unique('categorias', 'nome')->ignore($categoria->id_categoria, 'id_categoria'),
            ],
        ]);

        $categoria->update($dados);

        return redirect()->route('categorias.index')->with('sucesso', 'Categoria atualizada.');
    }

    public function destroy(Categoria $categoria)
    {
        $categoria->delete();

        return redirect()->route('categorias.index')
            ->with('sucesso', 'Categoria excluída. Os livros dela ficaram sem categoria.');
    }
}