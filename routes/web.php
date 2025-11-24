<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CarroController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\VendaController;

Route::get('/', function () {
    return view('welcome');
});

// Rotas para Carros
Route::resource('carros', CarroController::class);

// Rotas para Marcas
Route::resource('marcas', MarcaController::class)->except(['show']);

// Rotas para Categorias
Route::resource('categorias', CategoriaController::class)->except(['show']);

// Rotas para Clientes
Route::resource('clientes', ClienteController::class)->except(['show']);

// Rotas para Vendas
Route::resource('vendas', VendaController::class)->except(['edit', 'update']);