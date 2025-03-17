<?php

namespace App\Services;

use App\Models\Pokemon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PokemonApiService
{
    private const API_URL = 'https://pokeapi.co/api/v2/pokemon';
    
    /**
     * Busca e armazena os dados de Pokémons da API
     * 
     * @param int $limit Número de Pokémons a serem buscados
     * @param int $offset Offset para paginação
     * @return array Informações sobre a operação
     */
    public function fetchAndStorePokemon(int $limit = 100, int $offset = 0): array
    {
        try {
            $response = Http::get(self::API_URL, [
                'limit' => $limit,
                'offset' => $offset,
            ]);
            
            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => 'Falha ao buscar dados da API: ' . $response->status(),
                ];
            }
            
            $data = $response->json();
            $results = $data['results'] ?? [];
            
            $count = 0;
            foreach ($results as $result) {
                $pokemonData = Http::get($result['url'])->json();
                
                if (!$pokemonData) {
                    continue;
                }
                
                $this->storePokemon($pokemonData);
                $count++;
            }
            
            return [
                'success' => true,
                'message' => "Importados {$count} Pokémons com sucesso.",
                'count' => $count,
            ];
        } catch (\Exception $e) {
            Log::error('Erro ao buscar Pokémons: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Erro ao buscar Pokémons: ' . $e->getMessage(),
            ];
        }
    }
    
    /**
     * Armazena os dados de um Pokémon no banco de dados
     * 
     * @param array $data Dados do Pokémon
     * @return Pokemon
     */
    private function storePokemon(array $data): Pokemon
    {
        // Pega apenas o primeiro tipo, conforme especificado nos requisitos
        $type = isset($data['types'][0]['type']['name']) 
            ? $data['types'][0]['type']['name'] 
            : 'unknown';
        
        return Pokemon::updateOrCreate(
            ['api_id' => $data['id']],
            [
                'name' => $data['name'],
                'type' => $type,
                'height' => $data['height'],
                'weight' => $data['weight'],
                'image_url' => $data['sprites']['front_default'] ?? null,
            ]
        );
    }
} 