import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth.js'

import Login from '../pages/Login.vue'
import Register from '../pages/Register.vue'
import Dashboard from '../pages/Dashboard.vue'
import Vehicles from '../pages/Vehicles.vue'
import Facilities from '../pages/Facilities.vue'
import BookingForm from '../pages/BookingForm.vue'
import BookingHistory from '../pages/BookingHistory.vue'

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { requiresGuest: true },
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: { requiresGuest: true },
  },
  {
    path: '/',
    name: 'Dashboard',
    component: Dashboard,
    meta: { requiresAuth: true },
  },
  {
    path: '/vehicles',
    name: 'Vehicles',
    component: Vehicles,
    meta: { requiresAuth: true },
  },
  {
    path: '/facilities',
    name: 'Facilities',
    component: Facilities,
    meta: { requiresAuth: true },
  },
  {
    path: '/booking/new',
    name: 'BookingForm',
    component: BookingForm,
    meta: { requiresAuth: true },
  },
  {
    path: '/bookings',
    name: 'BookingHistory',
    component: BookingHistory,
    meta: { requiresAuth: true },
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/',
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/login')
  } else if (to.meta.requiresGuest && authStore.isAuthenticated) {
    next('/')
  } else {
    next()
  }
})

export default router
