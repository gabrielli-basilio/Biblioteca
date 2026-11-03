<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('categorias')) {
            return;
        }

        Schema::create('categorias', function (Blueprint $table) {
            $table->increments('id_categoria');
            $table->string('nome', 150)->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categorias');
    }
};
