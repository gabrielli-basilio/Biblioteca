<?php

namespace Tests\Feature\Web;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LivroWebTest extends TestCase
{
    use DatabaseTransactions;

    public function test_listagem_exibe_livro_cadastrado(): void
    {
        $this->criarLivro();

        $this->get(route('livros.index', ['busca' => 'Livro de Teste Automatizado']))
            ->assertOk()
            ->assertSee('Livro de Teste Automatizado');
    }

    public function test_detalhe_mostra_exemplares_disponiveis_calculados_pela_function(): void
    {
        $livro = $this->criarLivro(['quantidade_exemplar' => 3]);

        $this->get(route('livros.show', $livro))
            ->assertOk()
            ->assertSee('Disponíveis agora')
            ->assertViewHas('disponiveis', 3);
    }
}