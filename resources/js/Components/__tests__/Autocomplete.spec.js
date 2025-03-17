import { mount } from '@vue/test-utils';
import { nextTick } from 'vue';
import Autocomplete from '@/Components/Autocomplete.vue';

describe('Autocomplete.vue', () => {
  it('renders correctly with default props', () => {
    const wrapper = mount(Autocomplete, {
      props: {
        modelValue: '',
        suggestions: []
      }
    });
    
    expect(wrapper.find('input').exists()).toBe(true);
    expect(wrapper.find('input').attributes('placeholder')).toBe('Search...');
  });
  
  it('emits update:modelValue and search events when input changes', async () => {
    const wrapper = mount(Autocomplete, {
      props: {
        modelValue: '',
        suggestions: [],
        debounceTime: 0 // Set to 0 for testing
      }
    });
    
    const input = wrapper.find('input');
    await input.setValue('pika');
    
    // Wait for debounce
    await new Promise(resolve => setTimeout(resolve, 10));
    
    expect(wrapper.emitted('update:modelValue')).toBeTruthy();
    expect(wrapper.emitted('update:modelValue')[0][0]).toBe('pika');
    expect(wrapper.emitted('search')).toBeTruthy();
    expect(wrapper.emitted('search')[0][0]).toBe('pika');
  });
  
  it('shows suggestions when input is focused and suggestions are available', async () => {
    const wrapper = mount(Autocomplete, {
      props: {
        modelValue: 'pika',
        suggestions: ['Pikachu', 'Pikablu']
      }
    });
    
    const input = wrapper.find('input');
    await input.trigger('focus');
    
    expect(wrapper.find('ul').exists()).toBe(true);
    expect(wrapper.findAll('li').length).toBe(2);
    expect(wrapper.findAll('li')[0].text()).toBe('Pikachu');
    expect(wrapper.findAll('li')[1].text()).toBe('Pikablu');
  });
  
  it('emits select event when a suggestion is clicked', async () => {
    const wrapper = mount(Autocomplete, {
      props: {
        modelValue: 'pika',
        suggestions: ['Pikachu', 'Pikablu']
      }
    });
    
    const input = wrapper.find('input');
    await input.trigger('focus');
    
    const suggestion = wrapper.findAll('li')[0];
    await suggestion.trigger('mousedown');
    
    expect(wrapper.emitted('select')).toBeTruthy();
    expect(wrapper.emitted('select')[0][0]).toBe('Pikachu');
    expect(wrapper.emitted('update:modelValue')).toBeTruthy();
    expect(wrapper.emitted('update:modelValue')[0][0]).toBe('Pikachu');
  });
  
  it('shows loading indicator when loading prop is true', async () => {
    const wrapper = mount(Autocomplete, {
      props: {
        modelValue: '',
        suggestions: [],
        loading: true
      }
    });
    
    expect(wrapper.find('svg.animate-spin').exists()).toBe(true);
  });
  
  it('disables input when loading prop is true', async () => {
    const wrapper = mount(Autocomplete, {
      props: {
        modelValue: '',
        suggestions: [],
        loading: true
      }
    });
    
    expect(wrapper.find('input').attributes('disabled')).toBeDefined();
  });
}); 