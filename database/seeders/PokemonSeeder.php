<?php

namespace Database\Seeders;

use App\Models\Pokemon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class PokemonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpa a tabela antes de popular
        Pokemon::truncate();

        // Busca os primeiros 151 Pokémon (1ª geração)
        for ($i = 1; $i <= 151; $i++) {
            $response = Http::get("https://pokeapi.co/api/v2/pokemon/{$i}");
            
            if ($response->successful()) {
                $data = $response->json();
                
                $types = [];
                foreach ($data['types'] as $type) {
                    $types[] = $type['type']['name'];
                }
                
                $abilities = [];
                foreach ($data['abilities'] as $ability) {
                    $abilities[] = $ability['ability']['name'];
                }
                
                Pokemon::create([
                    'name' => ucfirst($data['name']),
                    'height' => $data['height'],
                    'weight' => $data['weight'],
                    'types' => $types,
                    'abilities' => $abilities,
                    'image_url' => $data['sprites']['other']['official-artwork']['front_default'] ?? $data['sprites']['front_default'],
                    'api_id' => $data['id'],
                ]);
                
                $this->command->info("Pokémon {$data['name']} adicionado com sucesso!");
                
                // Pequena pausa para não sobrecarregar a API
                usleep(100000); // 100ms
            }
        }
    }
} 