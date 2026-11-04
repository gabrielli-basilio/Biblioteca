<?php

namespace Tests\Feature\Api;

use App\Models\Emprestimo;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class EmprestimoApiTest extends TestCase
{
    use DatabaseTransactions;

    public function test_registra_emprestimo_e_retorna_201(): void
    {
        $cliente = $this->criarCliente();
        $livro = $this->criarLivro();

        $this->postJson('/api/emprestimos', [
            'cliente_id' => $cliente->id_cliente,
            'livros' => [$livro->id_livro],
        ])
            ->assertCreated()
            ->assertJsonPath('data.status', 'em_andamento')
            ->assertJsonPath('data.cliente.id', $cliente->id_cliente)
            ->assertJsonCount(1, 'data.livros');
    }

    public function test_procedure_bloqueia_cliente_com_atraso(): void
    {
        $cliente = $this->criarCliente();
        $livro = $this->criarLivro();

        $atrasado = Emprestimo::create([
            'cliente_id' => $cliente->id_cliente,
            'data_emprestimo' => now()->subDays(30)->toDateString(),
            'data_prevista_devolucao' => now()->subDays(16)->toDateString(),
            'status' => 'atrasado',
        ]);
        $atrasado->livros()->attach($livro->id_livro);

        $this->postJson('/api/emprestimos', [
            'cliente_id' => $cliente->id_cliente,
            'livros' => [$livro->id_livro],
        ])
            ->assertUnprocessable()
            ->assertJsonPath('message', "Cliente {$cliente->id_cliente} possui empréstimos em atraso");
    }

    public function test_devolucao_pela_api_muda_status_para_devolvido(): void
    {
        $cliente = $this->criarCliente();
        $livro = $this->criarLivro();

        $id = $this->postJson('/api/emprestimos', [
            'cliente_id' => $cliente->id_cliente,
            'livros' => [$livro->id_livro],
        ])->json('data.id');

        $this->patchJson("/api/emprestimos/{$id}/devolver")
            ->assertOk()
            ->assertJsonPath('data.status', 'devolvido');
    }
}