<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pokemon;
use App\Services\PokemonApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TypeController extends Controller
{
    /**
     * Retorna sugestões de tipos de Pokémon para autocomplete
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function autocomplete(Request $request)
    {
        $query = $request->input('query', '');
        
        // Se a consulta estiver vazia, retorna array vazio
        if (empty($query)) {
            return response()->json([]);
        }
        
        // Busca tipos no banco de dados local
        $types = Pokemon::select('types')
            ->get()
            ->pluck('types')
            ->flatten()
            ->filter(function ($type) use ($query) {
                return stripos($type, $query) === 0;
            })
            ->unique()
            ->values()
            ->toArray();
        
        // Se não encontrou tipos localmente, busca na API
        if (empty($types)) {
            $apiService = app(PokemonApiService::class);
            $apiTypes = $apiService->getAllTypes();
            
            $types = collect($apiTypes)
                ->filter(function ($type) use ($query) {
                    return stripos($type, $query) === 0;
                })
                ->values()
                ->toArray();
        }
        
        return response()->json($types);
    }
} 