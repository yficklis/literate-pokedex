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
     * Busca um Pokémon por nome na API pública
     * 
     * @param string $name Nome do Pokémon
     * @return Pokemon|null Pokémon encontrado ou null se não encontrado
     */
    public function findPokemonByName(string $name): ?Pokemon
    {
        try {
            // Converte o nome para minúsculas e remove espaços
            $formattedName = strtolower(trim($name));
            
            // Busca o Pokémon na API
            $response = Http::get(self::API_URL . '/' . $formattedName);
            
            // Se a resposta não for bem-sucedida, retorna null
            if (!$response->successful()) {
                Log::info("Pokémon '{$name}' não encontrado na API pública.");
                return null;
            }
            
            // Obtém os dados do Pokémon
            $pokemonData = $response->json();
            
            // Armazena o Pokémon no banco de dados e retorna
            return $this->storePokemon($pokemonData);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar Pokémon por nome: ' . $e->getMessage());
            return null;
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
        // Extrai os tipos do Pokémon
        $types = [];
        foreach ($data['types'] as $typeData) {
            $types[] = $typeData['type']['name'];
        }
        
        // Extrai as habilidades do Pokémon
        $abilities = [];
        foreach ($data['abilities'] as $abilityData) {
            $abilities[] = $abilityData['ability']['name'];
        }
        
        return Pokemon::updateOrCreate(
            ['api_id' => $data['id']],
            [
                'name' => ucfirst($data['name']),
                'types' => $types,
                'height' => $data['height'],
                'weight' => $data['weight'],
                'abilities' => $abilities,
                'image_url' => $data['sprites']['other']['official-artwork']['front_default'] ?? $data['sprites']['front_default'],
            ]
        );
    }
} 