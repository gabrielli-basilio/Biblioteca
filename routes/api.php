<?php

use App\Http\Controllers\Api\LivroController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    Route::apiResource('livros', LivroController::class)
        ->parameters(['livros' => 'livro']);
});
