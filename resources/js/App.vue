<template>
  <div class="min-h-screen bg-gray-100 flex flex-col">
    <!-- Navigation Bar & Main Content (Authenticated) -->
    <template v-if="authStore.isAuthenticated">
      <header class="bg-blue-800 text-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between gap-4 flex-wrap">
          <!-- Logo & Branding -->
          <div class="flex items-center gap-2 cursor-pointer" @click="$router.push('/')">
            <span class="text-2xl">🏢</span>
            <div class="hidden sm:block">
              <p class="font-bold text-sm">PL Deltamas</p>
              <p class="text-xs text-blue-200">Peminjaman Fasilitas</p>
            </div>
          </div>

          <!-- Main Navigation Links -->
          <nav class="flex items-center gap-2 sm:gap-4 flex-wrap">
            <router-link to="/" class="px-3 py-2 rounded-lg text-sm font-semibold transition hover:bg-blue-700">
              📊 Beranda
            </router-link>
            <router-link to="/vehicles" class="px-3 py-2 rounded-lg text-sm font-semibold transition hover:bg-blue-700">
              🚗 Kendaraan
            </router-link>
            <router-link to="/facilities" class="px-3 py-2 rounded-lg text-sm font-semibold transition hover:bg-blue-700">
              🏛️ Fasilitas
            </router-link>
            <router-link to="/bookings" class="px-3 py-2 rounded-lg text-sm font-semibold transition hover:bg-blue-700">
              📋 Riwayat
            </router-link>

            <!-- Admin Links -->
            <div v-if="authStore.isAdmin" class="hidden md:flex items-center gap-2 border-l border-blue-600 pl-4 ml-2">
              <router-link to="/admin" class="px-3 py-2 rounded-lg text-sm font-bold bg-yellow-500 text-blue-900 hover:bg-yellow-400 transition">
                👑 Super Admin
              </router-link>
              <router-link to="/admin/unit" class="px-3 py-2 rounded-lg text-sm font-bold bg-green-500 text-white hover:bg-green-600 transition">
                🏫 Admin Unit
              </router-link>
            </div>
          </nav>

          <!-- User & Logout -->
          <div class="flex items-center gap-3 border-l border-blue-600 pl-4">
            <div class="text-sm text-right hidden sm:block">
              <p class="font-semibold">{{ authStore.user?.name }}</p>
              <p class="text-xs text-blue-200 uppercase tracking-wider">
                {{ authStore.user?.role === 'admin' ? '👑 Admin' : '👤 User' }}
              </p>
            </div>
            <button @click="logout" class="px-3 py-2 rounded-lg text-sm font-semibold bg-red-600 hover:bg-red-700 transition">
              🚪 Keluar
            </button>
          </div>
        </div>

        <!-- Mobile Admin Links -->
        <div v-if="authStore.isAdmin" class="md:hidden bg-blue-900 px-4 py-2 flex gap-2 flex-wrap">
          <router-link to="/admin" class="px-3 py-1 rounded text-xs font-bold bg-yellow-500 text-blue-900 hover:bg-yellow-400">
            👑 Super Admin
          </router-link>
          <router-link to="/admin/unit" class="px-3 py-1 rounded text-xs font-bold bg-green-500 text-white hover:bg-green-600">
            🏫 Admin Unit
          </router-link>
        </div>
      </header>

      <!-- Page Content -->
      <main class="flex-1 max-w-7xl w-full mx-auto px-4 py-8">
        <router-view />
      </main>

      <!-- Footer -->
      <footer class="bg-gray-900 text-gray-400 text-center py-4 text-xs mt-8">
        <p>© 2026 Sekolah Pangudi Luhur Deltamas. Aplikasi PWA Peminjaman Fasilitas.</p>
      </footer>
    </template>

    <!-- Auth Pages (No Navbar) - Guest Only -->
    <template v-else>
      <router-view />
    </template>
  </div>
</template>

<script setup>
import { useAuthStore } from './stores/auth.js'
import { useRouter } from 'vue-router'

const authStore = useAuthStore()
const router = useRouter()

const logout = async () => {
  await authStore.logout()
  router.push({ name: 'Login' })
}
</script>