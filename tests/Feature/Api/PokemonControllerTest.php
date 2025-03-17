<?php

namespace Tests\Feature\Api;

use App\Models\Pokemon;
use App\Services\PokemonApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

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
        Pokemon::factory()->create(['types' => ['grass']]);
        Pokemon::factory()->create(['types' => ['fire']]);
        Pokemon::factory()->create(['types' => ['water']]);
        
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
            'types' => ['electric'],
            'height' => 4,
            'weight' => 60,
            'abilities' => ['static']
        ]);
        
        $response = $this->getJson('/api/pokemons/' . $pokemon->api_id);
        
        $response->assertStatus(200);
        $response->assertJson([
            'id' => $pokemon->id,
            'api_id' => $pokemon->api_id,
            'name' => 'pikachu',
            'types' => ['electric'],
            'height' => 4,
            'height_cm' => 40,
            'weight' => 60,
            'weight_kg' => 6.0,
            'abilities' => ['static']
        ]);
    }
    
    /** @test */
    public function it_returns_404_for_nonexistent_pokemon()
    {
        $response = $this->getJson('/api/pokemons/9999');
        
        $response->assertStatus(404);
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
        $response = $this->getJson('/api/pokemons?name=' . $uniquePokemonName);
        
        // Verifica se encontrou o Pokémon
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.name', $uniquePokemonName);
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
        $response = $this->getJson('/api/pokemons?name=nonexistentpokemon');
        
        // Verifica se não encontrou nenhum Pokémon
        $response->assertStatus(200);
        $response->assertJsonCount(0, 'data');
    }
    
    /** @test */
    public function it_can_provide_autocomplete_suggestions_for_pokemon_names()
    {
        // Cria alguns Pokémon para o teste
        Pokemon::factory()->create(['name' => 'Pikachu']);
        Pokemon::factory()->create(['name' => 'Pidgey']);
        Pokemon::factory()->create(['name' => 'Pidgeotto']);
        Pokemon::factory()->create(['name' => 'Charizard']);
        
        // Executa a busca com o termo "pi"
        $response = $this->getJson('/api/pokemons-autocomplete?query=pi');
        
        // Verifica se encontrou os Pokémon que começam com "pi"
        $response->assertStatus(200);
        $response->assertJsonCount(3);
        $response->assertJsonFragment(['Pikachu']);
        $response->assertJsonFragment(['Pidgey']);
        $response->assertJsonFragment(['Pidgeotto']);
    }
    
    /** @test */
    public function it_searches_api_for_autocomplete_when_pokemon_not_found_locally()
    {
        // Limpa a tabela de Pokémon para garantir que não há Pokémon com esse nome
        DB::table('pokemon')->truncate();
        
        // Nome único para garantir que não existe no banco
        $uniquePokemonName = 'testpokemon_' . uniqid();
        
        // Mock do serviço de API
        $apiServiceMock = Mockery::mock(PokemonApiService::class);
        
        // Configuração do mock para retornar um Pokémon quando chamado
        $apiServiceMock->shouldReceive('findPokemonByName')
            ->once()
            ->with($uniquePokemonName)
            ->andReturnUsing(function() use ($uniquePokemonName) {
                // Cria o Pokémon apenas quando o método for chamado
                return Pokemon::factory()->create([
                    'name' => $uniquePokemonName,
                    'types' => ['test'],
                    'height' => 10,
                    'weight' => 10,
                    'abilities' => ['test-ability']
                ]);
            });
        
        // Registra o mock no container
        $this->app->instance(PokemonApiService::class, $apiServiceMock);
        
        // Executa a busca
        $response = $this->getJson('/api/pokemons-autocomplete?query=' . $uniquePokemonName);
        
        // Verifica se encontrou o Pokémon
        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment([$uniquePokemonName]);
    }
    
    /** @test */
    public function it_returns_empty_result_for_autocomplete_when_pokemon_not_found_in_api()
    {
        // Limpa a tabela de Pokémon para garantir que não há Pokémon com esse nome
        DB::table('pokemon')->truncate();
        
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
        $response = $this->getJson('/api/pokemons-autocomplete?query=nonexistentpokemon');
        
        // Verifica se não encontrou nenhum Pokémon
        $response->assertStatus(200);
        $response->assertJsonCount(0);
    }
}
