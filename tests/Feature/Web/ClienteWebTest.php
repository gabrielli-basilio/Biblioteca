<?php

namespace Tests\Feature\Web;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ClienteWebTest extends TestCase
{
    use DatabaseTransactions;

    private function dados(array $alteracoes = []): array
    {
        return array_merge([
            'nome' => 'Cliente Web Teste',
            'cpf' => '529.982.247-25',
            'telefone' => '(42) 90000-0001',
            'email' => 'cliente.web@exemplo.com',
            'cep' => '85010-000',
            'logradouro' => 'Rua XV de Novembro',
            'numero_residencial' => '100',
        ], $alteracoes);
    }

    public function test_cadastro_de_cliente_grava_no_banco_sem_pontuacao(): void
    {
        $this->post(route('clientes.store'), $this->dados())
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('clientes', [
            'email' => 'cliente.web@exemplo.com',
            'cpf' => '52998224725',
            'telefone' => '42900000001',
            'cep' => '85010000',
        ]);
    }

    public function test_cadastro_recusa_cpf_invalido(): void
    {
        $this->post(route('clientes.store'), $this->dados(['cpf' => '123.456.789-00']))
            ->assertSessionHasErrors('cpf');

        $this->assertDatabaseMissing('clientes', ['email' => 'cliente.web@exemplo.com']);
    }
}