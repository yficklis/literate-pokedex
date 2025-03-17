<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pokemon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PokemonController extends Controller
{
    /**
     * Lista os Pokémons com filtros opcionais
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $name = $request->query('name');
        $type = $request->query('type');
        $perPage = $request->query('per_page', 20);
        
        $pokemons = Pokemon::findWithFilters($name, $type)
            ->paginate($perPage);
        
        return response()->json($pokemons);
    }
    
    /**
     * Retorna os detalhes de um Pokémon específico
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $pokemon = Pokemon::where('api_id', $id)->orWhere('id', $id)->first();
        
        if (!$pokemon) {
            return response()->json([
                'message' => 'Pokémon não encontrado'
            ], 404);
        }
        
        return response()->json([
            'id' => $pokemon->api_id,
            'name' => $pokemon->name,
            'type' => $pokemon->type,
            'height' => $pokemon->height,
            'height_cm' => $pokemon->height_in_cm,
            'weight' => $pokemon->weight,
            'weight_kg' => $pokemon->weight_in_kg,
            'image_url' => $pokemon->image_url,
        ]);
    }
}
