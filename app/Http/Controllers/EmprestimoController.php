<?php

namespace App\Http\Controllers;

use App\Exceptions\RegraNegocioException;
use App\Models\Cliente;
use App\Models\Emprestimo;
use App\Services\EmprestimoService;
use Illuminate\Http\Request;

class EmprestimoController extends Controller
{
    public function __construct(private EmprestimoService $service)
    {
    }

    public function index(Request $request)
    {
        $emprestimos = $this->service->listar($request->status);

        return view('emprestimos.index', compact('emprestimos'));
    }

    public function create()
    {
        $clientes = Cliente::orderBy('nome')->get(['id_cliente', 'nome', 'cpf']);

        return view('emprestimos.create', compact('clientes'));
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'cliente_id' => ['required', 'integer', 'exists:clientes,id_cliente'],
            'livros' => ['required', 'array', 'max:5'],
            'livros.*' => ['nullable', 'integer', 'min:1'],
            'dias_prazo' => ['required', 'integer', 'min:1', 'max:60'],
        ], [], ['cliente_id' => 'cliente', 'dias_prazo' => 'prazo']);

        try {
            $emprestimo = $this->service->registrar($dados['cliente_id'], $dados['livros'], $dados['dias_prazo']);
        } catch (RegraNegocioException $e) {
            return back()->withInput()->with('erro', $e->getMessage());
        }

        return redirect()->route('emprestimos.show', $emprestimo)->with('sucesso', 'Empréstimo registrado.');
    }

    public function show(Emprestimo $emprestimo)
    {
        $emprestimo->load(['cliente', 'livros']);
        $multa = $this->service->multa($emprestimo);
        $pendentes = $emprestimo->livros->filter(fn ($livro) => is_null($livro->pivot->data_devolucao))->count();

        return view('emprestimos.show', compact('emprestimo', 'multa', 'pendentes'));
    }

    public function devolver(Request $request, Emprestimo $emprestimo)
    {
        try {
            $this->service->devolver($emprestimo, $request->integer('livro_id') ?: null);
        } catch (RegraNegocioException $e) {
            return back()->with('erro', $e->getMessage());
        }

        return redirect()->route('emprestimos.show', $emprestimo)->with('sucesso', 'Devolução registrada.');
    }
}
