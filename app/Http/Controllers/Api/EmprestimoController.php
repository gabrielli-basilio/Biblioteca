<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\RegraNegocioException;
use App\Http\Controllers\Controller;
use App\Http\Resources\EmprestimoResource;
use App\Models\Emprestimo;
use App\Services\EmprestimoService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmprestimoController extends Controller
{
    public function __construct(private EmprestimoService $service)
    {
    }

    public function index(Request $request)
    {
        return EmprestimoResource::collection($this->service->listar($request->status));
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'cliente_id' => ['required', 'integer', 'exists:clientes,id_cliente'],
            'livros' => ['required', 'array', 'min:1', 'max:5'],
            'livros.*' => ['integer', 'min:1'],
            'dias_prazo' => ['nullable', 'integer', 'min:1', 'max:60'],
        ]);

        try {
            $emprestimo = $this->service->registrar(
                $dados['cliente_id'],
                $dados['livros'],
                $dados['dias_prazo'] ?? 14
            );
        } catch (RegraNegocioException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return (new EmprestimoResource($emprestimo->load(['cliente', 'livros'])))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Emprestimo $emprestimo)
    {
        return (new EmprestimoResource($emprestimo->load(['cliente', 'livros'])))
            ->additional(['meta' => ['multa' => $this->service->multa($emprestimo)]]);
    }

    public function devolver(Request $request, Emprestimo $emprestimo)
    {
        try {
            $this->service->devolver($emprestimo, $request->integer('livro_id') ?: null);
        } catch (RegraNegocioException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return new EmprestimoResource($emprestimo->refresh()->load(['cliente', 'livros']));
    }
}