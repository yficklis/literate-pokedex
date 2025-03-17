<?php

namespace App\Models;

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
        'types',
        'height',
        'weight',
        'abilities',
        'image_url',
    ];
    
    protected $casts = [
        'types' => 'array',
        'abilities' => 'array',
    ];

    /**
     * Converte a altura de decímetros para centímetros
     */
    public function getHeightCmAttribute(): int
    {
        return $this->height * 10;
    }

    /**
     * Converte o peso de hectogramas para quilogramas
     */
    public function getWeightKgAttribute(): float
    {
        return $this->weight / 10;
    }

    /**
     * Mantém compatibilidade com o nome anterior do atributo
     */
    public function getHeightInCmAttribute(): int
    {
        return $this->height_cm;
    }

    /**
     * Mantém compatibilidade com o nome anterior do atributo
     */
    public function getWeightInKgAttribute(): float
    {
        return $this->weight_kg;
    }
    
    /**
     * Obtém o tipo principal do Pokémon (primeiro da lista)
     */
    public function getTypeAttribute(): ?string
    {
        return $this->types[0] ?? null;
    }

    /**
     * Busca Pokémons com filtros opcionais
     */
    public static function findWithFilters(?string $name = null, ?string $type = null)
    {
        $query = self::query();
        
        if ($name) {
            $query->where('name', 'LIKE', "%{$name}%");
        }
        
        if ($type) {
            // Busca por tipo usando JSON_CONTAINS para campos JSON
            $query->whereJsonContains('types', $type);
        }
        
        return $query->orderBy('api_id');
    }
    
    /**
     * Busca Pokémons com filtros e tenta buscar na API se não encontrar localmente
     */
    public static function findWithFiltersAndApi(?string $name = null, ?string $type = null)
    {
        $query = self::findWithFilters($name, $type);
        
        // Se estiver buscando por nome e não encontrar resultados, tenta buscar na API pública
        if ($name && $query->count() === 0) {
            // Busca na API
            $apiService = app(\App\Services\PokemonApiService::class);
            $pokemon = $apiService->findPokemonByName($name);
            
            // Se encontrou o Pokémon na API, refaz a consulta para incluí-lo nos resultados
            if ($pokemon) {
                $query = self::findWithFilters($name, $type);
            }
        }
        
        return $query;
    }
}
