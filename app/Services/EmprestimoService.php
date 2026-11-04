<?php

namespace App\Services;

use App\Exceptions\RegraNegocioException;
use App\Models\Emprestimo;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class EmprestimoService
{
    public function listar(?string $status = null, int $porPagina = 15): LengthAwarePaginator
    {
        return Emprestimo::with('cliente')
            ->withCount('livros')
            ->when($status, fn ($q, $status) => $q->where('status', $status))
            ->orderByDesc('id_emprestimo')
            ->paginate($porPagina)
            ->withQueryString();
    }

    public function registrar(int $clienteId, array $livros, int $diasPrazo = 14): Emprestimo
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $livros))));
        $arrayPostgres = '{' . implode(',', $ids) . '}';

        try {
            $resultado = DB::transaction(fn () => DB::selectOne(
                'CALL sp_gabrielli_registrar_emprestimo(?, ?::int[], ?, NULL)',
                [$clienteId, $arrayPostgres, $diasPrazo]
            ));
        } catch (QueryException $e) {
            throw $this->converterErro($e);
        }

        return Emprestimo::findOrFail($resultado->p_emprestimo_id);
    }

    public function devolver(Emprestimo $emprestimo, ?int $livroId = null): void
    {
        try {
            DB::transaction(fn () => DB::statement(
                'CALL sp_gabrielli_registrar_devolucao(?, ?)',
                [$emprestimo->id_emprestimo, $livroId]
            ));
        } catch (QueryException $e) {
            throw $this->converterErro($e);
        }
    }

    public function multa(Emprestimo $emprestimo): float
    {
        return (float) DB::scalar('SELECT fn_calcular_multa(?)', [$emprestimo->id_emprestimo]);
    }

    public function atualizarAtrasados(): void
    {
        DB::statement('CALL sp_gabrielli_atualizar_atrasados()');
    }

    private function converterErro(QueryException $e): \Throwable
    {
        if ($e->getCode() !== 'P0001') {
            return $e;
        }

        $mensagem = strtok($e->errorInfo[2] ?? $e->getMessage(), "\n");

        return new RegraNegocioException(preg_replace('/^ERROR:\s*/', '', $mensagem));
    }
}