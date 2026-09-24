<template>
  <div class="min-h-screen bg-gray-100 flex flex-col">
    <!-- Navigation Bar -->
    <header v-if="authStore.isAuthenticated" class="bg-blue-800 text-white shadow-md">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <!-- Logo & Title -->
          <router-link to="/" class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-yellow-400 rounded-full flex items-center justify-center font-bold text-blue-900 border-2 border-white">
              PL
            </div>
            <div>
              <h1 class="font-bold text-lg leading-tight">Pangudi Luhur Deltamas</h1>
              <p class="text-xs text-blue-200">Peminjaman Fasilitas PL Deltamas</p>
            </div>
          </router-link>

          <!-- Navigation Links -->
          <nav class="hidden md:flex space-x-4 items-center">
            <router-link to="/" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-blue-700" active-class="bg-blue-900">Beranda</router-link>
            <router-link to="/vehicles" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-blue-700" active-class="bg-blue-900">Kendaraan</router-link>
            <router-link to="/facilities" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-blue-700" active-class="bg-blue-900">Ruangan & Lapangan</router-link>
            <router-link to="/bookings" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-blue-700" active-class="bg-blue-900">Riwayat Pengajuan</router-link>
          </nav>

          <!-- User Profile & Logout -->
          <div class="flex items-center space-x-4">
            <div class="text-right hidden sm:block">
              <p class="text-sm font-semibold">{{ authStore.userName }}</p>
              <span class="inline-block bg-blue-900 text-yellow-300 text-xs px-2 py-0.5 rounded-full uppercase tracking-wider font-bold">
                {{ authStore.user?.role }}
              </span>
            </div>
            <button @click="handleLogout" class="bg-red-600 hover:bg-red-700 text-white text-xs px-3 py-2 rounded-md font-medium transition">
              Keluar
            </button>
          </div>
        </div>
      </div>

      <!-- Mobile Bottom Navigation -->
      <div class="md:hidden bg-blue-900 border-t border-blue-700 flex justify-around py-2 text-xs">
        <router-link to="/" class="flex flex-col items-center text-blue-200" active-class="text-yellow-300 font-bold">
          <span>🏠</span>
          <span>Beranda</span>
        </router-link>
        <router-link to="/vehicles" class="flex flex-col items-center text-blue-200" active-class="text-yellow-300 font-bold">
          <span>🚐</span>
          <span>Kendaraan</span>
        </router-link>
        <router-link to="/facilities" class="flex flex-col items-center text-blue-200" active-class="text-yellow-300 font-bold">
          <span>🏛️</span>
          <span>Fasilitas</span>
        </router-link>
        <router-link to="/bookings" class="flex flex-col items-center text-blue-200" active-class="text-yellow-300 font-bold">
          <span>📋</span>
          <span>Riwayat</span>
        </router-link>
      </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl w-full mx-auto p-4 sm:p-6 lg:p-8">
      <router-view></router-view>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t py-4 text-center text-xs text-gray-500">
      &copy; 2026 Yayasan Pangudi Luhur Deltamas. All rights reserved.
    </footer>
  </div>
</template>

<script setup>
import { useAuthStore } from './stores/auth.js'
import { useRouter } from 'vue-router'

const authStore = useAuthStore()
const router = useRouter()

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}
</script>
