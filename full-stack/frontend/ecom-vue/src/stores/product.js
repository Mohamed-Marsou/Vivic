import { ref } from 'vue'
import { defineStore } from 'pinia'
import { useRouter } from 'vue-router'
import { useAuthtStore } from './auth'
import Cookies from 'js-cookie'

import api from '../http/api'

export const useProductStore = defineStore('product', {
  state: () => ({
    router: useRouter(),
    authStore: useAuthtStore(),
    whishListCount: 0,
    inCartCount: 0
  }),
  getters: {
    computedWhishListCount() {
      return this.whishListCount
    },
    computedInCartCountCount() {
      return this.inCartCount
    }
  },
  actions: {
    async getCategories() {
      try {
        const res = await api.get('/categories')
        if (res.status === 200) {
          return res
        } else {
          throw new Error('Failed to fetch categories')
        }
      } catch (error) {
        console.error('An error occurred while fetching categories:', error)
        throw error
      }
    },
    async getNewArrivals() {
      try {
        const res = await api.get('/products/new-arrivals')

        if (res.status === 200) {
          return res.data
        } else {
          throw new Error('Failed to fetch new arrivals')
        }
      } catch (error) {
        console.error('An error occurred while fetching new arrivals:', error)
        throw error
      }
    },
    async getHighRated() {
      try {
        const res = await api.get('/products/high-rating')

        if (res.status === 200) {
          return res
        } else {
          throw new Error('Failed to fetch new arrivals')
        }
      } catch (error) {
        console.error('An error occurred while fetching new arrivals:', error)
        throw error
      }
    },
    async getCategoryProducts(id) {
      try {
        const res = await api.get(`/products/category/${id}`)
        if (res.status === 200) {
          return res
        } else {
          throw new Error('Failed to fetch new arrivals')
        }
      } catch (error) {
        console.error('An error occurred while fetching new category Products:', error)
        throw error
      }
    },
    async addToWishlist(pId) {
      const productId = pId
      if (this.authStore.isAuth) {
        const userId = JSON.parse(Cookies.get('auth-user')).id
        // AUTH USER
        const payload = {
          userId,
          productId
        }
        try {
          const res = await api.post(`product/wishlist`, payload)
          this.router.push({ name: 'wishlist' })
          this.whishListCount++
        } catch (error) {
          console.log(error)
        }
      } else {
        // GUEST USER
        let localStorageWishlist = JSON.parse(localStorage.getItem('wishlist')) || []

        // Check if the product is already in the wishlist
        if (!localStorageWishlist.includes(pId)) {
          // Add the product to the wishlist
          localStorageWishlist.push(pId)

          // Update the localStorage with the new wishlist
          localStorage.setItem('wishlist', JSON.stringify(localStorageWishlist))
          this.whishListCount++
          console.log('Product added to local storage wishlist.')
        } else {
          console.log('Product already in local storage wishlist.')
        }
      }
    },
    async getWishlistProducts() {
      try {
        if (this.authStore.isAuth) {
          const authToken = Cookies.get('auth-token')
          const userId = JSON.parse(Cookies.get('auth-user')).id
          // User is authenticated, call the API to get wishlist products
          const response = await api.get(`/get/wishlist-products/${userId}`, {
            headers: {
              Authorization: `Bearer ${authToken}`
            }
          })

          this.whishListCount = response.data.wishlistCount || 0
          return response.data
        } else {
          // GUEST USER
          const ids = JSON.parse(localStorage.getItem('wishlist'))
          if (ids && ids.length > 0) {
            try {
              const response = await api.get('/get/products', { params: { productIds: ids } })
              this.whishListCount = response.data.count
              return response.data
            } catch (error) {
              console.error('Error fetching wishlist products for guest user:', error)
            }
          } else {
            console.log('No wishlist products found in local storage.')
            this.whishListCount = 0
          }
        }
      } catch (error) {
        console.error('Error getting wishlist products:', error)
      }
    },
    async addToCart(pId) {
      const productId = pId
      if (this.authStore.isAuth) {
        const userId = JSON.parse(Cookies.get('auth-user')).id
        // AUTH USER
        const payload = {
          userId,
          productId
        }
        try {
          const res = await api.post(`product/cart`, payload)
          this.router.push({ name: 'cart' })
          this.inCartCount++
        } catch (error) {
          console.log(error)
        }
      } else {
        // GUEST USER
        let localStorageInCart = JSON.parse(localStorage.getItem('inCart')) || []
        let updated = false

        // Check if the product is already in the inCart
        for (let i = 0; i < localStorageInCart.length; i++) {
          if (localStorageInCart[i].productId === pId) {
            // Increment the quantity for the existing product
            localStorageInCart[i].quantity += 1
            updated = true
            break
          }
        }

        if (!updated) {
          // If the product was not found, add it with quantity 1
          localStorageInCart.push({ productId: pId, quantity: 1 })
        }

        // Update the localStorage with the new inCart
        localStorage.setItem('inCart', JSON.stringify(localStorageInCart))
        this.inCartCount++
        console.log('Product added to local storage inCart.')
      }
    },
    async getInCartProducts() {
      try {
        if (this.authStore.isAuth) {
          const authToken = Cookies.get('auth-token')
          const userId = JSON.parse(Cookies.get('auth-user')).id
          // User is authenticated, call the API to get wishlist products
          const response = await api.get(`/get/cart-products/${userId}`, {
            headers: {
              Authorization: `Bearer ${authToken}`
            }
          })
          this.inCartCount = response.data.inCartlistCount || 0
          return response.data
        } else {
          // GUEST USER
          let localStorageInCart = JSON.parse(localStorage.getItem('inCart')) || [];

          // Extract only productId values and create a new array
          const productIdsInCart = localStorageInCart.map(item => item.productId);

          if (productIdsInCart && productIdsInCart.length > 0) {
            try {
              const response = await api.get('/get/products', { params: { productIds: productIdsInCart } })
              this.inCartCount = response.data.count
              
              let products = []
              // let products = [
              //   product : .....
              //   ,quantity : ...
              // ]
              return products
            } catch (error) {
              console.error('Error fetching inCart products for guest user:', error)
            }
          } else {
            console.log('No inCart products found in local storage.')
            this.inCartCount = 0
          }
        }
      } catch (error) {
        console.error('Error getting inCart products:', error)
      }
    },
    async removeCartItem(pId) {
      const userId = JSON.parse(Cookies.get('auth-user')).id
      try {
        await api.delete(`/product/cart/${userId}/${pId}`)
        this.inCartCount = this.inCartCount - 1
        return true
      } catch (error) {
        console.error('Error removing item from cart:', error)
        return false
      }
    }
  }
})
