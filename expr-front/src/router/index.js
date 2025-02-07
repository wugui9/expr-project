import { createRouter, createWebHistory } from 'vue-router';
import Login from '../components/Login.vue';
import Home from '../views/Home.vue';
import Storage from '../views/Storage.vue';
import Parcel from '../views/Parcel.vue';
import ParcelRecipient from '../views/ParcelRecipient.vue';
import ParcelPickupPoint from '../views/ParcelPickupPoint.vue';

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home,
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
  },
  {
    path: '/parcel',
    name: 'Parcel',
    component: Parcel,
  },
  {
    path: '/parcel/recipient',
    name: 'ParcelRecipient',
    component: ParcelRecipient,
  },
  {
    path: '/parcel/pickup-point',
    name: 'ParcelPickupPoint',
    component: ParcelPickupPoint,
  },
  {
    path: '/parcel/summary',
    name: 'parcel-summary',
    component: () => import('../views/ParcelSummary.vue')
  },
  {
    path: '/parcel/payment',
    name: 'parcel-payment',
    component: () => import('../views/ParcelPayment.vue')
  }
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
});

export default router;
