<?php

namespace App\Models;

use App\Services\PokemonApiService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Pokemon extends Model
{
    use HasFactory;

    protected $table = 'pokemon';
    
    protected $fillable = [
        'api_id',
        'name',
        'type',
        'height',
        'weight',
        'image_url',
    ];

    /**
     * Converte a altura de decímetros para centímetros
     */
    public function getHeightInCmAttribute(): int
    {
        return $this->height * 10;
    }

    /**
     * Converte o peso de hectogramas para quilogramas
     */
    public function getWeightInKgAttribute(): float
    {
        return $this->weight / 10;
    }

    /**
     * Busca Pokémons com filtros opcionais
     * Se não encontrar por nome no banco local, busca na API pública
     */
    public static function findWithFilters(?string $name = null, ?string $type = null)
    {
        $query = self::query();
        
        if ($name) {
            $query->where('name', 'LIKE', "%{$name}%");
        }
        
        if ($type) {
            $query->where('type', 'LIKE', "%{$type}%");
        }
        
        $result = $query->orderBy('api_id');
        
        // Se estiver buscando por nome e não encontrar resultados, tenta buscar na API pública
        if ($name && $result->count() === 0) {
            // Busca na API
            $apiService = app(PokemonApiService::class);
            $pokemon = $apiService->findPokemonByName($name);
            
            // Se encontrou o Pokémon na API, refaz a consulta para incluí-lo nos resultados
            if ($pokemon) {
                return self::findWithFilters($name, $type);
            }
        }
        
        return $result;
    }
}
