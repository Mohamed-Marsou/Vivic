import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/home.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView
    },
    {
      path: '/collections',
      name: 'collections',
      component: () => import('../views/collections.vue')
    },
    {
      path: '/category',
      name: 'category',
      component: () => import('../views/category-page.vue')
    },
    {
      path: '/contact-us',
      name: 'contact',
      component: () => import('../views/contact.vue')
    },
    {
      path: '/about-us',
      name: 'about',
      component: () => import('../views/about.vue')
    },
    {
      path: '/product',
      name: 'product-page',
      component: () => import('../views/product-page.vue')
    },
    {
      path: '/user/auth',
      name: 'user-auth',
      component: () => import('../views/user-auth.vue')
    },
    {
      path: '/cart',
      name: 'cart',
      component: () => import('../views/cart.vue')
    },
    {
      path: '/wishlist',
      name: 'wishlist',
      component: () => import('../views/wishlist.vue')
    },
    {
      path: '/admin/login',
      name: 'admin-auth',
      component: () => import('../views/admin/admin-auth.vue')
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: () => import('../views/admin/dashboard.vue')
    },
  ]
})

export default router
