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
    async getFeaturedProducts() {
      try {
        const res = await api.get('/products/featured')

        if (res.status === 200) {
          return res
        } else {
          throw new Error('Failed to fetch Featured Products ')
        }
      } catch (error) {
        console.error('An error occurred while fetching Featured Products :', error)
        throw error
      }
    },
    async getCategoryProducts(id) {
      try {
        const res = await api.get(`/products/category/${id}`)
        if (res.status === 200) {
          return res
        } else {
          throw new Error('Failed to fetch Featured Products ')
        }
      } catch (error) {
        console.error('An error occurred while fetching new category Products:', error)
        throw error
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
          let localStorageInCart = JSON.parse(localStorage.getItem('inCart')) || []

          if (localStorageInCart.length > 0) {
            // Extract only productId values and create a new array
            const productsInCart = localStorageInCart.map((item) => ({
              productId: item.productId,
              SKU: item.SKU
            }))

            if (productsInCart && productsInCart.length > 0) {
              try {
                const response = await api.get('/get/products', {
                  params: { products: productsInCart }
                })
                this.inCartCount = response.data.count
                let products = response.data.products.map((product) => {
                  const quantityObj = localStorageInCart.find((item) => item.SKU === product.SKU)
                  return {
                    product: product,
                    quantity: quantityObj.quantity
                  }
                })

                return products
              } catch (error) {
                console.error('Error fetching inCart products for guest user:', error)
              }
            } else {
              this.inCartCount = 0
            }
          }
        }
      } catch (error) {
        console.error('Error getting inCart products:', error)
      }
    },
    async addToCart(productId, SKU) {
      if (this.authStore.isAuth) {
        const userId = JSON.parse(Cookies.get('auth-user')).id
        // AUTH USER
        const payload = {
          userId,
          productId,
          SKU
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
          if (localStorageInCart[i].SKU === SKU) {
            // Increment the quantity for the existing product
            localStorageInCart[i].quantity += 1
            updated = true
            break
          }
        }
        if (!updated) {
          localStorageInCart.push({ productId: productId, SKU: SKU, quantity: 1 })
        }
        // Update the localStorage with the new inCart
        localStorage.setItem('inCart', JSON.stringify(localStorageInCart))
        this.inCartCount++
      }
    },
    async removeCartItem(pId, SKU) {
      if (this.authStore.isAuth) {
        const userId = JSON.parse(Cookies.get('auth-user')).id
        try {
          await api.delete(`/product/cart/${userId}/${pId}/${SKU}`)
          this.inCartCount = this.inCartCount - 1
          return true
        } catch (error) {
          console.error('Error removing item from cart:', error)
          return false
        }
      } else {
        // Guest User
        let localStorageInCart = JSON.parse(localStorage.getItem('inCart')) || []
        const itemIndex = localStorageInCart.findIndex(
          (item) => item.productId === pId || item.SKU === SKU
        )

        if (itemIndex !== -1) {
          localStorageInCart.splice(itemIndex, 1)
          localStorage.setItem('inCart', JSON.stringify(localStorageInCart))
          this.inCartCount = this.inCartCount - 1
          return true
        }
        return false
      }
    },
    async addToWishlist(Id, SKU) {
      if (this.authStore.isAuth) {
        const userId = JSON.parse(Cookies.get('auth-user')).id
        // AUTH USER
        const payload = {
          userId,
          Id,
          SKU
        }
        try {
          const res = await api.post(`product/wishlist`, payload)
          this.whishListCount++
        } catch (error) {
          console.log(error)
        }
      } else {
        // GUEST USER
        let localStorageWishlist = JSON.parse(localStorage.getItem('wishlist')) || []
        let isProductInWishlist = false // Flag to check if the product is already in the wishlist

        // Check if the SKU exists in the wishlist
        for (let i = 0; i < localStorageWishlist.length; i++) {
          if (localStorageWishlist[i].SKU === SKU) {
            isProductInWishlist = true
            alert('Product already in Wishlist.')
            break // Exit the loop as we found a match
          }
        }

        // If the product is not in the wishlist, add it
        if (!isProductInWishlist) {
          localStorageWishlist.push({ productId: Id, SKU: SKU })
          this.wishListCount++
          // Update the wishlist in localStorage here
          localStorage.setItem('wishlist', JSON.stringify(localStorageWishlist))
        }
      }
    },
    async getWishlistProducts() {
      try {
        if (this.authStore.isAuth) {
          const authToken = Cookies.get('auth-token')
          const userId = JSON.parse(Cookies.get('auth-user')).id
          // User is authenticated
          const response = await api.get(`/get/wishlist-products/${userId}`, {
            headers: {
              Authorization: `Bearer ${authToken}`
            }
          })

          this.whishListCount = response.data.wishlistCount || 0
          return response.data
        } else {
          // GUEST USER
          const productsWishlist = JSON.parse(localStorage.getItem('wishlist')) || []

            if (productsWishlist.length > 0) {
              const productsInWishlist = productsWishlist.map((item) => ({
                productId: item.productId,
                SKU: item.SKU
              }))
              try {
                const response = await api.get('/get/products', {
                  params: { products: productsInWishlist }
                })
                this.whishListCount = response.data.count
                let products = response.data
                return products
              } catch (error) {
                console.error('Error fetching inCart products for guest user:', error)
              }
            } else {
              this.inCartCount = 0
            }
        }
      } catch (error) {
        console.error('Error getting wishlist products:', error)
      }
    },
    async removeWishlistItem(SKU) {
      if (this.authStore.isAuth) {
        const userId = JSON.parse(Cookies.get('auth-user')).id
        try {
          await api.delete(`/product/wishlist/${userId}/${SKU}`)
          this.whishListCount = this.whishListCount - 1
          return true
        } catch (error) {
          console.error('Error removing item from cart:', error)
          return false
        }
      } else {
        let localStorageInCart = JSON.parse(localStorage.getItem('wishlist')) || []
        const itemIndex = localStorageInCart.findIndex((item) => item.SKU === SKU)

        if (itemIndex !== -1) {
          localStorageInCart.splice(itemIndex, 1)
          localStorage.setItem('wishlist', JSON.stringify(localStorageInCart))
          this.whishListCount = this.whishListCount - 1
          return true
        }
        return false
      }
    }
  },

})
