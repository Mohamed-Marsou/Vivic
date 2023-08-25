import { defineStore } from 'pinia'
import { useRouter } from 'vue-router'
import Cookies from 'js-cookie';

import api from '../http/api'

export const useAuthtStore = defineStore('userAuth', {
  state: () => ({
    router: useRouter(),
    isAuth : false
  }),
  actions: {

  }
})
