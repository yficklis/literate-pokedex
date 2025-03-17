<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch, computed, onMounted } from 'vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import PokemonCard from '@/Components/Pokemon/PokemonCard.vue';
import Pagination from '@/Components/Pagination.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    pokemons: Object,
    filters: Object,
});

const search = ref({
    name: props.filters.name || '',
    type: props.filters.type || '',
});

const debouncedSearch = ref(null);
const isLoading = ref(false);

watch(search, (value) => {
    clearTimeout(debouncedSearch.value);
    
    debouncedSearch.value = setTimeout(() => {
        isLoading.value = true;
        router.get(route('pokemons.index'), {
            name: value.name || null,
            type: value.type || null,
        }, {
            preserveState: true,
            replace: true,
            onSuccess: () => {
                isLoading.value = false;
            },
            onError: () => {
                isLoading.value = false;
            }
        });
    }, 300);
}, { deep: true });

const clearFilters = () => {
    search.value.name = '';
    search.value.type = '';
    
    isLoading.value = true;
    router.get(route('pokemons.index'), {}, {
        preserveState: true,
        replace: true,
        onSuccess: () => {
            isLoading.value = false;
        },
        onError: () => {
            isLoading.value = false;
        }
    });
};

// Computed property para garantir que os dados dos Pokémon estejam no formato correto
const pokemonData = computed(() => {
    if (!props.pokemons || !props.pokemons.data) {
        return [];
    }
    
    // Se os dados já estiverem no formato correto, retorna-os diretamente
    if (Array.isArray(props.pokemons.data)) {
        return props.pokemons.data;
    }
    
    // Se os dados estiverem em um formato aninhado, tenta extraí-los
    if (typeof props.pokemons.data === 'object') {
        return Object.values(props.pokemons.data);
    }
    
    return [];
});

// Computed property para verificar se há links de paginação
const hasLinks = computed(() => {
    return props.pokemons && 
           props.pokemons.links && 
           Array.isArray(props.pokemons.links) && 
           props.pokemons.links.length > 0;
});

// Inicializa o estado de carregamento como falso quando o componente é montado
onMounted(() => {
    isLoading.value = false;
});
</script>

<template>
    <Head title="Pokémons" />

    <MainLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pokémons</h2>
                <Link :href="route('pokemon-types.index')" class="text-blue-500 hover:underline">
                    Ver todos os tipos
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <!-- Filtros -->
                        <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <InputLabel for="name" value="Nome" />
                                <TextInput
                                    id="name"
                                    v-model="search.name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="Buscar por nome"
                                    :disabled="isLoading"
                                />
                            </div>
                            <div>
                                <InputLabel for="type" value="Tipo" />
                                <div class="flex space-x-2">
                                    <TextInput
                                        id="type"
                                        v-model="search.type"
                                        type="text"
                                        class="mt-1 block w-full"
                                        placeholder="Buscar por tipo"
                                        :disabled="isLoading"
                                    />
                                    <Link 
                                        :href="route('pokemon-types.index')" 
                                        class="mt-1 inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                                        :class="{ 'opacity-50 cursor-not-allowed': isLoading }"
                                        :disabled="isLoading"
                                    >
                                        Tipos
                                    </Link>
                                </div>
                            </div>
                            <div class="flex items-end space-x-2">
                                <PrimaryButton @click="clearFilters" class="mt-1" :disabled="isLoading">
                                    Limpar Filtros
                                </PrimaryButton>
                            </div>
                        </div>

                        <!-- Indicador de carregamento -->
                        <div v-if="isLoading" class="flex justify-center items-center py-8">
                            <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
                            <span class="ml-3 text-gray-600">Carregando...</span>
                        </div>

                        <!-- Listagem de Pokémons -->
                        <div v-else-if="pokemonData.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            <PokemonCard
                                v-for="pokemon in pokemonData"
                                :key="pokemon.id"
                                :pokemon="pokemon"
                            />
                        </div>
                        <div v-else class="text-center py-8">
                            <p class="text-gray-500">Nenhum Pokémon encontrado.</p>
                        </div>

                        <!-- Paginação -->
                        <div v-if="!isLoading && pokemonData.length > 0" class="mt-6">
                            <div v-if="hasLinks" class="flex justify-center">
                                <Pagination :links="pokemons.links" />
                            </div>
                            <div v-else class="text-center text-gray-500">
                                Mostrando {{ pokemons.from }} a {{ pokemons.to }} de {{ pokemons.total }} resultados
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template> 