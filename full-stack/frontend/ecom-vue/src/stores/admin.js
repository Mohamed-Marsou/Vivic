import { defineStore } from 'pinia'
import { useRouter } from 'vue-router'
import Cookies from 'js-cookie'

import api from '../http/api'

export const useAdminStore = defineStore('adminStore', {
  state: () => ({
    router: useRouter(),
    isAdmin: false
  }),
  actions: {
    checkAdminAuth() {
      const token = Cookies.get('admin-token')
      if (token) {
        api.defaults.headers.common['Authorization'] = `Bearer ${token}`
        this.isAdmin = true
      } 
    },
    adminLogOut(){
      Cookies.remove('admin-token');
      Cookies.remove('auth-admin');
      this.isAdmin = false
      this.router.push('/')
    }
  }
})
