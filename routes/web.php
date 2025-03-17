<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PokemonController;
use App\Http\Controllers\PokemonTypeController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('pokemons.index');
});

// Rotas para Pokémons
Route::get('/pokemons', [PokemonController::class, 'index'])->name('pokemons.index');
Route::get('/pokemons/{id}', [PokemonController::class, 'show'])->name('pokemons.show');

// Rota para tipos de Pokémon
Route::get('/pokemon-types', [PokemonTypeController::class, 'index'])->name('pokemon-types.index');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
