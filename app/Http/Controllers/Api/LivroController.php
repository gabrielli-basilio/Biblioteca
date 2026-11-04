<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\RegraNegocioException;
use App\Http\Controllers\Controller;
use App\Http\Requests\LivroRequest;
use App\Http\Resources\LivroResource;
use App\Models\Livro;
use App\Services\LivroService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LivroController extends Controller
{
    public function __construct(private LivroService $service)
    {
    }

    public function index(Request $request)
    {
        $livros = $this->service->listar(
            $request->busca,
            $request->integer('categoria_id') ?: null,
            $request->integer('por_pagina', 15)
        );

        return LivroResource::collection($livros);
    }

    public function store(LivroRequest $request)
    {
        $livro = $this->service->criar($request->validated());

        return (new LivroResource($livro->load(['categoria', 'editora', 'autor'])))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Livro $livro)
    {
        return (new LivroResource($livro->load(['categoria', 'editora', 'autor'])))
            ->additional(['meta' => [
                'exemplares_disponiveis' => $this->service->exemplaresDisponiveis($livro),
            ]]);
    }

    public function update(LivroRequest $request, Livro $livro)
    {
        $livro = $this->service->atualizar($livro, $request->validated());

        return new LivroResource($livro->load(['categoria', 'editora', 'autor']));
    }

    public function destroy(Livro $livro)
    {
        try {
            $this->service->excluir($livro);
        } catch (RegraNegocioException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_CONFLICT);
        }

        return response()->noContent();
    }
}
