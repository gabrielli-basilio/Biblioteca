<?php

use App\Http\Controllers\CategoriaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmprestimoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AutorController;
use App\Http\Controllers\EditoraController;

Route::get('/', [DashboardController::class, 'index'])->name('home');
Route::post('/atualizar-atrasados', [DashboardController::class, 'atualizarAtrasados'])->name('atrasados.atualizar');

Route::resource('categorias', CategoriaController::class)
    ->except('show')
    ->parameters(['categorias' => 'categoria']);

Route::resource('emprestimos', EmprestimoController::class)
    ->only(['index', 'create', 'store', 'show'])
    ->parameters(['emprestimos' => 'emprestimo']);

Route::patch('emprestimos/{emprestimo}/devolver', [EmprestimoController::class, 'devolver'])
    ->name('emprestimos.devolver');

Route::resource('autores', AutorController::class)
    ->except('show')
    ->parameters(['autores' => 'autor']);
    
Route::resource('editoras', EditoraController::class)
    ->except('show')
    ->parameters(['editoras' => 'editora']);