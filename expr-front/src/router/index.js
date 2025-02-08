import { createRouter, createWebHistory } from 'vue-router';
import Login from '../components/Login.vue';
import Home from '../views/Home.vue';
import Storage from '../views/Storage.vue';
import Parcel from '../views/Parcel.vue';
import ParcelRecipient from '../views/ParcelRecipient.vue';
import ParcelPickupPoint from '../views/ParcelPickupPoint.vue';
import OrderTracking from '@/views/OrderTracking.vue'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home,
    meta: { requiresAuth: true }
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
  },
  {
    path: '/storage',
    name: 'Storage',
    component: Storage,
    meta: { requiresAuth: true }
  },
  {
    path: '/parcel',
    name: 'Parcel',
    component: Parcel,
    meta: { requiresAuth: true }
  },
  {
    path: '/parcel/recipient',
    name: 'ParcelRecipient',
    component: ParcelRecipient,
    meta: { requiresAuth: true }
  },
  {
    path: '/parcel/pickup-point',
    name: 'ParcelPickupPoint',
    component: ParcelPickupPoint,
    meta: { requiresAuth: true }
  },
  {
    path: '/parcel/summary',
    name: 'parcel-summary',
    component: () => import('../views/ParcelSummary.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/parcel/payment',
    name: 'parcel-payment',
    component: () => import('../views/ParcelPayment.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/order-tracking',
    name: 'order-tracking',
    component: OrderTracking,
    meta: { requiresAuth: true }
  },
  {
    path: '/orders',
    name: 'orders',
    component: () => import('../views/Orders.vue')
  },
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
});

// Navigation guard
router.beforeEach(async (to, from, next) => {
  // Check if the route requires authentication
  if (to.matched.some(record => record.meta.requiresAuth)) {
    try {
      // Try to get current user
      const response = await fetch('/api/users/current')
      const data = await response.json()
      
      if (response.ok) {
        // User is authenticated, proceed
        next()
      } else {
        // User is not authenticated, redirect to login
        next({
          path: '/login',
          query: { redirect: to.fullPath }
        })
      }
    } catch (error) {
      console.error('Authentication check failed:', error)
      // On error, redirect to login
      next({
        path: '/login',
        query: { redirect: to.fullPath }
      })
    }
  } else {
    // Route doesn't require auth, proceed
    next()
  }
})

export default router;
