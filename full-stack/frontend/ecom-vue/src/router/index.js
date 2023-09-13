import { createRouter, createWebHistory } from 'vue-router'
import { useAuthtStore } from '../stores/auth'
import HomeView from '../views/home.vue'
import { useAdminStore } from '../stores/admin'


const router = createRouter({
  
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView
      ,meta: { title: 'Home ' }
    },
    {
      path: '/collections',
      name: 'collections',
      component: () => import('../views/collections.vue')
      ,meta: { title: 'Collections' }
    },
    {
      path: '/category/:slug/:id',
      name: 'category',
      component: () => import('../views/category-page.vue')
      ,meta: { title: 'Ecommerce' }
    },
    {
      path: '/newset',
      name: 'new',
      component: () => import('../views/category-page.vue')
      ,meta: { title: 'New In' }

    },
    {
      path: '/contact-us',
      name: 'contact',
      component: () => import('../views/contact.vue')
      ,meta: { title: 'Contact us' }

    },
    {
      path: '/about-us',
      name: 'about',
      component: () => import('../views/about.vue')
      ,meta: { title: 'About us' }

    },
    {
      path: '/product/:slug', 
      name: 'product-page',
      component: () => import('../views/product-page.vue'),
      props: true
      ,meta: { title: 'Product page' }

    },
    {
      path: '/user/auth',
      name: 'user-auth',
      component: () => import('../views/user-auth.vue')
      ,meta: { title: 'Login' }
    },
    {
      path: '/cart',
      name: 'cart',
      component: () => import('../views/cart.vue')
      ,meta: { title: 'Cart' }

    },
    {
      path: '/wishlist',
      name: 'wishlist',
      component: () => import('../views/wishlist.vue')
      ,meta: { title: 'Wishlist' }

    },
    {
      path: '/profile',
      name: 'profile',
      component: () => import('../views/user-profile.vue'),
      meta: {
        requiresAuth: true, 
        title: 'Profile'
      }
    },
    {
      path: '/admin/login',
      name: 'admin-auth',
      component: () => import('../views/admin/admin-auth.vue')
      ,meta: { title: 'Admin Login' }

    },
    {
      path: '/success',
      name: 'success',
      component: () => import('../views/success.vue')
      ,meta: { title: 'Order Details' }

    },
    {
      path: '/error',
      name: 'error',
      component: () => import('../views/error-page.vue')
      ,meta: { title: '404' }

    },
    {
      path: '/track-orders',
      name: 'trackOrders',
      component: () => import('../views/order-track.vue')
      ,meta: { title: 'Track Order' }

    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: () => import('../views/admin/dashboard.vue'),
      
      meta: {
        requiresToBeAdmin: true, 
        title: 'Dashboard'
      }
    },
  ]
})
  
// Navigation guard for protected routes
router.beforeEach(async (to, from, next) => {

  const authStore = await useAuthtStore();
  const adminStore = await useAdminStore();

  console.log(adminStore.isAdmin);
  console.log(authStore.isAuth);
  // Set document title
  document.title = to.meta.title || 'Vivic'; // todo change the fallback option

  if (to.meta.requiresAuth) {
    if (authStore.isAuth) {
      // User is authenticated, allow navigation
      next();
    } else {
      next('/user/auth');
    }
  } else if (to.meta.requiresToBeAdmin) {
    if (adminStore.isAdmin) {
      // Admin is authenticated, allow navigation
      next();
    } else {
      // Admin is not authenticated, redirect to admin login or any other route
      next('/admin/login');
    }
  } else {
    // Route does not require authentication, allow navigation
    next();
  }

});

export default router
