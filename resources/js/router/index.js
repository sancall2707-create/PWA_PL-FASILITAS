import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth.js'

import Login from '../pages/Login.vue'
import Register from '../pages/Register.vue'
import Dashboard from '../pages/Dashboard.vue'
import Vehicles from '../pages/Vehicles.vue'
import Facilities from '../pages/Facilities.vue'
import BookingForm from '../pages/BookingForm.vue'
import BookingHistory from '../pages/BookingHistory.vue'
import AdminDashboard from '../pages/AdminDashboard.vue'
import UnitAdminDashboard from '../pages/UnitAdminDashboard.vue'

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { guest: true },
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: { guest: true },
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
    path: '/admin',
    name: 'AdminDashboard',
    component: AdminDashboard,
    meta: { requiresAuth: true, requiresAdmin: true },
  },
  {
    path: '/admin/unit',
    name: 'UnitAdminDashboard',
    component: UnitAdminDashboard,
    meta: { requiresAuth: true, requiresAdmin: true },
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

  // Check if route requires authentication
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'Login' })
    return
  }

  // Check if route requires admin
  if (to.meta.requiresAdmin && !authStore.isAdmin) {
    next({ name: 'Dashboard' })
    return
  }

  // Redirect logged-in users away from guest pages
  if (to.meta.guest && authStore.isAuthenticated) {
    // Redirect admin to admin dashboard
    if (authStore.isAdmin) {
      next({ name: 'AdminDashboard' })
    } else {
      next({ name: 'Dashboard' })
    }
    return
  }

  next()
})

export default router