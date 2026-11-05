<?php

namespace Tests\Feature\Web;

use App\Models\Emprestimo;
use App\Services\EmprestimoService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class EmprestimoWebTest extends TestCase
{
    use DatabaseTransactions;

    public function test_registra_emprestimo_pela_procedure(): void
    {
        $cliente = $this->criarCliente();
        $livro = $this->criarLivro();

        $resposta = $this->post(route('emprestimos.store'), [
            'cliente_id' => $cliente->id_cliente,
            'livros' => [$livro->id_livro],
            'dias_prazo' => 14,
        ]);

        $emprestimo = Emprestimo::where('cliente_id', $cliente->id_cliente)->firstOrFail();

        $resposta->assertRedirect(route('emprestimos.show', $emprestimo));
        $this->assertSame('em_andamento', $emprestimo->status);
        $this->assertDatabaseHas('itens_emprestimos', [
            'emprestimo_id' => $emprestimo->id_emprestimo,
            'livro_id' => $livro->id_livro,
            'data_devolucao' => null,
        ]);
    }

    public function test_trigger_bloqueia_livro_sem_exemplares(): void
    {
        $cliente = $this->criarCliente();
        $livro = $this->criarLivro(['quantidade_exemplar' => 0]);

        $this->post(route('emprestimos.store'), [
            'cliente_id' => $cliente->id_cliente,
            'livros' => [$livro->id_livro],
            'dias_prazo' => 14,
        ])->assertSessionHas('erro', "O livro de código {$livro->id_livro} não tem exemplares disponíveis.");

        $this->assertDatabaseMissing('emprestimos', ['cliente_id' => $cliente->id_cliente]);
    }

    public function test_devolucao_total_finaliza_emprestimo_pela_trigger(): void
    {
        $cliente = $this->criarCliente();
        $livro = $this->criarLivro();
        $emprestimo = app(EmprestimoService::class)->registrar($cliente->id_cliente, [$livro->id_livro]);

        $this->patch(route('emprestimos.devolver', $emprestimo))
            ->assertRedirect(route('emprestimos.show', $emprestimo));

        $this->assertSame('devolvido', $emprestimo->refresh()->status);
    }
}