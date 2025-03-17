<script setup>
import { ref, watch, onMounted, onUnmounted, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { debounce } from 'lodash';

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    label: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: '',
    },
    endpoint: {
        type: String,
        required: true,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    id: {
        type: String,
        default: () => `autocomplete-${Math.random().toString(36).substring(2, 9)}`,
    },
    loading: {
        type: Boolean,
        default: false,
    },
    debounceTime: {
        type: Number,
        default: 300,
    },
});

const emit = defineEmits(['update:modelValue', 'select', 'search']);

const inputValue = ref(props.modelValue);
const suggestions = ref([]);
const isLoading = ref(false);
const showSuggestions = ref(false);
const selectedIndex = ref(-1);
const debounceTimeout = ref(null);
const highlightedIndex = ref(-1);

const containerRef = ref(null);

// Atualiza o valor do input quando o modelValue muda
watch(() => props.modelValue, (newValue) => {
    inputValue.value = newValue;
});

// Busca sugestões quando o valor do input muda
watch(inputValue, (newValue) => {
    emit('update:modelValue', newValue);
    
    if (debounceTimeout.value) {
        clearTimeout(debounceTimeout.value);
    }
    
    if (!newValue || newValue.length < 2) {
        suggestions.value = [];
        showSuggestions.value = false;
        return;
    }
    
    debounceTimeout.value = setTimeout(() => {
        fetchSuggestions(newValue);
    }, 300);
});

// Busca sugestões na API
const fetchSuggestions = async (query) => {
    isLoading.value = true;
    
    try {
        const response = await fetch(`${props.endpoint}?query=${encodeURIComponent(query)}`);
        const data = await response.json();
        
        suggestions.value = data;
        showSuggestions.value = data.length > 0;
        selectedIndex.value = -1;
    } catch (error) {
        console.error('Erro ao buscar sugestões:', error);
        suggestions.value = [];
        showSuggestions.value = false;
    } finally {
        isLoading.value = false;
    }
};

// Seleciona uma sugestão
const selectSuggestion = (suggestion) => {
    inputValue.value = suggestion;
    emit('update:modelValue', suggestion);
    emit('select', suggestion);
    showSuggestions.value = false;
};

// Fecha as sugestões quando clica fora do componente
const handleClickOutside = (event) => {
    if (containerRef.value && !containerRef.value.contains(event.target)) {
        showSuggestions.value = false;
    }
};

// Manipula as teclas de navegação
const handleKeyDown = (event) => {
    if (!showSuggestions.value) return;
    
    switch (event.key) {
        case 'ArrowDown':
            event.preventDefault();
            highlightedIndex.value = Math.min(highlightedIndex.value + 1, suggestions.value.length - 1);
            break;
        case 'ArrowUp':
            event.preventDefault();
            highlightedIndex.value = Math.max(highlightedIndex.value - 1, -1);
            break;
        case 'Enter':
            event.preventDefault();
            if (highlightedIndex.value >= 0) {
                selectSuggestion(suggestions.value[highlightedIndex.value]);
            }
            break;
        case 'Escape':
            event.preventDefault();
            showSuggestions.value = false;
            break;
    }
};

// Adiciona e remove os event listeners
onMounted(() => {
    document.addEventListener('click', handleClickOutside);
    document.addEventListener('keydown', handleKeyDown);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
    document.removeEventListener('keydown', handleKeyDown);
});

// Computed para verificar se o input está vazio
const isEmpty = computed(() => !inputValue.value || inputValue.value.length === 0);

const onInput = debounce((e) => {
    emit('update:modelValue', e.target.value);
    emit('search', e.target.value);
}, props.debounceTime);

const onBlur = () => {
    // Pequeno atraso para permitir que o clique na sugestão seja processado
    setTimeout(() => {
        showSuggestions.value = false;
    }, 200);
};
</script>

<template>
    <div ref="containerRef" class="relative">
        <InputLabel v-if="label" :for="id" :value="label" />
        
        <div class="relative">
            <TextInput
                :id="id"
                v-model="inputValue"
                :placeholder="placeholder"
                :disabled="disabled"
                class="mt-1 block w-full"
                @focus="showSuggestions = suggestions.length > 0"
                @input="onInput"
                @blur="onBlur"
            />
            
            <div v-if="loading" class="absolute right-3 top-1/2 transform -translate-y-1/2">
                <div class="animate-spin rounded-full h-4 w-4 border-t-2 border-b-2 border-blue-500"></div>
            </div>
        </div>
        
        <div v-if="showSuggestions && suggestions.length > 0" class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto">
            <ul class="py-1">
                <li
                    v-for="(suggestion, index) in suggestions"
                    :key="index"
                    :class="[
                        'px-4 py-2 cursor-pointer hover:bg-gray-100',
                        { 'bg-gray-100': index === highlightedIndex }
                    ]"
                    @click="selectSuggestion(suggestion)"
                >
                    {{ suggestion }}
                </li>
            </ul>
        </div>
    </div>
</template> 