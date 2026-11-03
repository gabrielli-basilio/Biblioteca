<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('clientes')) {
            return;
        }

        Schema::create('clientes', function (Blueprint $table) {
            $table->increments('id_cliente');
            $table->string('nome', 150);
            $table->char('cpf', 11)->unique();
            $table->char('telefone', 11)->unique();
            $table->string('email', 150)->unique();
            $table->char('cep', 8);
            $table->string('logradouro', 150);
            $table->string('numero_residencial', 6);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
