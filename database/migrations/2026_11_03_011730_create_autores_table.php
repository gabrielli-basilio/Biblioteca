<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('autores')) {
            return;
        }

        Schema::create('autores', function (Blueprint $table) {
            $table->increments('id_autor');
            $table->string('nome', 150);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('autores');
    }
};
