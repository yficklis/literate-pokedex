<?php

namespace Tests\Unit\Services;

use App\Models\Pokemon;
use App\Services\PokemonApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PokemonApiServiceTest extends TestCase
{
    use RefreshDatabase;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Limpa as fakes do Http antes de cada teste
        Http::fake();
    }
    
    /** @test */
    public function it_can_find_pokemon_by_name()
    {
        // Mock do cliente HTTP
        Http::fake([
            'https://pokeapi.co/api/v2/pokemon/pikachu' => Http::response([
                'id' => 25,
                'name' => 'pikachu',
                'height' => 4,
                'weight' => 60,
                'types' => [
                    ['type' => ['name' => 'electric']],
                ],
                'abilities' => [
                    ['ability' => ['name' => 'static']],
                    ['ability' => ['name' => 'lightning-rod']],
                ],
                'sprites' => [
                    'other' => [
                        'official-artwork' => [
                            'front_default' => 'https://example.com/pikachu.png'
                        ]
                    ],
                    'front_default' => 'https://example.com/pikachu-default.png'
                ]
            ], 200),
            '*' => Http::response([], 404)
        ]);
        
        $service = new PokemonApiService();
        $pokemon = $service->findPokemonByName('pikachu');
        
        $this->assertNotNull($pokemon);
        $this->assertEquals('pikachu', $pokemon->name);
        $this->assertEquals(['electric'], $pokemon->types);
        $this->assertEquals(4, $pokemon->height);
        $this->assertEquals(60, $pokemon->weight);
        $this->assertEquals(['static', 'lightning-rod'], $pokemon->abilities);
        $this->assertEquals('https://example.com/pikachu.png', $pokemon->image_url);
    }
    
    /** @test */
    public function it_returns_null_when_pokemon_not_found_by_name()
    {
        // Mock do cliente HTTP
        Http::fake([
            'https://pokeapi.co/api/v2/pokemon/nonexistentpokemon' => Http::response([], 404),
            '*' => Http::response([], 404)
        ]);
        
        $service = new PokemonApiService();
        $pokemon = $service->findPokemonByName('nonexistentpokemon');
        
        $this->assertNull($pokemon);
    }
    
    /** @test */
    public function it_can_find_pokemons_by_type()
    {
        // Mock do cliente HTTP
        Http::fake([
            'https://pokeapi.co/api/v2/type/fire' => Http::response([
                'pokemon' => [
                    [
                        'pokemon' => [
                            'name' => 'charmander',
                            'url' => 'https://pokeapi.co/api/v2/pokemon/4/'
                        ]
                    ],
                    [
                        'pokemon' => [
                            'name' => 'charmeleon',
                            'url' => 'https://pokeapi.co/api/v2/pokemon/5/'
                        ]
                    ]
                ]
            ], 200),
            'https://pokeapi.co/api/v2/pokemon/charmander' => Http::response([
                'id' => 4,
                'name' => 'charmander',
                'height' => 6,
                'weight' => 85,
                'types' => [
                    ['type' => ['name' => 'fire']],
                ],
                'abilities' => [
                    ['ability' => ['name' => 'blaze']],
                ],
                'sprites' => [
                    'other' => [
                        'official-artwork' => [
                            'front_default' => 'https://example.com/charmander.png'
                        ]
                    ],
                    'front_default' => 'https://example.com/charmander-default.png'
                ]
            ], 200),
            'https://pokeapi.co/api/v2/pokemon/charmeleon' => Http::response([
                'id' => 5,
                'name' => 'charmeleon',
                'height' => 11,
                'weight' => 190,
                'types' => [
                    ['type' => ['name' => 'fire']],
                ],
                'abilities' => [
                    ['ability' => ['name' => 'blaze']],
                ],
                'sprites' => [
                    'other' => [
                        'official-artwork' => [
                            'front_default' => 'https://example.com/charmeleon.png'
                        ]
                    ],
                    'front_default' => 'https://example.com/charmeleon-default.png'
                ]
            ], 200),
            '*' => Http::response([], 404)
        ]);
        
        $service = new PokemonApiService();
        $pokemons = $service->findPokemonsByType('fire');
        
        $this->assertCount(2, $pokemons);
        $this->assertEquals('charmander', $pokemons[0]->name);
        $this->assertEquals('charmeleon', $pokemons[1]->name);
        $this->assertEquals(['fire'], $pokemons[0]->types);
        $this->assertEquals(['fire'], $pokemons[1]->types);
    }
    
    /** @test */
    public function it_returns_empty_array_when_type_not_found()
    {
        // Configura o mock para a resposta da API
        Http::fake([
            'https://pokeapi.co/api/v2/type/nonexistenttype' => Http::response([], 404),
            '*' => Http::response([], 404)
        ]);
        
        $service = new PokemonApiService();
        $pokemons = $service->findPokemonsByType('nonexistenttype');
        
        $this->assertEmpty($pokemons);
    }
    
    /** @test */
    public function it_can_get_all_types()
    {
        // Mock do cliente HTTP
        Http::fake([
            'https://pokeapi.co/api/v2/type' => Http::response([
                'results' => [
                    ['name' => 'normal'],
                    ['name' => 'fire'],
                    ['name' => 'water'],
                    ['name' => 'grass'],
                    ['name' => 'electric'],
                    ['name' => 'ice'],
                    ['name' => 'fighting'],
                    ['name' => 'poison'],
                    ['name' => 'ground']
                ]
            ], 200),
            '*' => Http::response([], 404)
        ]);
        
        $service = new PokemonApiService();
        $types = $service->getAllTypes();
        
        $this->assertCount(5, $types);
        $this->assertEquals(['normal', 'fire', 'water', 'grass', 'electric'], $types);
    }
    
    /** @test */
    public function it_returns_empty_array_when_types_not_found()
    {
        // Configura o mock para a resposta da API
        Http::fake([
            'https://pokeapi.co/api/v2/type' => Http::response([], 404),
            '*' => Http::response([], 404)
        ]);
        
        $service = new PokemonApiService();
        $types = $service->getAllTypes();
        
        $this->assertEmpty($types);
    }
} 