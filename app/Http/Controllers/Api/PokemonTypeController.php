<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pokemon;
use App\Services\PokemonApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class PokemonTypeController extends Controller
{
    /**
     * Lista todos os tipos de Pokémon disponíveis
     * 
     * @return JsonResponse
     */
    public function index(): JsonResponse
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
        
        // Se não houver tipos na base local, busca na API
        if ($types->isEmpty()) {
            $apiService = app(PokemonApiService::class);
            $apiTypes = $apiService->getAllTypes();
            $types = collect($apiTypes)->sort()->values();
        }
        
        return response()->json($types);
    }
    
    /**
     * Retorna uma lista de tipos para autocomplete
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function autocomplete(Request $request): JsonResponse
    {
        $query = $request->input('query', '');
        
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
        
        // Se não houver tipos na base local, busca na API
        if ($types->isEmpty()) {
            $apiService = app(PokemonApiService::class);
            $apiTypes = $apiService->getAllTypes();
            $types = collect($apiTypes)->sort()->values();
        }
        
        // Filtra os tipos pelo termo de busca
        if (!empty($query)) {
            $types = $types->filter(function ($type) use ($query) {
                return stripos($type, $query) !== false;
            })->values();
        }
        
        return response()->json($types);
    }
}
