<template>
  <div class="space-y-8">
    <!-- Welcome Section -->
    <section class="text-center space-y-4">
      <div class="text-6xl">🏢</div>
      <h1 class="text-4xl font-bold text-blue-900">Pangudi Luhur Deltamas</h1>
      <p class="text-2xl font-semibold text-gray-700">Sistem Peminjaman Fasilitas</p>
      <p class="text-xl text-gray-600">
        Selamat Datang, <span class="font-bold text-blue-800">Sahabat PL Deltamas</span>
      </p>
    </section>

    <!-- Action Cards -->
    <section class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Kendaraan Operasional Card -->
      <router-link to="/vehicles" class="group">
        <div class="bg-gradient-to-br from-blue-500 to-blue-700 text-white rounded-xl p-8 shadow-lg hover:shadow-2xl transform hover:scale-105 transition cursor-pointer">
          <div class="text-5xl mb-4">🚐</div>
          <h2 class="text-2xl font-bold mb-2">Kendaraan Operasional</h2>
          <p class="text-blue-100 mb-4">Peminjaman Mobil & Motor</p>
          <div class="flex items-center justify-between">
            <span class="text-sm">Pilih kendaraan yang dibutuhkan</span>
            <span class="text-2xl">→</span>
          </div>
        </div>
      </router-link>

      <!-- Ruangan & Lapangan Card -->
      <router-link to="/facilities" class="group">
        <div class="bg-gradient-to-br from-green-500 to-green-700 text-white rounded-xl p-8 shadow-lg hover:shadow-2xl transform hover:scale-105 transition cursor-pointer">
          <div class="text-5xl mb-4">🏛️</div>
          <h2 class="text-2xl font-bold mb-2">Ruangan & Lapangan</h2>
          <p class="text-green-100 mb-4">Peminjaman Aula & Fasilitas Ruang</p>
          <div class="flex items-center justify-between">
            <span class="text-sm">Lihat ketersediaan ruangan</span>
            <span class="text-2xl">→</span>
          </div>
        </div>
      </router-link>
    </section>

    <!-- Quick Action -->
    <section class="bg-yellow-50 border-2 border-yellow-300 rounded-xl p-6 text-center">
      <p class="text-lg text-gray-700 mb-4">Ingin membuat pengajuan peminjaman baru?</p>
      <router-link to="/booking/new" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg transition transform hover:scale-105">
        Buat Pengajuan Baru
      </router-link>
    </section>

    <!-- Recent Submissions (Riwayat Pengajuan Milik User) -->
    <section>
      <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
        <span class="text-2xl mr-2">📋</span>
        Riwayat Pengajuan Terbaru Anda
      </h2>
      
      <div v-if="bookingStore.loading" class="text-center py-8">
        <p class="text-gray-500">Memuat data...</p>
      </div>

      <div v-else-if="userBookings.length === 0" class="bg-gray-100 rounded-lg p-8 text-center text-gray-500">
        <p class="text-lg">Belum ada pengajuan peminjaman.</p>
        <router-link to="/booking/new" class="text-blue-600 font-semibold hover:underline">
          Buat pengajuan pertama Anda
        </router-link>
      </div>

      <div v-else class="grid gap-4">
        <div v-for="booking in userBookings" :key="booking.id" class="bg-white rounded-lg shadow p-4 border-l-4" :class="statusColor(booking.status)">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex-1">
              <h3 class="font-bold text-lg text-gray-800">
                {{ booking.bookable?.name }}
              </h3>
              <p class="text-sm text-gray-600">
                {{ booking.responsible_person }} • {{ formatDate(booking.date_from) }}
              </p>
              <p class="text-xs text-gray-500 mt-1">
                Kategori: <span class="font-semibold">{{ booking.category?.name }}</span>
              </p>
            </div>
            <div class="flex items-center gap-3">
              <span :class="statusBadge(booking.status)" class="px-3 py-1 rounded-full text-xs font-bold whitespace-nowrap">
                {{ formatStatus(booking.status) }}
              </span>
              <span v-if="booking.confirmation_status === 'sudah'" class="text-green-600 font-bold text-xs">✓ Dikonfirmasi</span>
              <span v-else class="text-yellow-600 font-bold text-xs">⏳ Menunggu</span>
            </div>
          </div>
        </div>
      </div>

      <div class="mt-6 text-center">
        <router-link to="/bookings" class="text-blue-600 font-semibold hover:underline">
          Lihat semua riwayat pengajuan →
        </router-link>
      </div>
    </section>
  </div>
</template>

<script setup>
import { onMounted, computed } from 'vue'
import { useBookingStore } from '../stores/booking.js'
import { useAuthStore } from '../stores/auth.js'

const bookingStore = useBookingStore()
const authStore = useAuthStore()

onMounted(async () => {
  await bookingStore.fetchBookings()
})

const userBookings = computed(() => {
  return bookingStore.bookings.slice(0, 5) // Tampilkan 5 terbaru
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('id-ID', {
    weekday: 'short',
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

const formatStatus = (status) => {
  const statusMap = {
    pending: 'Menunggu Persetujuan',
    approved: 'Disetujui',
    rejected: 'Ditolak',
    completed: 'Selesai',
  }
  return statusMap[status] || status
}

const statusColor = (status) => {
  const colorMap = {
    pending: 'border-yellow-500',
    approved: 'border-green-500',
    rejected: 'border-red-500',
    completed: 'border-blue-500',
  }
  return colorMap[status] || 'border-gray-300'
}

const statusBadge = (status) => {
  const badgeMap = {
    pending: 'bg-yellow-200 text-yellow-800',
    approved: 'bg-green-200 text-green-800',
    rejected: 'bg-red-200 text-red-800',
    completed: 'bg-blue-200 text-blue-800',
  }
  return badgeMap[status] || 'bg-gray-200 text-gray-800'
}
</script>
