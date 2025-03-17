<?php

namespace Database\Factories;

use App\Models\Pokemon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pokemon>
 */
class PokemonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['normal', 'fire', 'water', 'grass', 'electric', 'ice', 'fighting', 'poison', 'ground', 'flying', 'psychic', 'bug', 'rock', 'ghost', 'dragon', 'dark', 'steel', 'fairy'];
        $abilities = ['overgrow', 'blaze', 'torrent', 'shield-dust', 'shed-skin', 'compound-eyes', 'swarm', 'keen-eye', 'run-away', 'intimidate', 'static', 'sand-veil', 'lightning-rod'];
        
        // Seleciona de 1 a 2 tipos aleatórios
        $selectedTypes = $this->faker->randomElements($types, $this->faker->numberBetween(1, 2));
        
        // Seleciona de 1 a 3 habilidades aleatórias
        $selectedAbilities = $this->faker->randomElements($abilities, $this->faker->numberBetween(1, 3));
        
        return [
            'api_id' => $this->faker->unique()->numberBetween(1, 1000),
            'name' => $this->faker->word(),
            'types' => $selectedTypes,
            'height' => $this->faker->numberBetween(1, 100), // em decímetros
            'weight' => $this->faker->numberBetween(1, 1000), // em hectogramas
            'abilities' => $selectedAbilities,
            'image_url' => $this->faker->imageUrl(96, 96, 'pokemon'),
        ];
    }
}
