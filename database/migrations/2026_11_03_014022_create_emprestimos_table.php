<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('emprestimos')) {
            return;
        }

        Schema::create('emprestimos', function (Blueprint $table) {
            $table->increments('id_emprestimo');
            $table->date('data_emprestimo');
            $table->date('data_prevista_devolucao');
            $table->string('status', 50)->default('em_andamento');
            $table->integer('cliente_id');

            $table->foreign('cliente_id')->references('id_cliente')->on('clientes')
                ->restrictOnDelete()->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emprestimos');
    }
};
