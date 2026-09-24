<template>
  <div class="space-y-8">
    <!-- Unit Admin Header -->
    <div class="bg-green-800 text-white p-6 rounded-xl shadow-lg flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold flex items-center">
          <span class="mr-2">🏫</span> Dashboard Admin Unit
        </h1>
        <p class="text-green-200 text-sm mt-1">
          Unit: <span class="font-bold text-yellow-200">{{ unitName }}</span> - Kelola Antrean & Riwayat Unit Anda
        </p>
      </div>
      <div class="flex gap-2">
        <button @click="activeTab = 'approvals'" :class="activeTab === 'approvals' ? 'bg-yellow-400 text-green-900 font-bold' : 'bg-green-700 text-white'" class="px-4 py-2 rounded-lg text-sm transition">
          Antrean Keputusan ({{ pendingBookings.length }})
        </button>
        <button @click="activeTab = 'history'" :class="activeTab === 'history' ? 'bg-yellow-400 text-green-900 font-bold' : 'bg-green-700 text-white'" class="px-4 py-2 rounded-lg text-sm transition">
          Riwayat Unit
        </button>
      </div>
    </div>

    <!-- Stats Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <div class="bg-white p-6 rounded-xl shadow border-l-4 border-yellow-500">
        <p class="text-gray-500 text-sm">Menunggu Keputusan Unit</p>
        <p class="text-3xl font-bold text-yellow-600">{{ pendingBookings.length }}</p>
      </div>
      <div class="bg-white p-6 rounded-xl shadow border-l-4 border-green-500">
        <p class="text-gray-500 text-sm">Disetujui Unit</p>
        <p class="text-3xl font-bold text-green-600">{{ approvedBookings.length }}</p>
      </div>
      <div class="bg-white p-6 rounded-xl shadow border-l-4 border-blue-500">
        <p class="text-gray-500 text-sm">Total Riwayat Unit</p>
        <p class="text-3xl font-bold text-blue-600">{{ unitBookings.length }}</p>
      </div>
    </div>

    <!-- Alert -->
    <div v-if="alertMessage" :class="alertType === 'error' ? 'bg-red-100 text-red-700 border-red-500' : 'bg-green-100 text-green-700 border-green-500'" class="p-4 rounded-lg border-l-4 text-sm font-semibold">
      {{ alertMessage }}
    </div>

    <!-- TAB 1: ANTREAN KEPUTUSAN UNIT -->
    <div v-if="activeTab === 'approvals'" class="space-y-4">
      <h2 class="text-2xl font-bold text-gray-800 flex items-center">
        <span class="mr-2">⏳</span> Antrean Keputusan Unit {{ unitName }}
      </h2>

      <div class="bg-yellow-50 border-2 border-yellow-300 rounded-lg p-4 mb-4 text-sm">
        <p class="font-bold text-yellow-800 mb-1">⚠️ Peran Admin Unit</p>
        <p class="text-yellow-700">
          Anda hanya bisa melihat dan memutuskan pengajuan untuk <strong>{{ unitName }}</strong>.
          Tombol "Setujui" di bawah ini = Setujui Tahap Unit (Menjadikan status <span class="font-bold">approved</span>).
          Untuk pembatalan, gunakan tombol "Tolak".
        </p>
      </div>

      <div v-if="pendingBookings.length === 0" class="bg-gray-50 p-8 rounded-xl text-center text-gray-500">
        Tidak ada pengajuan yang menunggu keputusan unit.
      </div>

      <div v-else class="space-y-4">
        <div v-for="booking in pendingBookings" :key="booking.id" class="bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-500">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Col 1: Item & Category -->
            <div>
              <span class="px-2.5 py-1 rounded text-xs font-bold uppercase tracking-wider" :class="isVehicle(booking) ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'">
                {{ isVehicle(booking) ? 'Kendaraan' : 'Fasilitas Tempat' }}
              </span>
              <h3 class="text-xl font-bold text-gray-800 mt-2">{{ booking.bookable?.name }}</h3>
              <p class="text-sm text-gray-600">Kategori: <span class="font-semibold">{{ booking.category?.name }}</span></p>

              <div v-if="isVehicle(booking)" class="mt-2 text-xs text-gray-600 space-y-0.5">
                <p>Driver: {{ booking.driver || '-' }}</p>
                <p>Keperluan: {{ booking.purpose || '-' }}</p>
              </div>
              <div v-else class="mt-2 text-xs text-gray-600 space-y-0.5">
                <p>Penyelenggara: {{ booking.organizer || '-' }}</p>
                <p>Kegiatan: {{ booking.event_name || '-' }}</p>
                <p v-if="booking.requested_facilities_qty">Fasilitas: {{ booking.requested_facilities_qty }} unit</p>
              </div>
            </div>

            <!-- Col 2: Schedule & Contact -->
            <div class="text-sm space-y-1">
              <p><span class="font-semibold">Pemohon:</span> {{ booking.user?.name }} ({{ booking.user?.email }})</p>
              <p><span class="font-semibold">Penanggung Jawab:</span> {{ booking.responsible_person }}</p>
              <p><span class="font-semibold">No HP/WA:</span> {{ booking.responsible_phone }}</p>
              <p><span class="font-semibold">Tanggal:</span> {{ formatDate(booking.date_from) }} s/d {{ formatDate(booking.date_to) }}</p>
              <p><span class="font-semibold">Waktu:</span> {{ booking.time_from }} - {{ booking.time_to }} WIB</p>
              <p v-if="booking.confirmation_status" class="text-yellow-700 font-semibold">
                Konfirmasi Sekolah: {{ booking.confirmation_status === 'sudah' ? '✓ Sudah' : '⏳ Belum' }}
              </p>
            </div>

            <!-- Col 3: Actions (Unit Level) -->
            <div class="flex flex-col justify-between items-end border-t md:border-t-0 pt-4 md:pt-0">
              <span class="text-xs font-bold px-3 py-1 rounded bg-yellow-100 text-yellow-800">
                Menunggu Keputusan Unit
              </span>

              <div class="flex gap-2 mt-4 w-full md:w-auto">
                <button @click="updateStatus(booking.id, 'rejected')" class="flex-1 md:flex-initial bg-red-600 hover:bg-red-700 text-white text-xs font-bold py-2.5 px-4 rounded-lg shadow transition">
                  ✕ Tolak
                </button>
                <button @click="updateStatus(booking.id, 'approved')" class="flex-1 md:flex-initial bg-green-600 hover:bg-green-700 text-white text-xs font-bold py-2.5 px-4 rounded-lg shadow transition">
                  ✓ Setujui Tahap Unit
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- TAB 2: RIWAYAT UNIT -->
    <div v-if="activeTab === 'history'" class="space-y-4">
      <h2 class="text-2xl font-bold text-gray-800 flex items-center">
        <span class="mr-2">📋</span> Riwayat Pengajuan Unit {{ unitName }}
      </h2>

      <!-- Filter Status -->
      <div class="flex gap-2 flex-wrap">
        <button @click="filterStatus = null" :class="filterStatus === null ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700'" class="px-4 py-2 rounded-full text-sm font-semibold transition">
          Semua ({{ unitBookings.length }})
        </button>
        <button @click="filterStatus = 'pending'" :class="filterStatus === 'pending' ? 'bg-yellow-600 text-white' : 'bg-yellow-100 text-yellow-700'" class="px-4 py-2 rounded-full text-sm font-semibold transition">
          Menunggu ({{ unitPendingCount }})
        </button>
        <button @click="filterStatus = 'approved'" :class="filterStatus === 'approved' ? 'bg-green-600 text-white' : 'bg-green-100 text-green-700'" class="px-4 py-2 rounded-full text-sm font-semibold transition">
          Disetujui ({{ unitApprovedCount }})
        </button>
        <button @click="filterStatus = 'rejected'" :class="filterStatus === 'rejected' ? 'bg-red-600 text-white' : 'bg-red-100 text-red-700'" class="px-4 py-2 rounded-full text-sm font-semibold transition">
          Ditolak ({{ unitRejectedCount }})
        </button>
      </div>

      <div v-if="filteredBookings.length === 0" class="bg-gray-50 p-8 rounded-xl text-center text-gray-500">
        Tidak ada riwayat untuk filter ini.
      </div>

      <div v-else class="space-y-4">
        <div v-for="booking in filteredBookings" :key="booking.id" class="bg-white rounded-lg shadow p-6 border-l-4" :class="statusColor(booking.status)">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <h3 class="text-xl font-bold text-gray-800 mb-1">{{ booking.bookable?.name }}</h3>
              <p class="text-sm text-gray-600 mb-3">
                Kategori: <span class="font-semibold">{{ booking.category?.name }}</span>
              </p>

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

            <div class="flex flex-col justify-between">
              <div class="space-y-2 text-sm">
                <p><span class="font-semibold">Tanggal:</span> {{ formatDateRange(booking.date_from, booking.date_to) }}</p>
                <p><span class="font-semibold">Waktu:</span> {{ booking.time_from }} - {{ booking.time_to }}</p>
                <p><span class="font-semibold">Penanggung Jawab:</span> {{ booking.responsible_person }}</p>
                <p><span class="font-semibold">No HP/WA:</span> {{ booking.responsible_phone }}</p>
              </div>

              <div class="mt-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <span :class="statusBadge(booking.status)" class="px-3 py-1 rounded-full text-xs font-bold">
                    {{ formatStatus(booking.status) }}
                  </span>
                  <span v-if="booking.confirmation_status === 'sudah'" class="text-green-600 font-bold text-xs bg-green-50 px-2 py-1 rounded">
                    ✓ Dikonfirmasi
                  </span>
                  <span v-else class="text-yellow-600 font-bold text-xs bg-yellow-50 px-2 py-1 rounded">
                    ⏳ Menunggu Konfirmasi
                  </span>
                </div>

                <div v-if="booking.status === 'pending'" class="flex gap-2">
                  <button @click="updateStatus(booking.id, 'rejected')" class="text-red-600 hover:text-red-800 text-xs font-bold">
                    Tolak
                  </button>
                  <button @click="updateStatus(booking.id, 'approved')" class="text-green-600 hover:text-green-800 text-xs font-bold">
                    Setujui
                  </button>
                </div>
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
import apiClient from '../services/api.js'

