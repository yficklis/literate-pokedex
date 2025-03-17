<?php

namespace App\Http\Controllers;

use App\Models\Pokemon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PokemonController extends Controller
{
    /**
     * Exibe a listagem de Pokémons
     * 
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $name = $request->query('name');
        $type = $request->query('type');
        
        $pokemons = Pokemon::findWithFilters($name, $type)
            ->paginate(12)
            ->withQueryString();
        
        return Inertia::render('Pokemon/Index', [
            'pokemons' => $pokemons,
            'filters' => [
                'name' => $name,
                'type' => $type,
            ],
        ]);
    }
    
    /**
     * Exibe os detalhes de um Pokémon específico
     * 
     * @param int $id
     * @return Response
     */
    public function show(int $id): Response
    {
        $pokemon = Pokemon::where('api_id', $id)->orWhere('id', $id)->firstOrFail();
        
        return Inertia::render('Pokemon/Show', [
            'pokemon' => [
                'id' => $pokemon->id,
                'api_id' => $pokemon->api_id,
                'name' => $pokemon->name,
                'type' => $pokemon->type,
                'height' => $pokemon->height,
                'height_cm' => $pokemon->height_in_cm,
                'weight' => $pokemon->weight,
                'weight_kg' => $pokemon->weight_in_kg,
                'image_url' => $pokemon->image_url,
            ],
        ]);
    }
}
