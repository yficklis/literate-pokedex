<?php

namespace App\Http\Controllers;

use App\Models\Pokemon;
use App\Http\Resources\PokemonResource;
use App\Services\PokemonApiService;
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
        
        // Busca os Pokémon com os filtros aplicados e tenta buscar na API se necessário
        $query = Pokemon::findWithFiltersAndApi($name, $type);
        
        // Pagina os resultados
        $pokemons = $query->paginate(12)->withQueryString();
        
        // Transformar os dados usando o resource
        $pokemonsData = [];
        foreach ($pokemons->items() as $pokemon) {
            $pokemonsData[] = (new PokemonResource($pokemon))->toArray($request);
        }
        
        // Obtém os links de paginação
        $links = $pokemons->linkCollection()->toArray();
        
        return Inertia::render('Pokemon/Index', [
            'pokemons' => [
                'data' => $pokemonsData,
                'links' => $links,
                'current_page' => $pokemons->currentPage(),
                'last_page' => $pokemons->lastPage(),
                'from' => $pokemons->firstItem(),
                'to' => $pokemons->lastItem(),
                'total' => $pokemons->total(),
            ],
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
     * @param Request $request
     * @return Response
     */
    public function show(int $id, Request $request): Response
    {
        // Tenta encontrar o Pokémon no banco de dados
        $pokemon = Pokemon::where('api_id', $id)->orWhere('id', $id)->first();
        
        // Se não encontrar, tenta buscar na API
        if (!$pokemon) {
            $apiService = app(PokemonApiService::class);
            $pokemon = $apiService->findPokemonByName((string) $id);
            
            // Se ainda não encontrar, retorna 404
            if (!$pokemon) {
                abort(404, 'Pokémon não encontrado');
            }
        }
        
        return Inertia::render('Pokemon/Show', [
            'pokemon' => (new PokemonResource($pokemon))->toArray($request),
        ]);
    }
}
