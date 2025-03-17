<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pokemon;
use Illuminate\Http\JsonResponse;

class PokemonTypeController extends Controller
{
    /**
     * Lista todos os tipos de Pokémon disponíveis
     * 
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $types = Pokemon::select('type')
            ->distinct()
            ->orderBy('type')
            ->pluck('type');
        
        return response()->json($types);
    }
}
