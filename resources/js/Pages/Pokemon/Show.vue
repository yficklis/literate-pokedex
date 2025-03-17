<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
    pokemon: Object,
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
    <Head :title="capitalize(pokemon.name)" />

    <MainLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ capitalize(pokemon.name) }}
                </h2>
                <Link :href="route('pokemons.index')" class="text-blue-500 hover:underline">
                    Voltar para a lista
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row">
                            <!-- Imagem do Pokémon -->
                            <div class="md:w-1/3 flex justify-center">
                                <img 
                                    :src="pokemon.image_url" 
                                    :alt="pokemon.name" 
                                    class="w-64 h-64 object-contain"
                                    onerror="this.src='https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/0.png'"
                                />
                            </div>
                            
                            <!-- Detalhes do Pokémon -->
                            <div class="md:w-2/3 mt-6 md:mt-0 md:pl-8">
                                <h1 class="text-3xl font-bold mb-4">{{ capitalize(pokemon.name) }}</h1>
                                
                                <div class="mb-6">
                                    <span :class="[getTypeColor(pokemon.type), 'px-4 py-2 rounded-full text-sm font-semibold']">
                                        {{ capitalize(pokemon.type) }}
                                    </span>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="bg-gray-100 p-4 rounded-lg">
                                        <h3 class="text-lg font-semibold mb-2">Altura</h3>
                                        <p class="text-2xl">{{ pokemon.height_cm }} cm</p>
                                        <p class="text-sm text-gray-500">{{ pokemon.height / 10 }} m</p>
                                    </div>
                                    
                                    <div class="bg-gray-100 p-4 rounded-lg">
                                        <h3 class="text-lg font-semibold mb-2">Peso</h3>
                                        <p class="text-2xl">{{ pokemon.weight_kg }} kg</p>
                                        <p class="text-sm text-gray-500">{{ pokemon.weight }} hg</p>
                                    </div>
                                </div>
                                
                                <div class="mt-8">
                                    <h3 class="text-lg font-semibold mb-2">ID na Pokédex</h3>
                                    <p class="text-xl">#{{ pokemon.api_id }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template> 