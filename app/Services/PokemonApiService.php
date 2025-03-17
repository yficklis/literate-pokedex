<?php

namespace App\Services;

use App\Models\Pokemon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PokemonApiService
{
    private const API_URL = 'https://pokeapi.co/api/v2/pokemon';
    private const TYPE_API_URL = 'https://pokeapi.co/api/v2/type';
    
    /**
     * URL base da API de Pokémon
     */
    private string $baseUrl = 'https://pokeapi.co/api/v2';
    
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
     * Busca um Pokémon pelo nome na API
     *
     * @param string $name Nome do Pokémon
     * @return Pokemon|null
     */
    public function findPokemonByName(string $name): ?Pokemon
    {
        try {
            // Formata o nome para minúsculas e remove espaços
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
            
            // Se não houver dados, retorna null
            if (!$pokemonData || !isset($pokemonData['id'])) {
                Log::info("Dados do Pokémon '{$name}' não encontrados na API pública.");
                return null;
            }
            
            // Armazena o Pokémon no banco de dados e retorna
            return $this->storePokemon($pokemonData);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar Pokémon por nome: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Busca Pokémons por tipo na API pública
     * 
     * @param string $type Tipo do Pokémon
     * @return array Lista de Pokémons encontrados
     */
    public function findPokemonsByType(string $type): array
    {
        try {
            $response = Http::get(self::TYPE_API_URL . '/' . strtolower(trim($type)));
            
            if (!$response->successful()) {
                Log::info("Tipo de Pokémon não encontrado na API: {$type}");
                return [];
            }
            
            $data = $response->json();
            
            if (!$data || !isset($data['pokemon'])) {
                Log::info("Dados do tipo {$type} não encontrados na API");
                return [];
            }
            
            $pokemons = [];
            
            // Limita a 10 Pokémon para não sobrecarregar
            $pokemonList = array_slice($data['pokemon'], 0, 10);
            
            foreach ($pokemonList as $item) {
                if (!isset($item['pokemon']['name'])) {
                    continue;
                }
                
                $pokemonName = $item['pokemon']['name'];
                $response = Http::get(self::API_URL . '/' . $pokemonName);
                
                if (!$response->successful()) {
                    continue;
                }
                
                $pokemonData = $response->json();
                
                if (!$pokemonData || !isset($pokemonData['id'])) {
                    continue;
                }
                
                $pokemon = $this->storePokemon($pokemonData);
                if ($pokemon) {
                    $pokemons[] = $pokemon;
                }
            }
            
            return $pokemons;
        } catch (\Exception $e) {
            Log::error("Erro ao buscar Pokémon por tipo na API: {$e->getMessage()}");
            return [];
        }
    }
    
    /**
     * Busca todos os tipos de Pokémon disponíveis na API
     * 
     * @return array Lista de tipos de Pokémon
     */
    public function getAllTypes(): array
    {
        try {
            $response = Http::get(self::TYPE_API_URL);
            
            if (!$response->successful()) {
                Log::info("Não foi possível obter os tipos de Pokémon da API");
                return [];
            }
            
            $data = $response->json();
            
            if (!$data || !isset($data['results'])) {
                Log::info("Dados dos tipos não encontrados na API");
                return [];
            }
            
            // Filtra apenas os tipos principais
            $mainTypes = ['normal', 'fire', 'water', 'grass', 'electric'];
            $types = array_map(function ($type) {
                return $type['name'];
            }, $data['results']);
            
            return array_values(array_intersect($mainTypes, $types));
        } catch (\Exception $e) {
            Log::error("Erro ao obter tipos de Pokémon da API: {$e->getMessage()}");
            return [];
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
            if (isset($typeData['type']['name'])) {
                $types[] = $typeData['type']['name'];
            }
        }
        
        // Extrai as habilidades do Pokémon
        $abilities = [];
        foreach ($data['abilities'] as $abilityData) {
            if (isset($abilityData['ability']['name'])) {
                $abilities[] = $abilityData['ability']['name'];
            }
        }
        
        // Obtém a URL da imagem
        $imageUrl = $data['sprites']['other']['official-artwork']['front_default'] 
            ?? $data['sprites']['front_default']
            ?? null;
        
        // Cria ou atualiza o Pokémon
        return Pokemon::updateOrCreate(
            ['api_id' => $data['id']],
            [
                'name' => strtolower($data['name']),
                'types' => $types,
                'height' => $data['height'],
                'weight' => $data['weight'],
                'abilities' => $abilities,
                'image_url' => $imageUrl,
            ]
        );
    }
} 