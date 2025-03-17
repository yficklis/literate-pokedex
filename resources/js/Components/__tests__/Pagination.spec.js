import { mount } from '@vue/test-utils';
import Pagination from '@/Components/Pagination.vue';

describe('Pagination.vue', () => {
  it('renders correctly with links', () => {
    const links = [
      { url: null, label: '&laquo; Previous', active: false },
      { url: 'http://example.com/page/1', label: '1', active: true },
      { url: 'http://example.com/page/2', label: '2', active: false },
      { url: 'http://example.com/page/3', label: '3', active: false },
      { url: 'http://example.com/page/2', label: 'Next &raquo;', active: false }
    ];
    
    const wrapper = mount(Pagination, {
      props: {
        links: links
      },
      global: {
        stubs: {
          Link: {
            template: '<a :href="href"><slot /></a>',
            props: ['href']
          }
        }
      }
    });
    
    expect(wrapper.find('nav').exists()).toBe(true);
    expect(wrapper.findAll('a').length).toBe(5);
    expect(wrapper.findAll('a')[1].classes()).toContain('bg-blue-500');
  });
  
  it('does not render when links are empty', () => {
    const wrapper = mount(Pagination, {
      props: {
        links: []
      }
    });
    
    expect(wrapper.find('nav').exists()).toBe(false);
  });
  
  it('does not render when links are null', () => {
    const wrapper = mount(Pagination, {
      props: {
        links: null
      }
    });
    
    expect(wrapper.find('nav').exists()).toBe(false);
  });
  
  it('renders disabled links correctly', () => {
    const links = [
      { url: null, label: '&laquo; Previous', active: false },
      { url: 'http://example.com/page/1', label: '1', active: true },
      { url: null, label: 'Next &raquo;', active: false }
    ];
    
    const wrapper = mount(Pagination, {
      props: {
        links: links
      },
      global: {
        stubs: {
          Link: {
            template: '<a :href="href"><slot /></a>',
            props: ['href']
          }
        }
      }
    });
    
    const allLinks = wrapper.findAll('a');
    expect(allLinks[0].classes()).toContain('text-gray-500');
    expect(allLinks[2].classes()).toContain('text-gray-500');
  });
}); 