<?php

namespace Tests\Unit\Models;

use App\Models\Pokemon;
use App\Services\PokemonApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

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
        Pokemon::factory()->create(['types' => ['grass']]);
        Pokemon::factory()->create(['types' => ['fire']]);
        Pokemon::factory()->create(['types' => ['water']]);
        
        $results = Pokemon::findWithFilters(null, 'fire')->get();
        
        $this->assertCount(1, $results);
        $this->assertEquals('fire', $results->first()->type);
    }
    
    /** @test */
    public function it_can_filter_by_name_and_type()
    {
        Pokemon::factory()->create(['name' => 'bulbasaur', 'types' => ['grass']]);
        Pokemon::factory()->create(['name' => 'charmander', 'types' => ['fire']]);
        Pokemon::factory()->create(['name' => 'squirtle', 'types' => ['water']]);
        
        $results = Pokemon::findWithFilters('char', 'fire')->get();
        
        $this->assertCount(1, $results);
        $this->assertEquals('charmander', $results->first()->name);
        $this->assertEquals('fire', $results->first()->type);
    }
    
    /** @test */
    public function it_searches_api_when_pokemon_not_found_locally()
    {
        // Limpa a tabela de Pokémon para garantir que não há Pokémon com esse nome
        DB::table('pokemon')->truncate();
        
        // Nome único para garantir que não existe no banco
        $uniquePokemonName = 'testpokemon_' . uniqid();
        
        // Dados do Pokémon para criar quando o mock for chamado
        $pokemonData = [
            'api_id' => 999,
            'name' => $uniquePokemonName,
            'types' => ['test'],
            'height' => 10,
            'weight' => 10,
            'abilities' => ['test-ability']
        ];
        
        // Mock do serviço de API
        $apiServiceMock = Mockery::mock(PokemonApiService::class);
        
        // Configuração do mock para retornar um Pokémon quando chamado
        $apiServiceMock->shouldReceive('findPokemonByName')
            ->once()
            ->with($uniquePokemonName)
            ->andReturnUsing(function() use ($pokemonData) {
                // Cria o Pokémon apenas quando o método for chamado
                return Pokemon::factory()->create($pokemonData);
            });
        
        // Registra o mock no container
        $this->app->instance(PokemonApiService::class, $apiServiceMock);
        
        // Executa a busca
        $results = Pokemon::findWithFiltersAndApi($uniquePokemonName)->get();
        
        // Verifica se encontrou o Pokémon
        $this->assertCount(1, $results);
        $this->assertEquals($uniquePokemonName, $results->first()->name);
    }
    
    /** @test */
    public function it_returns_empty_result_when_pokemon_not_found_in_api()
    {
        // Mock do serviço de API
        $apiServiceMock = Mockery::mock(PokemonApiService::class);
        
        // Configuração do mock para retornar null quando chamado
        $apiServiceMock->shouldReceive('findPokemonByName')
            ->once()
            ->with('nonexistentpokemon')
            ->andReturn(null);
        
        // Registra o mock no container
        $this->app->instance(PokemonApiService::class, $apiServiceMock);
        
        // Executa a busca
        $results = Pokemon::findWithFiltersAndApi('nonexistentpokemon')->get();
        
        // Verifica se não encontrou nenhum Pokémon
        $this->assertCount(0, $results);
    }
}
