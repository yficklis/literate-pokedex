<?php

namespace Tests\Feature\Api;

use App\Models\Pokemon;
use App\Services\PokemonApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class TypeControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_provide_autocomplete_suggestions_for_pokemon_types()
    {
        // Cria alguns Pokémon com diferentes tipos
        Pokemon::factory()->create(['types' => ['fire']]);
        Pokemon::factory()->create(['types' => ['water']]);
        Pokemon::factory()->create(['types' => ['electric']]);
        Pokemon::factory()->create(['types' => ['fire', 'flying']]);
        
        // Executa a busca com o termo "fi"
        $response = $this->getJson('/api/types-autocomplete?query=fi');
        
        // Verifica se encontrou os tipos que começam com "fi"
        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['fire']);
    }
    
    /** @test */
    public function it_searches_api_for_types_when_not_found_locally()
    {
        // Limpa a tabela de Pokémon para garantir que não há tipos
        DB::table('pokemon')->truncate();
        
        // Tipo único para garantir que não existe no banco
        $uniqueType = 'testtype_' . uniqid();
        
        // Mock do serviço de API
        $apiServiceMock = Mockery::mock(PokemonApiService::class);
        
        // Configuração do mock para retornar tipos quando chamado
        $apiServiceMock->shouldReceive('getAllTypes')
            ->once()
            ->andReturn([$uniqueType, 'normal', 'fire']);
        
        // Registra o mock no container
        $this->app->instance(PokemonApiService::class, $apiServiceMock);
        
        // Executa a busca
        $response = $this->getJson('/api/types-autocomplete?query=' . substr($uniqueType, 0, 4));
        
        // Verifica se encontrou o tipo
        $response->assertStatus(200);
        $response->assertJsonFragment([$uniqueType]);
    }
    
    /** @test */
    public function it_returns_empty_result_for_type_autocomplete_when_not_found()
    {
        // Limpa a tabela de Pokémon para garantir que não há tipos
        DB::table('pokemon')->truncate();
        
        // Mock do serviço de API
        $apiServiceMock = Mockery::mock(PokemonApiService::class);
        
        // Configuração do mock para retornar array vazio quando chamado
        $apiServiceMock->shouldReceive('getAllTypes')
            ->once()
            ->andReturn([]);
        
        // Registra o mock no container
        $this->app->instance(PokemonApiService::class, $apiServiceMock);
        
        // Executa a busca com um termo que não deve existir
        $response = $this->getJson('/api/types-autocomplete?query=nonexistenttype');
        
        // Verifica se não encontrou nenhum tipo
        $response->assertStatus(200);
        $response->assertJsonCount(0);
    }
} 