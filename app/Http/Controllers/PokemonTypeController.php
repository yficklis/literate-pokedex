<?php

namespace App\Http\Controllers;

use App\Models\Pokemon;
use App\Services\PokemonApiService;
use Illuminate\Http\Request;
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
        
        // Se não houver tipos na base local, busca na API
        if ($types->isEmpty()) {
            $apiService = app(PokemonApiService::class);
            $apiTypes = $apiService->getAllTypes();
            $types = collect($apiTypes)->sort()->values();
        }
        
        return Inertia::render('Pokemon/Types', [
            'types' => $types,
        ]);
    }
    
    /**
     * Retorna uma lista de tipos para autocomplete
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function autocomplete(Request $request)
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
