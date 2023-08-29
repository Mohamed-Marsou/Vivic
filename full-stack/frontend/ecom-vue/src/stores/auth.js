import { defineStore } from 'pinia'
import { useRouter } from 'vue-router'
import Cookies from 'js-cookie'

import api from '../http/api'

export const useAuthtStore = defineStore('userAuth', {
  state: () => ({
    router: useRouter(),
    isAuth: false
  }),
  actions: {
    checkAuth() {
      const token = Cookies.get('auth-token')
      if (token) {
        api.defaults.headers.common['Authorization'] = `Bearer ${token}`
        this.isAuth = true
      } 
    },
    logOut(){
      Cookies.remove('auth-token');
      Cookies.remove('auth-user');
      this.isAuth = false
    }
  }
})
