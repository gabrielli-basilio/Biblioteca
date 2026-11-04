<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClienteRequest;
use App\Models\Cliente;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $clientes = Cliente::withCount('emprestimos')
            ->when($request->busca, function ($q, $busca) {
                $digitos = preg_replace('/\D/', '', $busca);
                $q->where(function ($sub) use ($busca, $digitos) {
                    $sub->where('nome', 'ilike', "%{$busca}%");
                    if ($digitos !== '') {
                        $sub->orWhere('cpf', 'like', "%{$digitos}%");
                    }
                });
            })
            ->orderBy('nome')
            ->paginate(15)
            ->withQueryString();
        return view('clientes.index', compact('clientes'));
    }
    
    public function create()
    {
        return view('clientes.create');
    }

    public function store(ClienteRequest $request)
    {
        $cliente = Cliente::create($request->validated());
        return redirect()->route('clientes.show', $cliente)->with(
            'sucesso',
            'Cliente cadastrado.'
        );
    }

    public function show(Cliente $cliente)
    {
        $emprestimos = $cliente->emprestimos()
            ->withCount('livros')
            ->orderByDesc('id_emprestimo')
            ->limit(20)
            ->get();
        return view('clientes.show', compact('cliente', 'emprestimos'));
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(ClienteRequest $request, Cliente $cliente)
    {
        $cliente->update($request->validated());
        return redirect()->route('clientes.show', $cliente)->with(
            'sucesso',
            'Cliente atualizado.'
        );
    }

    public function destroy(Cliente $cliente)
    {
        try {
            $cliente->delete();
        } catch (QueryException $e) {
            if ($e->getCode() !== '23503') {
                throw $e;
            }
            return back()->with('erro', 'Este cliente tem empréstimos
registrados e não pode ser excluído.');
        }
        return redirect()->route('clientes.index')->with('sucesso', 'Cliente
excluído.');
    }
}
