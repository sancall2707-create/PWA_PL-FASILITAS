<template>
  <div class="min-h-screen flex items-center justify-center bg-blue-900 p-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-2xl p-8 space-y-6">
      <!-- Logo & Header -->
      <div class="text-center space-y-2">
        <div class="w-16 h-16 bg-yellow-400 rounded-full flex items-center justify-center font-bold text-2xl text-blue-900 border-4 border-white mx-auto shadow">
          PL
        </div>
        <h1 class="text-2xl font-bold text-gray-800">Pangudi Luhur Deltamas</h1>
        <p class="text-sm text-gray-500">Daftar Akun Baru</p>
      </div>

      <!-- Error Alert -->
      <div v-if="authStore.error" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 text-sm rounded">
        {{ authStore.error }}
      </div>

      <!-- Info: Role always 'user' -->
      <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-3 text-xs rounded">
        ℹ️ Akun yang mendaftar selalu mendapatkan role <strong>User</strong>. Tidak dapat memilih role admin.
      </div>

      <!-- Form -->
      <form @submit.prevent="handleRegister" class="space-y-4">
        <div>
          <label class="block text-sm font-bold text-gray-700 mb-1">Nama Lengkap *</label>
          <input type="text" v-model="form.name" required placeholder="Nama Anda" class="w-full border-gray-300 rounded-lg p-3 border">
        </div>

        <div>
          <label class="block text-sm font-bold text-gray-700 mb-1">Email Sekolah *</label>
          <input type="email" v-model="form.email" required placeholder="user@pangudiluhur.sch.id" class="w-full border-gray-300 rounded-lg p-3 border">
        </div>

        <div>
          <label class="block text-sm font-bold text-gray-700 mb-1">No HP / WA</label>
          <input type="tel" v-model="form.phone_number" placeholder="08xxxxxxxxxx" class="w-full border-gray-300 rounded-lg p-3 border">
        </div>

        <div>
          <label class="block text-sm font-bold text-gray-700 mb-1">Password *</label>
          <input type="password" v-model="form.password" required placeholder="Min. 8 karakter" class="w-full border-gray-300 rounded-lg p-3 border">
          <p class="text-xs text-gray-500 mt-1">Gunakan kombinasi huruf, angka, dan simbol untuk keamanan maksimal.</p>
        </div>

        <div>
          <label class="block text-sm font-bold text-gray-700 mb-1">Konfirmasi Password *</label>
          <input type="password" v-model="form.password_confirmation" required placeholder="Ulangi password" class="w-full border-gray-300 rounded-lg p-3 border">
        </div>

        <button type="submit" :disabled="authStore.loading" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-lg transition">
          {{ authStore.loading ? 'Mendaftar...' : 'Daftar Akun' }}
        </button>
      </form>

      <!-- Links -->
      <div class="text-center text-sm space-y-2 border-t pt-4">
        <p class="text-gray-600">
          Sudah punya akun? 
          <router-link to="/login" class="text-blue-600 font-bold hover:underline">
            Masuk di sini
          </router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth.js'

const form = reactive({
  name: '',
  email: '',
  phone_number: '',
  password: '',
  password_confirmation: '',
})

const router = useRouter()
const authStore = useAuthStore()

const handleRegister = async () => {
  try {
    await authStore.register(
      form.name,
      form.email,
      form.password,
      form.password_confirmation,
      form.phone_number
    )
    router.push('/')
  } catch (e) {
    // Error handled in store
  }
}
</script>
