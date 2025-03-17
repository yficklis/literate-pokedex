<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PokemonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'api_id' => $this->api_id,
            'name' => $this->name,
            'type' => $this->type,
            'types' => $this->types ?? [],
            'height' => $this->height,
            'height_cm' => $this->height_cm,
            'weight' => $this->weight,
            'weight_kg' => $this->weight_kg,
            'abilities' => $this->abilities ?? [],
            'image_url' => $this->image_url,
        ];
    }
} 