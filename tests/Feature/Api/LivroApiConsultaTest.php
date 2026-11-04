<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LivroApiConsultaTest extends TestCase
{
    use DatabaseTransactions;

    public function test_listagem_retorna_json_paginado(): void
    {
        $this->criarLivro();

        $this->getJson(route('api.livros.index', ['busca' => 'Livro de Teste Automatizado']))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    ['id', 'titulo', 'isbn', 'ano_publicacao', 'quantidade_exemplar', 'categoria', 'editora', 'autor'],
                ],
                'links',
                'meta' => ['current_page', 'per_page', 'total'],
            ])
            ->assertJsonPath('data.0.titulo', 'Livro de Teste Automatizado');
    }

    public function test_detalhe_retorna_livro_com_exemplares_disponiveis(): void
    {
        $livro = $this->criarLivro(['quantidade_exemplar' => 4]);

        $this->getJson(route('api.livros.show', $livro))
            ->assertOk()
            ->assertJsonPath('data.id', $livro->id_livro)
            ->assertJsonPath('meta.exemplares_disponiveis', 4);
    }

    public function test_livro_inexistente_retorna_404(): void
    {
        $this->getJson('/api/livros/99999999')
            ->assertNotFound()
            ->assertJson(['message' => 'Registro não encontrado.']);
    }
}