# Biblioteca — Laravel + PostgreSQL

Sistema de empréstimo de livros desenvolvido para o Projeto Avaliativo do 2º bimestre
(Banco de Dados II e Back-end) do curso de Engenharia de Software do Centro Universitário Campo Real.

Parte do banco de dados da biblioteca criado no 1º bimestre por Gabrielli Basilio Ferreira.

## Integrantes

- Gabrielli Basilio Ferreira — banco de dados, objetos SQL e backup
- Gabriel de Oliveira Caldas — migrations, models e API
- João Victor Fortkamp — telas e CRUDs
- Lucas Amaral Santos — empréstimos, testes e documentação

## Stack

- Laravel (PHP 8.2+)
- PostgreSQL hospedado no Neon (schema `bribriotecadb`)
- Blade para as telas, PHPUnit para os testes

## Como rodar

1. Clone e instale as dependências:

   ```bash
   git clone https://github.com/gabrielli-basilio/Biblioteca.git
   cd Biblioteca
   composer install
   cp .env.example .env
   php artisan key:generate
   ```

2. No `.env`, preencha `DB_PASSWORD` com a senha do Neon.
3. Se o banco estiver vazio, rode os scripts de `database/sql/` na ordem (00 a 07).
   No banco do projeto eles já foram executados.
4. Registre as migrations (as tabelas existentes não são alteradas):

   ```bash
   php artisan migrate
   ```

5. Suba o servidor e acesse `http://localhost:8000`:

   ```bash
   php artisan serve
   ```

## Scripts SQL

| Arquivo | Conteúdo |
| --- | --- |
| `00_search_path.sql` | Define o schema padrão do banco |
| `01_ddl.sql` | Criação do schema e das 7 tabelas |
| `02_dml.sql` | População (1.000 clientes, 3.000 livros, 10.000 empréstimos) |
| `03_relatorios.sql` | 5 views e 2 índices do 1º bimestre |
| `04_adequacoes.sql` | Constraints CHECK e índice de empréstimos por cliente |
| `05_functions.sql` | `fn_exemplares_disponiveis`, `fn_calcular_multa` |
| `06_triggers.sql` | `trg_valida_disponibilidade`, `trg_finaliza_emprestimo` |
| `07_procedures.sql` | `sp_registrar_emprestimo`, `sp_registrar_devolucao`, `sp_atualizar_atrasados` |
| `08_testes_objetos.sql` | Consultas para testar os objetos |
| `09_backup_restore.md` | Passo a passo do backup e da restauração |

O backup fica em `database/backups/` e as evidências em `docs/evidencias/`.

## Rotas web

| Rota | Descrição |
| --- | --- |
| `GET /` | Painel com totais, livros mais emprestados e maiores atrasos |
| `POST /atualizar-atrasados` | Executa `sp_atualizar_atrasos` |
| `/livros` | CRUD de livros, com busca por título/ISBN e filtro por categoria |
| `/clientes` | CRUD de clientes, com validação de CPF |
| `/autores`, `/editoras`, `/categorias` | CRUD das tabelas de apoio |
| `GET /emprestimos` | Lista de empréstimos com filtro por status |
| `GET /emprestimos/create` · `POST /emprestimos` | Novo empréstimo (procedure) |
| `GET /emprestimos/{id}` | Detalhe com multa calculada pela function |
| `PATCH /emprestimos/{id}/devolver` | Devolução de um livro ou de todos (procedure + trigger) |

## API REST

Todas as respostas são JSON. Envie os cabeçalhos Accept: application/json e Content-Type: application/json.

| Método | Rota | Descrição | Sucesso |
| --- | --- | --- | --- |
| GET | /api/livros | Lista paginada (busca, categoria_id, por_pagina) | 200 |
| POST | /api/livros | Cria livro | 201 |
| GET | /api/livros/{id} | Detalhe + exemplares disponíveis | 200 |
| PUT/PATCH | /api/livros/{id} | Atualiza (PATCH aceita campos parciais) | 200 |
| DELETE | /api/livros/{id} | Exclui (409 se já foi emprestado) | 204 |
| GET | /api/emprestimos | Lista paginada (status) | 200 |
| POST | /api/emprestimos | Registra empréstimo via procedure | 201 |
| GET | /api/emprestimos/{id} | Detalhe + multa | 200 |
| PATCH | /api/emprestimos/{id}/devolver | Devolve (livro_id opcional) | 200 |

Erros: 404 para id inexistente, 422 para dados inválidos ou regra recusada pelo banco.

Exemplo — criar livro:

    POST /api/livros
    {
      "titulo": "O Hobbit",
      "isbn": "9788595084742",
      "ano_publicacao": 2019,
      "quantidade_exemplar": 3,
      "categoria_id": 3,
      "editora_id": 1,
      "autor_id": 30
    }

Exemplo — registrar empréstimo:

    POST /api/emprestimos
    {
      "cliente_id": 1001,
      "livros": [1, 2],
      "dias_prazo": 14
    }

Resposta 422 quando o cliente tem atraso:

    { "message": "Cliente 12 possui empréstimos em atraso" }

## Testes

Os testes rodam num branch testes do Neon (cópia do banco com functions, procedures e triggers).
Cada teste roda numa transação desfeita no final; nada fica gravado.

1. No Neon, crie o branch testes a partir de production.
2. Configure o ambiente:

        cp .env.testing.example .env.testing
        php artisan key:generate --env=testing

3. No arquivo .env.testing, preencha DB_HOST (host do branch) e DB_PASSWORD.
4. Rode:

        php artisan test

| Arquivo | O que testa |
| --- | --- |
| Web/LivroWebTest | Listagem e detalhe de livros (function de disponibilidade) |
| Web/ClienteWebTest | Cadastro de cliente e recusa de CPF inválido |
| Web/EmprestimoWebTest | Procedure de empréstimo, trigger de disponibilidade e de devolução |
| Api/LivroApiConsultaTest | Estrutura JSON da listagem, detalhe e 404 |
| Api/LivroApiEscritaTest | 201, 422, PATCH e 204 |
| Api/EmprestimoApiTest | 201, bloqueio por atraso (422) e devolução |