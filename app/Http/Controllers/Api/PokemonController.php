<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pokemon;
use App\Http\Resources\PokemonResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\PokemonApiService;

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
        
        $query = Pokemon::findWithFiltersAndApi($name, $type);
        
        // Se estiver buscando por tipo e não encontrar resultados, tenta buscar na API pública
        if ($type && $query->count() === 0) {
            // Busca na API
            $apiService = app(PokemonApiService::class);
            $pokemons = $apiService->findPokemonsByType($type);
            
            // Se encontrou Pokémons na API, refaz a consulta para incluí-los nos resultados
            if (!empty($pokemons)) {
                $query = Pokemon::findWithFiltersAndApi($name, $type);
            }
        }
        
        $pokemons = $query->paginate($perPage);
        
        return response()->json(PokemonResource::collection($pokemons)->response()->getData(true));
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
        
        return response()->json(new PokemonResource($pokemon));
    }

    /**
     * Retorna uma lista de nomes de Pokémon para autocomplete
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function autocomplete(Request $request): JsonResponse
    {
        $query = $request->input('query', '');
        
        // Busca Pokémon pelo nome
        $pokemons = Pokemon::where('name', 'LIKE', "%{$query}%")
            ->orderBy('name')
            ->limit(10)
            ->pluck('name');
        
        // Se não encontrar resultados e tiver um termo de busca, tenta buscar na API
        if ($pokemons->isEmpty() && !empty($query)) {
            $apiService = app(PokemonApiService::class);
            $pokemon = $apiService->findPokemonByName($query);
            
            if ($pokemon) {
                $pokemons = collect([$pokemon->name]);
            }
        }
        
        return response()->json($pokemons);
    }
}
