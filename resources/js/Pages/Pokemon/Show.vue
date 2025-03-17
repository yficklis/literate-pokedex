<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
    pokemon: Object,
});

const isLoading = ref(true);

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

// Inicializa o estado de carregamento como falso quando o componente é montado
onMounted(() => {
    // Simula um pequeno atraso para mostrar o indicador de carregamento
    setTimeout(() => {
        isLoading.value = false;
    }, 300);
});

// Função para voltar à lista com indicador de carregamento
const backToList = () => {
    isLoading.value = true;
    router.get(route('pokemons.index'));
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
                <button @click="backToList" class="text-blue-500 hover:underline">
                    Voltar para a lista
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Indicador de carregamento -->
                        <div v-if="isLoading" class="flex justify-center items-center py-16">
                            <div class="animate-spin rounded-full h-16 w-16 border-t-2 border-b-2 border-blue-500"></div>
                            <span class="ml-3 text-lg text-gray-600">Carregando detalhes do Pokémon...</span>
                        </div>

                        <div v-else class="flex flex-col md:flex-row">
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
                                
                                <div class="mb-6 flex flex-wrap gap-2">
                                    <span 
                                        v-for="(type, index) in pokemon.types" 
                                        :key="index" 
                                        :class="[getTypeColor(type), 'px-4 py-2 rounded-full text-sm font-semibold']"
                                    >
                                        {{ capitalize(type) }}
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
                                
                                <div class="mt-8" v-if="pokemon.abilities && pokemon.abilities.length > 0">
                                    <h3 class="text-lg font-semibold mb-2">Habilidades</h3>
                                    <div class="flex flex-wrap gap-2">
                                        <span 
                                            v-for="(ability, index) in pokemon.abilities" 
                                            :key="index" 
                                            class="bg-gray-200 px-3 py-1 rounded-full text-sm"
                                        >
                                            {{ capitalize(ability.replace('-', ' ')) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template> 