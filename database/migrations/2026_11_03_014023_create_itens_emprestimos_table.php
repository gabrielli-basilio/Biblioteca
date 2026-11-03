<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('itens_emprestimos')) {
            return;
        }

        Schema::create('itens_emprestimos', function (Blueprint $table) {
            $table->integer('emprestimo_id');
            $table->integer('livro_id');
            $table->date('data_devolucao')->nullable();

            $table->primary(['emprestimo_id', 'livro_id']);

            $table->foreign('emprestimo_id')->references('id_emprestimo')->on('emprestimos')
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('livro_id')->references('id_livro')->on('livros')
                ->restrictOnDelete()->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('itens_emprestimos');
    }
};
