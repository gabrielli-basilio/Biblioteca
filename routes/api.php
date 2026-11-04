<?php

use App\Http\Controllers\Api\EmprestimoController;
use App\Http\Controllers\Api\LivroController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    Route::apiResource('livros', LivroController::class)
        ->parameters(['livros' => 'livro']);

    Route::apiResource('emprestimos', EmprestimoController::class)
        ->only(['index', 'store', 'show'])
        ->parameters(['emprestimos' => 'emprestimo']);

    Route::patch('emprestimos/{emprestimo}/devolver', [EmprestimoController::class, 'devolver'])
        ->name('emprestimos.devolver');
});