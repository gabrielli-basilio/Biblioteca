<?php

namespace Tests;

use App\Models\Autor;
use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Editora;
use App\Models\Livro;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function criarLivro(array $dados = []): Livro
    {
        return Livro::create(array_merge([
            'titulo' => 'Livro de Teste Automatizado',
            'isbn' => '9789999999999',
            'ano_publicacao' => 2024,
            'quantidade_exemplar' => 2,
            'categoria_id' => Categoria::value('id_categoria'),
            'editora_id' => Editora::value('id_editora'),
            'autor_id' => Autor::value('id_autor'),
        ], $dados));
    }

    protected function criarCliente(array $dados = []): Cliente
    {
        return Cliente::create(array_merge([
            'nome' => 'Cliente de Teste',
            'cpf' => '52998224725',
            'telefone' => '42900000001',
            'email' => 'cliente.teste@exemplo.com',
            'cep' => '85010000',
            'logradouro' => 'Rua XV de Novembro',
            'numero_residencial' => '100',
        ], $dados));
    }
}