const activeTab = ref('approvals')
const allBookings = ref([])
const unitName = ref('')
const alertMessage = ref('')
const alertType = ref('success')
const filterStatus = ref(null)

// Prop: unit facility IDs yang dikelola oleh admin unit ini
// Mapping: unitName -> facility IDs
const UNIT_FACILITIES = {
  'SD': [1, 2],      // Aula SD + maybe others
  'SMP': [2],        // Aula SMP
  'SMA': [3],        // Aula SMA
  'Lapangan': [4],   // Lapangan Sekolah
}

const getUnitFromUser = (user) => {
  // Logic: ambil dari role atau custom field
  // Untuk demo: hardcode berdasarkan user email/name
  if (user?.email?.includes('sd')) return 'SD'
  if (user?.email?.includes('smp')) return 'SMP'
  if (user?.email?.includes('sma')) return 'SMA'
  if (user?.email?.includes('lapangan')) return 'Lapangan'
  return 'SD' // Default
}

const formatDate = (dateStr) => {
  return new Date(dateStr).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
  })
}

const formatDateRange = (from, to) => {
  const fromDate = new Date(from).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
  const toDate = new Date(to).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
  return from === to ? fromDate : `${fromDate} s/d ${toDate}`
}

const formatStatus = (status) => {
  const map = {
    pending: 'Menunggu',
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

const isVehicle = (booking) => booking.bookable_type?.includes('Vehicle')

onMounted(async () => {
  // Simulasi: get user info for unit determination
  try {
    const authRes = await apiClient.get('/me')
    const user = authRes.data.user
    unitName.value = getUnitFromUser(user)
    await fetchData()
  } catch (e) {
    unitName.value = 'SD'
    await fetchData()
  }
})

const fetchData = async () => {
  try {
    const res = await apiClient.get('/bookings')
    allBookings.value = res.data.bookings
  } catch (e) {
    showAlert('Gagal memuat data', 'error')
  }
}

// Filter bookings hanya untuk unit ini
const unitBookings = computed(() => {
  if (!unitName.value) return []
  const facilityIds = UNIT_FACILITIES[unitName.value] || []
  
  return allBookings.value.filter(booking => {
    if (isVehicle(booking)) {
      // Kendaraan: bisa dikelola semua unit admin unit
      return true
    }
    // Fasilitas: filter by facility ID
    return facilityIds.includes(booking.bookable_id)
  })
})

const pendingBookings = computed(() => unitBookings.value.filter(b => b.status === 'pending'))
const approvedBookings = computed(() => unitBookings.value.filter(b => b.status === 'approved'))
const rejectedBookings = computed(() => unitBookings.value.filter(b => b.status === 'rejected'))

const unitPendingCount = computed(() => pendingBookings.value.length)
const unitApprovedCount = computed(() => approvedBookings.value.length)
const unitRejectedCount = computed(() => rejectedBookings.value.length)

const filteredBookings = computed(() => {
  if (!filterStatus.value) return unitBookings.value
  return unitBookings.value.filter(b => b.status === filterStatus.value)
})

const updateStatus = async (id, status) => {
  alertMessage.value = ''
  try {
    await apiClient.patch(`/bookings/${id}/status`, { status })
    showAlert(`Peminjaman berhasil di-${status === 'approved' ? 'setujui' : 'tolak'}.`, 'success')
    await fetchData()
  } catch (e) {
    const msg = e.response?.data?.conflict_detail || e.response?.data?.message || 'Gagal mengubah status.'
    showAlert(msg, 'error')
  }
}

const showAlert = (msg, type = 'success') => {
  alertMessage.value = msg
  alertType.value = type
  setTimeout(() => {
    alertMessage.value = ''
  }, 5000)
}
</script>