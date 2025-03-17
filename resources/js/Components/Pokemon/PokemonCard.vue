<script setup>
import { Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    pokemon: {
        type: Object,
        required: true,
    },
});

const isLoading = ref(false);

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

// Computed property para garantir que temos um ID válido
const pokemonId = computed(() => {
    // Verifica se pokemon.api_id existe
    if (props.pokemon && props.pokemon.api_id) {
        return props.pokemon.api_id;
    }
    // Caso contrário, tenta usar o ID regular
    if (props.pokemon && props.pokemon.id) {
        return props.pokemon.id;
    }
    // Se nenhum ID estiver disponível, retorna null
    return null;
});

// Computed property para verificar se temos um ID válido para criar o link
const hasValidId = computed(() => {
    return pokemonId.value !== null;
});

// Função para navegar para a página de detalhes com indicador de carregamento
const navigateToDetails = () => {
    if (hasValidId.value) {
        isLoading.value = true;
        router.get(route('pokemons.show', pokemonId.value));
    }
};
</script>

<template>
    <div v-if="!hasValidId" class="block">
        <div class="bg-white border rounded-lg shadow-md overflow-hidden">
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
                
                <div class="mt-2 flex flex-wrap gap-1 justify-center">
                    <span 
                        v-for="(type, index) in pokemon.types" 
                        :key="index" 
                        :class="[getTypeColor(type), 'px-3 py-1 rounded-full text-sm']"
                    >
                        {{ capitalize(type) }}
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
    </div>
    <div v-else @click="navigateToDetails" class="block cursor-pointer">
        <div class="bg-white border rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden relative">
            <!-- Indicador de carregamento -->
            <div v-if="isLoading" class="absolute inset-0 bg-white bg-opacity-80 flex justify-center items-center z-10">
                <div class="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-blue-500"></div>
            </div>
            
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
                
                <div class="mt-2 flex flex-wrap gap-1 justify-center">
                    <span 
                        v-for="(type, index) in pokemon.types" 
                        :key="index" 
                        :class="[getTypeColor(type), 'px-3 py-1 rounded-full text-sm']"
                    >
                        {{ capitalize(type) }}
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
    </div>
</template> 