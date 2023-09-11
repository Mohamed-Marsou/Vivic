import { createRouter, createWebHistory } from 'vue-router'
import {useAuthtStore} from '../stores/auth'
import HomeView from '../views/home.vue'
import {useAdminStore} from '../stores/admin'


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
      path: '/category/:slug/:id',
      name: 'category',
      component: () => import('../views/category-page.vue')
    },
    {
      path: '/newset',
      name: 'new',
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
      path: '/product/:slug', 
      name: 'product-page',
      component: () => import('../views/product-page.vue'),
      props: true
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
      path: '/profile',
      name: 'profile',
      component: () => import('../views/user-profile.vue'),
      meta: {
        requiresAuth: true, // This route requires authentication
      },
    },
    {
      path: '/admin/login',
      name: 'admin-auth',
      component: () => import('../views/admin/admin-auth.vue')
    },
    {
      path: '/success',
      name: 'success',
      component: () => import('../views/success.vue')
    },
    {
      path: '/error',
      name: 'error',
      component: () => import('../views/error-page.vue')
    },
    {
      path: '/track-orders',
      name: 'trackOrders',
      component: () => import('../views/order-track.vue')
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: () => import('../views/admin/dashboard.vue'),
      meta: {
        requiresToBeAdmin: true, // This route requires authentication
      },
    },
  ]
})
// Navigation guard for protected routes
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthtStore();
  
    if (to.meta.requiresAuth) {
      if (authStore.isAuth) {
        // User is authenticated, allow navigation
        next();
      } else {
        // User is not authenticated, redirect to login or any other route
        next('/user/auth'); 
      }
    } else {
      // Route does not require authentication, allow navigation
      next();
    }
});
router.beforeEach(async (to, from, next) => {
const adminStore = useAdminStore()
adminStore.checkAdminAuth()
    if (to.meta.requiresToBeAdmin) {
      if (adminStore.isAdmin) {
        next();
      } else {
        next('/admin/login'); 
      }
    } else {
      next();
    }
});
export default router
