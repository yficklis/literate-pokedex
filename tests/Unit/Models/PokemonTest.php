<?php

namespace Tests\Unit\Models;

use App\Models\Pokemon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PokemonTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function it_converts_height_to_centimeters()
    {
        $pokemon = Pokemon::factory()->create([
            'height' => 7, // 7 decímetros
        ]);
        
        $this->assertEquals(70, $pokemon->height_in_cm); // 7 decímetros = 70 centímetros
    }
    
    /** @test */
    public function it_converts_weight_to_kilograms()
    {
        $pokemon = Pokemon::factory()->create([
            'weight' => 69, // 69 hectogramas
        ]);
        
        $this->assertEquals(6.9, $pokemon->weight_in_kg); // 69 hectogramas = 6.9 quilogramas
    }
    
    /** @test */
    public function it_can_filter_by_name()
    {
        Pokemon::factory()->create(['name' => 'bulbasaur']);
        Pokemon::factory()->create(['name' => 'ivysaur']);
        Pokemon::factory()->create(['name' => 'venusaur']);
        
        $results = Pokemon::findWithFilters('saur')->get();
        
        $this->assertCount(3, $results);
    }
    
    /** @test */
    public function it_can_filter_by_type()
    {
        Pokemon::factory()->create(['type' => 'grass']);
        Pokemon::factory()->create(['type' => 'fire']);
        Pokemon::factory()->create(['type' => 'water']);
        
        $results = Pokemon::findWithFilters(null, 'fire')->get();
        
        $this->assertCount(1, $results);
        $this->assertEquals('fire', $results->first()->type);
    }
    
    /** @test */
    public function it_can_filter_by_name_and_type()
    {
        Pokemon::factory()->create(['name' => 'bulbasaur', 'type' => 'grass']);
        Pokemon::factory()->create(['name' => 'charmander', 'type' => 'fire']);
        Pokemon::factory()->create(['name' => 'squirtle', 'type' => 'water']);
        
        $results = Pokemon::findWithFilters('char', 'fire')->get();
        
        $this->assertCount(1, $results);
        $this->assertEquals('charmander', $results->first()->name);
        $this->assertEquals('fire', $results->first()->type);
    }
}
