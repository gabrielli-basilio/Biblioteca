<?php

namespace Tests\Feature\Api;

use App\Models\Autor;
use App\Models\Editora;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LivroApiEscritaTest extends TestCase
{
    use DatabaseTransactions;

    public function test_cria_livro_e_retorna_201(): void
    {
        $this->postJson('/api/livros', [
            'titulo' => 'Livro Criado pela API',
            'isbn' => '9789999999998',
            'ano_publicacao' => 2025,
            'quantidade_exemplar' => 3,
            'editora_id' => Editora::value('id_editora'),
            'autor_id' => Autor::value('id_autor'),
        ])
            ->assertCreated()
            ->assertJsonPath('data.titulo', 'Livro Criado pela API');

        $this->assertDatabaseHas('livros', ['isbn' => '9789999999998']);
    }

    public function test_dados_invalidos_retornam_422_com_erros(): void
    {
        $this->postJson('/api/livros', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['titulo', 'isbn', 'ano_publicacao', 'quantidade_exemplar', 'editora_id', 'autor_id']);
    }

    public function test_atualiza_parcialmente_com_patch(): void
    {
        $livro = $this->criarLivro();

        $this->patchJson("/api/livros/{$livro->id_livro}", ['titulo' => 'Título Atualizado'])
            ->assertOk()
            ->assertJsonPath('data.titulo', 'Título Atualizado')
            ->assertJsonPath('data.isbn', '9789999999999');
    }

    public function test_exclui_livro_e_retorna_204(): void
    {
        $livro = $this->criarLivro();

        $this->deleteJson("/api/livros/{$livro->id_livro}")->assertNoContent();

        $this->assertDatabaseMissing('livros', ['id_livro' => $livro->id_livro]);
    }
}