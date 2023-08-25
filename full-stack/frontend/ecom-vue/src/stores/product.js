import { ref } from 'vue'
import { defineStore } from 'pinia'
import { useRouter } from 'vue-router'
import api from '../http/api'

export const useProductStore = defineStore('product', {
  state: () => ({
    router: useRouter(),

  }),
  actions: {
   async getCategories() {
        try {
            const res = await api.get('/categories');
            if (res.status === 200) {
                return res;
            } else {
                throw new Error('Failed to fetch categories');
            }
        } catch (error) {
            console.error('An error occurred while fetching categories:', error);
            throw error; 
        }
    },
    async getNewArrivals() {
        try {
            const res = await api.get('/products/new-arrivals');
            
            if (res.status === 200) {
                return res.data;
            } else {
                throw new Error('Failed to fetch new arrivals');
            }
        } catch (error) {
            console.error('An error occurred while fetching new arrivals:', error);
            throw error; 
        }
    },
    async getHighRated() {
        try {
            const res = await api.get('/products/high-rating');
            
            if (res.status === 200) {
                return res;
            } else {
                throw new Error('Failed to fetch new arrivals');
            }
        } catch (error) {
            console.error('An error occurred while fetching new arrivals:', error);
            throw error; 
        }
    },
    async getCategoryProducts(id) {
        try {
            const res = await api.get(`/products/category/${id}`);
            if (res.status === 200) {
                return res;
            } else {
                throw new Error('Failed to fetch new arrivals');
            }
        } catch (error) {
            console.error('An error occurred while fetching new category Products:', error);
            throw error; 
        }
    },

 
  }
})