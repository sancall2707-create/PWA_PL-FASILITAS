<template>
  <div class="space-y-6">
    <div class="border-b pb-4">
      <h1 class="text-3xl font-bold text-blue-900 flex items-center">
        <span class="mr-2">📋</span> Riwayat Pengajuan Peminjaman
      </h1>
      <p class="text-gray-600 text-sm mt-1">Kelola dan pantau status pengajuan peminjaman fasilitas Anda</p>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-8">
      <p class="text-gray-500">Memuat data pengajuan...</p>
    </div>

    <!-- Empty State -->
    <div v-else-if="bookings.length === 0" class="bg-gray-100 rounded-lg p-8 text-center">
      <p class="text-lg text-gray-600 mb-4">Belum ada pengajuan peminjaman.</p>
      <router-link to="/booking/new" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">
        Buat Pengajuan Baru
      </router-link>
    </div>

    <!-- Bookings List -->
    <div v-else class="space-y-4">
      <!-- Filter / Status Selector -->
      <div class="flex gap-2 flex-wrap">
        <button @click="filterStatus = null" :class="filterStatus === null ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'" class="px-4 py-2 rounded-full text-sm font-semibold transition">
          Semua ({{ bookings.length }})
        </button>
        <button @click="filterStatus = 'pending'" :class="filterStatus === 'pending' ? 'bg-yellow-600 text-white' : 'bg-yellow-100 text-yellow-700'" class="px-4 py-2 rounded-full text-sm font-semibold transition">
          Menunggu ({{ pendingCount }})
        </button>
        <button @click="filterStatus = 'approved'" :class="filterStatus === 'approved' ? 'bg-green-600 text-white' : 'bg-green-100 text-green-700'" class="px-4 py-2 rounded-full text-sm font-semibold transition">
          Disetujui ({{ approvedCount }})
        </button>
        <button @click="filterStatus = 'rejected'" :class="filterStatus === 'rejected' ? 'bg-red-600 text-white' : 'bg-red-100 text-red-700'" class="px-4 py-2 rounded-full text-sm font-semibold transition">
          Ditolak ({{ rejectedCount }})
        </button>
      </div>

      <!-- Bookings Cards -->
      <div v-for="booking in filteredBookings" :key="booking.id" class="bg-white rounded-lg shadow-md p-6 border-l-4" :class="statusColor(booking.status)">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Left Column: Item Info -->
          <div>
            <h3 class="text-xl font-bold text-gray-800 mb-1">{{ booking.bookable?.name }}</h3>
            <p class="text-sm text-gray-600 mb-3">
              Kategori: <span class="font-semibold">{{ booking.category?.name }}</span>
            </p>

            <!-- Conditional Content -->
            <div v-if="isVehicle(booking)" class="space-y-1 text-sm">
              <p><span class="font-semibold">Driver:</span> {{ booking.driver || '-' }}</p>
              <p><span class="font-semibold">Keperluan:</span> {{ booking.purpose || '-' }}</p>
            </div>
            <div v-else class="space-y-1 text-sm">
              <p><span class="font-semibold">Penyelenggara:</span> {{ booking.organizer || '-' }}</p>
              <p><span class="font-semibold">Kegiatan:</span> {{ booking.event_name || '-' }}</p>
              <p v-if="booking.event_description" class="text-gray-600 italic">{{ booking.event_description }}</p>
            </div>
          </div>

          <!-- Right Column: Schedule & Status -->
          <div class="flex flex-col justify-between">
            <div class="space-y-2 text-sm">
              <p><span class="font-semibold">Tanggal:</span> {{ formatDateRange(booking.date_from, booking.date_to) }}</p>
              <p><span class="font-semibold">Waktu:</span> {{ booking.time_from }} - {{ booking.time_to }}</p>
              <p><span class="font-semibold">Penanggung Jawab:</span> {{ booking.responsible_person }}</p>
              <p><span class="font-semibold">No HP/WA:</span> {{ booking.responsible_phone }}</p>
            </div>

            <!-- Status & Actions -->
            <div class="mt-4 flex items-center justify-between">
              <div class="flex items-center gap-2">
                <span :class="statusBadge(booking.status)" class="px-3 py-1 rounded-full text-xs font-bold">
                  {{ formatStatus(booking.status) }}
                </span>
                <span v-if="booking.confirmation_status === 'sudah'" class="text-green-600 font-bold text-xs bg-green-50 px-2 py-1 rounded">
                  ✓ Konfirmasi
                </span>
              </div>

              <!-- Action Buttons -->
              <div v-if="booking.status === 'pending'" class="flex gap-2">
                <button @click="editBooking(booking.id)" class="text-blue-600 hover:text-blue-800 text-xs font-bold">
                  Edit
                </button>
                <button @click="deleteBooking(booking.id)" class="text-red-600 hover:text-red-800 text-xs font-bold">
                  Hapus
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useBookingStore } from '../stores/booking.js'

const router = useRouter()
const bookingStore = useBookingStore()

const loading = ref(false)
const filterStatus = ref(null)

onMounted(async () => {
  loading.value = true
  await bookingStore.fetchBookings()
  loading.value = false
})

const bookings = computed(() => bookingStore.bookings)

const filteredBookings = computed(() => {
  if (!filterStatus.value) return bookings.value
  return bookings.value.filter(b => b.status === filterStatus.value)
})

const pendingCount = computed(() => bookings.value.filter(b => b.status === 'pending').length)
const approvedCount = computed(() => bookings.value.filter(b => b.status === 'approved').length)
const rejectedCount = computed(() => bookings.value.filter(b => b.status === 'rejected').length)

const isVehicle = (booking) => booking.bookable_type.includes('Vehicle')

const formatDateRange = (from, to) => {
  const fromDate = new Date(from).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
  })
  const toDate = new Date(to).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
  })
  return from === to ? fromDate : `${fromDate} s/d ${toDate}`
}

const formatStatus = (status) => {
  const map = {
    pending: 'Menunggu Persetujuan',
    approved: 'Disetujui',
    rejected: 'Ditolak',
    completed: 'Selesai',
  }
  return map[status] || status
}

const statusColor = (status) => {
  const map = {
    pending: 'border-yellow-500',
    approved: 'border-green-500',
    rejected: 'border-red-500',
    completed: 'border-blue-500',
  }
  return map[status] || 'border-gray-300'
}

const statusBadge = (status) => {
  const map = {
    pending: 'bg-yellow-200 text-yellow-800',
    approved: 'bg-green-200 text-green-800',
    rejected: 'bg-red-200 text-red-800',
    completed: 'bg-blue-200 text-blue-800',
  }
  return map[status] || 'bg-gray-200 text-gray-800'
}

const editBooking = (id) => {
  // TODO: Implement edit functionality
  alert('Fitur edit akan segera tersedia')
}

const deleteBooking = async (id) => {
  if (confirm('Yakin ingin menghapus pengajuan ini?')) {
    try {
      await bookingStore.deleteBooking(id)
    } catch (e) {
      alert('Gagal menghapus pengajuan')
    }
  }
}
</script>
