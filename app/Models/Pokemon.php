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
        
        return $query->orderBy('api_id');
    }
}
