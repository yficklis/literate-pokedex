<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
    types: Array,
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
    <Head title="Tipos de Pokémon" />

    <MainLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Tipos de Pokémon
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
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                            <div v-for="type in types" :key="type" class="text-center">
                                <Link 
                                    :href="route('pokemons.index', { type: type })" 
                                    class="block p-4 rounded-lg transition-transform transform hover:scale-105"
                                    :class="[getTypeColor(type)]"
                                >
                                    <span class="font-semibold text-lg">{{ capitalize(type) }}</span>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template> 