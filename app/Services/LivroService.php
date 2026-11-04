<?php

namespace App\Services;

use App\Exceptions\RegraNegocioException;
use App\Models\Livro;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class LivroService
{
    public function listar(?string $busca = null, ?int $categoriaId = null, int $porPagina = 15): LengthAwarePaginator
    {
        return Livro::with(['categoria', 'editora', 'autor'])
            ->when($busca, fn ($q, $busca) => $q->where(fn ($sub) => $sub
                ->where('titulo', 'ilike', "%{$busca}%")
                ->orWhere('isbn', 'like', "%{$busca}%")))
            ->when($categoriaId, fn ($q, $categoriaId) => $q->where('categoria_id', $categoriaId))
            ->orderBy('titulo')
            ->paginate(min($porPagina, 100))
            ->withQueryString();
    }

    public function criar(array $dados): Livro
    {
        return Livro::create($dados);
    }

    public function atualizar(Livro $livro, array $dados): Livro
    {
        $livro->update($dados);

        return $livro->refresh();
    }

    public function excluir(Livro $livro): void
    {
        try {
            $livro->delete();
        } catch (QueryException $e) {
            if ($e->getCode() !== '23503') {
                throw $e;
            }

            throw new RegraNegocioException('Este livro já foi emprestado e não pode ser excluído.');
        }
    }

    public function exemplaresDisponiveis(Livro $livro): int
    {
        return (int) DB::scalar('SELECT fn_gabrielli_exemplares_disponiveis(?)', [$livro->id_livro]);
    }
}
