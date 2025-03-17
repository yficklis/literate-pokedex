<?php

namespace App\Http\Controllers;

use App\Models\Pokemon;
use Inertia\Inertia;
use Inertia\Response;

class PokemonTypeController extends Controller
{
    /**
     * Lista todos os tipos de Pokémon disponíveis
     * 
     * @return Response
     */
    public function index(): Response
    {
        $types = Pokemon::select('type')
            ->distinct()
            ->orderBy('type')
            ->pluck('type');
        
        return Inertia::render('Pokemon/Types', [
            'types' => $types,
        ]);
    }
}
