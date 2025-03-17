<?php

namespace Tests\Feature\Api;

use App\Models\Pokemon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PokemonControllerTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function it_can_list_pokemons()
    {
        Pokemon::factory()->count(5)->create();
        
        $response = $this->getJson('/api/pokemons');
        
        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data');
    }
    
    /** @test */
    public function it_can_filter_pokemons_by_name()
    {
        Pokemon::factory()->create(['name' => 'bulbasaur']);
        Pokemon::factory()->create(['name' => 'ivysaur']);
        Pokemon::factory()->create(['name' => 'charmander']);
        
        $response = $this->getJson('/api/pokemons?name=saur');
        
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
    }
    
    /** @test */
    public function it_can_filter_pokemons_by_type()
    {
        Pokemon::factory()->create(['type' => 'grass']);
        Pokemon::factory()->create(['type' => 'fire']);
        Pokemon::factory()->create(['type' => 'water']);
        
        $response = $this->getJson('/api/pokemons?type=fire');
        
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
    }
    
    /** @test */
    public function it_can_show_pokemon_details()
    {
        $pokemon = Pokemon::factory()->create([
            'api_id' => 25,
            'name' => 'pikachu',
            'type' => 'electric',
            'height' => 4,
            'weight' => 60,
        ]);
        
        $response = $this->getJson('/api/pokemons/' . $pokemon->api_id);
        
        $response->assertStatus(200);
        $response->assertJson([
            'id' => $pokemon->api_id,
            'name' => 'pikachu',
            'type' => 'electric',
            'height' => 4,
            'height_cm' => 40,
            'weight' => 60,
            'weight_kg' => 6.0,
        ]);
    }
    
    /** @test */
    public function it_returns_404_for_nonexistent_pokemon()
    {
        $response = $this->getJson('/api/pokemons/9999');
        
        $response->assertStatus(404);
    }
}
