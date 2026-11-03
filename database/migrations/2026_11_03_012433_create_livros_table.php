<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('livros')) {
            return;
        }

        Schema::create('livros', function (Blueprint $table) {
            $table->increments('id_livro');
            $table->string('titulo', 150);
            $table->string('isbn', 13)->unique();
            $table->integer('ano_publicacao');
            $table->integer('quantidade_exemplar')->default(1);
            $table->integer('categoria_id')->nullable();
            $table->integer('editora_id');
            $table->integer('autor_id');

            $table->foreign('categoria_id')->references('id_categoria')->on('categorias')
                ->nullOnDelete()->cascadeOnUpdate();
            $table->foreign('editora_id')->references('id_editora')->on('editoras')
                ->restrictOnDelete()->cascadeOnUpdate();
            $table->foreign('autor_id')->references('id_autor')->on('autores')
                ->restrictOnDelete()->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livros');
    }
};
