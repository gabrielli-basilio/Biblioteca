# Biblioteca — Laravel + PostgreSQL

Sistema de empréstimo de livros desenvolvido para o Projeto Avaliativo do 2º bimestre
(Banco de Dados II e Back-end) do curso de Engenharia de Software do Centro Universitário Campo Real.
Parte do banco de dados da biblioteca criado no 1º bimestre por Gabrielli Basilio Ferreira.

## Integrantes

- Gabrielli Basilio Ferreira — banco de dados, objetos SQL e backup
- Biel — migrations, models e API
- João Victor Fortkamp — telas e CRUDs
- Lucas — empréstimos, testes e documentação

## Stack

- Laravel (PHP 8.2+)
- PostgreSQL hospedado no Neon (schema `bribrioteca_bd`)
- Blade para as telas, PHPUnit para os testes

## Como rodar

1. Clone e instale as dependências:

   ```bash
   git clone https://github.com/USUARIO/biblioteca.git
   cd biblioteca
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
