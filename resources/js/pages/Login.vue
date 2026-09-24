<template>
  <div class="min-h-screen flex items-center justify-center bg-blue-900 p-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-2xl p-8 space-y-6">
      <!-- Logo & Header -->
      <div class="text-center space-y-2">
        <div class="w-16 h-16 bg-yellow-400 rounded-full flex items-center justify-center font-bold text-2xl text-blue-900 border-4 border-white mx-auto shadow">
          PL
        </div>
        <h1 class="text-2xl font-bold text-gray-800">Pangudi Luhur Deltamas</h1>
        <p class="text-sm text-gray-500">Peminjaman Fasilitas PL Deltamas</p>
      </div>

      <!-- Error Alert -->
      <div v-if="authStore.error" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 text-sm rounded">
        {{ authStore.error }}
      </div>

      <!-- Form -->
      <form @submit.prevent="handleLogin" class="space-y-4">
        <div>
          <label class="block text-sm font-bold text-gray-700 mb-1">Email Sekolah / Pengguna</label>
          <input type="email" v-model="email" required placeholder="user@pangudiluhur.sch.id" class="w-full border-gray-300 rounded-lg p-3 border">
        </div>

        <div>
          <label class="block text-sm font-bold text-gray-700 mb-1">Password</label>
          <input type="password" v-model="password" required placeholder="••••••••" class="w-full border-gray-300 rounded-lg p-3 border">
        </div>

        <button type="submit" :disabled="authStore.loading" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-lg transition">
          {{ authStore.loading ? 'Masuk...' : 'Masuk' }}
        </button>
      </form>

      <!-- Links -->
      <div class="text-center text-sm space-y-2 border-t pt-4">
        <p class="text-gray-600">
          Belum punya akun? 
          <router-link to="/register" class="text-blue-600 font-bold hover:underline">
            Daftar Akun Baru
          </router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth.js'

const email = ref('')
const password = ref('')
const router = useRouter()
const authStore = useAuthStore()

const handleLogin = async () => {
  try {
    await authStore.login(email.value, password.value)
    router.push('/')
  } catch (e) {
    // Error handled in store
  }
}
</script>
