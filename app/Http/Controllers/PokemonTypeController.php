<?php

namespace App\Http\Controllers;

use App\Models\Pokemon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
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
        // Obtém todos os tipos de todos os Pokémon
        $allTypes = new Collection();
        
        // Busca todos os Pokémon e extrai os tipos
        Pokemon::select('types')->get()->each(function ($pokemon) use ($allTypes) {
            if (is_array($pokemon->types)) {
                foreach ($pokemon->types as $type) {
                    $allTypes->push($type);
                }
            }
        });
        
        // Remove duplicatas e ordena
        $types = $allTypes->unique()->sort()->values();
        
        return Inertia::render('Pokemon/Types', [
            'types' => $types,
        ]);
    }
}
