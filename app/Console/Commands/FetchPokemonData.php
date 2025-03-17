<?php

namespace App\Console\Commands;

use App\Services\PokemonApiService;
use Illuminate\Console\Command;

class FetchPokemonData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pokemon:fetch {--limit=100 : Número de Pokémons a serem buscados} {--offset=0 : Offset para paginação}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Busca dados de Pokémons da API pública e armazena no banco de dados';

    /**
     * Execute the console command.
     */
    public function handle(PokemonApiService $pokemonApiService)
    {
        $limit = $this->option('limit');
        $offset = $this->option('offset');
        
        $this->info("Buscando {$limit} Pokémons a partir do offset {$offset}...");
        
        $result = $pokemonApiService->fetchAndStorePokemon($limit, $offset);
        
        if ($result['success']) {
            $this->info($result['message']);
        } else {
            $this->error($result['message']);
        }
        
        return $result['success'] ? Command::SUCCESS : Command::FAILURE;
    }
}
