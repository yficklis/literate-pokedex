<?php

use App\Http\Controllers\Api\PokemonController;
use App\Http\Controllers\Api\PokemonTypeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rotas para Pokémons
Route::get('/pokemons', [PokemonController::class, 'index']);
Route::get('/pokemons/{id}', [PokemonController::class, 'show']);

// Rota para tipos de Pokémon
Route::get('/pokemon-types', [PokemonTypeController::class, 'index']); 