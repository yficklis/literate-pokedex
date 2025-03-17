<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    pokemon: {
        type: Object,
        required: true,
    },
});

// Função para capitalizar a primeira letra
const capitalize = (str) => {
    return str.charAt(0).toUpperCase() + str.slice(1);
};

// Função para obter a cor de fundo baseada no tipo do Pokémon
const getTypeColor = (type) => {
    const typeColors = {
        normal: 'bg-gray-300',
        fire: 'bg-red-400',
        water: 'bg-blue-400',
        grass: 'bg-green-400',
        electric: 'bg-yellow-300',
        ice: 'bg-blue-200',
        fighting: 'bg-red-600',
        poison: 'bg-purple-500',
        ground: 'bg-yellow-600',
        flying: 'bg-indigo-300',
        psychic: 'bg-pink-400',
        bug: 'bg-green-500',
        rock: 'bg-yellow-700',
        ghost: 'bg-purple-700',
        dragon: 'bg-indigo-600',
        dark: 'bg-gray-700 text-white',
        steel: 'bg-gray-400',
        fairy: 'bg-pink-300',
    };
    
    return typeColors[type.toLowerCase()] || 'bg-gray-300';
};
</script>

<template>
    <Link :href="route('pokemons.show', pokemon.api_id)" class="block">
        <div class="bg-white border rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden">
            <div class="p-4 flex flex-col items-center">
                <img 
                    :src="pokemon.image_url" 
                    :alt="pokemon.name" 
                    class="w-32 h-32 object-contain mb-2"
                    onerror="this.src='https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/0.png'"
                />
                
                <h3 class="text-lg font-semibold text-center">
                    {{ capitalize(pokemon.name) }}
                </h3>
                
                <div class="mt-2">
                    <span :class="[getTypeColor(pokemon.type), 'px-3 py-1 rounded-full text-sm']">
                        {{ capitalize(pokemon.type) }}
                    </span>
                </div>
                
                <div class="mt-3 text-sm text-gray-600 w-full">
                    <div class="flex justify-between">
                        <span>Altura:</span>
                        <span>{{ pokemon.height_cm }} cm</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Peso:</span>
                        <span>{{ pokemon.weight_kg }} kg</span>
                    </div>
                </div>
            </div>
        </div>
    </Link>
</template> 