<template>
  <div class="fixed bottom-4 right-4 z-50" v-if="showInstallPrompt && !isIOS">
    <button @click="installPWA" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-full shadow-lg flex items-center gap-2 font-semibold transition-all duration-200 transform hover:scale-105">
      <span>📱</span> Pasang Aplikasi
    </button>
    <button @click="dismissInstall" class="absolute -top-2 -right-2 bg-gray-800 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold hover:bg-gray-700">
      ×
    </button>
  </div>

  <!-- iOS Install Instructions (Safari doesn't support beforeinstallprompt) -->
  <div class="fixed bottom-4 right-4 z-50" v-if="showInstallPrompt && isIOS">
    <div class="bg-blue-900 text-white p-4 rounded-xl shadow-lg max-w-sm flex flex-col gap-2">
      <div class="flex items-center gap-2">
        <span class="text-2xl">📱</span>
        <span class="font-semibold">Tambah ke Layar Utama</span>
      </div>
      <p class="text-sm text-blue-200">Tekan tombol <strong>Share (Bagikan)</strong> di Safari, lalu pilih <strong>"Tambah ke Layar Utama"</strong>.</p>
      <button @click="dismissInstall" class="bg-blue-700 hover:bg-blue-600 px-4 py-2 rounded-lg font-semibold self-end">
        Mengerti
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const showInstallPrompt = ref(false)
const isIOS = ref(false)

onMounted(() => {
  // Detect iOS
  isIOS.value = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream

  // Listen for PWA install prompt
  window.addEventListener('pwa-install-available', () => {
    showInstallPrompt.value = true
  })

  // Check if already installed
  if (window.matchMedia('(display-mode: standalone)').matches) {
    showInstallPrompt.value = false
  }

  // Also check for iOS standalone
  if (window.navigator.standalone === true) {
    showInstallPrompt.value = false
  }
})

const installPWA = () => {
  if (window.installPWA) {
    window.installPWA()
  }
}

const dismissInstall = () => {
  showInstallPrompt.value = false
  localStorage.setItem('pwa-install-dismissed', Date.now().toString())
}
</script>