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
        
        return [
            'api_id' => $this->faker->unique()->numberBetween(1, 1000),
            'name' => $this->faker->word(),
            'type' => $this->faker->randomElement($types),
            'height' => $this->faker->numberBetween(1, 100), // em decímetros
            'weight' => $this->faker->numberBetween(1, 1000), // em hectogramas
            'image_url' => $this->faker->imageUrl(96, 96, 'pokemon'),
        ];
    }
}
