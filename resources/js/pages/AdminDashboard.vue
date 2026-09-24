<template>
  <div class="space-y-8">
    <!-- Super Admin Header -->
    <div class="bg-blue-900 text-white p-6 rounded-xl shadow-lg flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold flex items-center">
          <span class="mr-2">👑</span> Dashboard Super Admin
        </h1>
        <p class="text-blue-200 text-sm mt-1">Pangudi Luhur Deltamas - Panel Pengelolaan & Keputusan Akhir</p>
      </div>
      <div class="flex gap-2">
        <button @click="activeTab = 'approvals'" :class="activeTab === 'approvals' ? 'bg-yellow-400 text-blue-900 font-bold' : 'bg-blue-800 text-white'" class="px-4 py-2 rounded-lg text-sm transition">
          Antrean Keputusan ({{ pendingBookings.length }})
        </button>
        <button @click="activeTab = 'calendar'" :class="activeTab === 'calendar' ? 'bg-yellow-400 text-blue-900 font-bold' : 'bg-blue-800 text-white'" class="px-4 py-2 rounded-lg text-sm transition">
          Kalender Jadwal
        </button>
        <button @click="activeTab = 'management'" :class="activeTab === 'management' ? 'bg-yellow-400 text-blue-900 font-bold' : 'bg-blue-800 text-white'" class="px-4 py-2 rounded-lg text-sm transition">
          Kelola Fasilitas
        </button>
      </div>
    </div>

    <!-- Stats Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white p-6 rounded-xl shadow border-l-4 border-yellow-500">
        <p class="text-gray-500 text-sm">Menunggu Keputusan</p>
        <p class="text-3xl font-bold text-yellow-600">{{ pendingBookings.length }}</p>
      </div>
      <div class="bg-white p-6 rounded-xl shadow border-l-4 border-green-500">
        <p class="text-gray-500 text-sm">Disetujui</p>
        <p class="text-3xl font-bold text-green-600">{{ approvedBookings.length }}</p>
      </div>
      <div class="bg-white p-6 rounded-xl shadow border-l-4 border-blue-500">
        <p class="text-gray-500 text-sm">Total Peminjaman</p>
        <p class="text-3xl font-bold text-blue-600">{{ allBookings.length }}</p>
      </div>
      <div class="bg-white p-6 rounded-xl shadow border-l-4 border-purple-500">
        <p class="text-gray-500 text-sm">Fasilitas & Kendaraan</p>
        <p class="text-3xl font-bold text-purple-600">{{ totalItems }}</p>
      </div>
    </div>

    <!-- Error / Success Alert -->
    <div v-if="alertMessage" :class="alertType === 'error' ? 'bg-red-100 text-red-700 border-red-500' : 'bg-green-100 text-green-700 border-green-500'" class="p-4 rounded-lg border-l-4 text-sm font-semibold">
      {{ alertMessage }}
    </div>

    <!-- TAB 1: ANTREAN KEPUTUSAN (APPROVAL PANEL) -->
    <div v-if="activeTab === 'approvals'" class="space-y-4">
      <h2 class="text-2xl font-bold text-gray-800 flex items-center">
        <span class="mr-2">⏳</span> Antrean Keputusan Akhir (Aula, Kendaraan, Lapangan)
      </h2>

      <div v-if="pendingBookings.length === 0" class="bg-gray-50 p-8 rounded-xl text-center text-gray-500">
        Tidak ada pengajuan yang menunggu keputusan.
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
              <p class="text-sm text-gray-600">Unit/Kategori: <span class="font-semibold">{{ booking.category?.name }}</span></p>

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
            </div>

            <!-- Col 3: Actions -->
            <div class="flex flex-col justify-between items-end border-t md:border-t-0 pt-4 md:pt-0">
              <span class="text-xs font-bold px-3 py-1 rounded bg-yellow-100 text-yellow-800">
                Menunggu Persetujuan
              </span>

              <div class="flex gap-2 mt-4 w-full md:w-auto">
                <button @click="updateStatus(booking.id, 'rejected')" class="flex-1 md:flex-initial bg-red-600 hover:bg-red-700 text-white text-xs font-bold py-2.5 px-4 rounded-lg shadow transition">
                  ✕ Tolak
                </button>
                <button @click="updateStatus(booking.id, 'approved')" class="flex-1 md:flex-initial bg-green-600 hover:bg-green-700 text-white text-xs font-bold py-2.5 px-4 rounded-lg shadow transition">
                  ✓ Setujui
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- TAB 2: KALENDER JADWAL -->
    <div v-if="activeTab === 'calendar'" class="space-y-4">
      <h2 class="text-2xl font-bold text-gray-800 flex items-center">
        <span class="mr-2">📅</span> Jadwal Peminjaman Disetujui
      </h2>

      <div class="bg-white rounded-xl shadow p-6">
        <div v-if="approvedBookings.length === 0" class="text-center py-8 text-gray-500">
          Belum ada jadwal yang disetujui.
        </div>
        <div v-else class="space-y-3">
          <div v-for="booking in approvedBookings" :key="booking.id" class="p-4 rounded-lg bg-green-50 border border-green-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <div>
              <p class="font-bold text-green-900">{{ booking.bookable?.name }} ({{ booking.category?.name }})</p>
              <p class="text-xs text-green-700">Penanggung Jawab: {{ booking.responsible_person }} ({{ booking.responsible_phone }})</p>
            </div>
            <div class="text-right text-xs text-green-800 font-semibold">
              <p>{{ formatDate(booking.date_from) }} - {{ booking.time_from }} s/d {{ booking.time_to }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- TAB 3: KELOLA FASILITAS & KENDARAAN -->
    <div v-if="activeTab === 'management'" class="space-y-6">
      <h2 class="text-2xl font-bold text-gray-800 flex items-center">
        <span class="mr-2">🛠️</span> Pengelolaan Kendaraan & Fasilitas
      </h2>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Kendaraan -->
        <div class="bg-white p-6 rounded-xl shadow">
          <h3 class="font-bold text-lg text-gray-800 mb-4">Daftar Kendaraan</h3>
          <div class="space-y-2">
            <div v-for="v in vehicles" :key="v.id" class="flex justify-between items-center p-3 bg-gray-50 rounded-lg text-sm">
              <div>
                <p class="font-bold text-gray-800">{{ v.name }}</p>
                <p class="text-xs text-gray-500">Plat: {{ v.plate_number }} | Seri: {{ v.series_number }}</p>
              </div>
              <span class="text-xs px-2 py-1 rounded font-bold" :class="v.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                {{ v.is_active ? 'Aktif' : 'Non-aktif' }}
              </span>
            </div>
          </div>
        </div>

        <!-- Ruangan & Lapangan -->
        <div class="bg-white p-6 rounded-xl shadow">
          <h3 class="font-bold text-lg text-gray-800 mb-4">Daftar Ruangan & Lapangan</h3>
          <div class="space-y-2">
            <div v-for="f in facilities" :key="f.id" class="flex justify-between items-center p-3 bg-gray-50 rounded-lg text-sm">
              <div>
                <p class="font-bold text-gray-800">{{ f.name }}</p>
                <p class="text-xs text-gray-500">{{ f.location }} | Kapasitas: {{ f.capacity }}</p>
              </div>
              <span class="text-xs px-2 py-1 rounded font-bold" :class="f.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                {{ f.is_active ? 'Aktif' : 'Non-aktif' }}
              </span>
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
const vehicles = ref([])
const facilities = ref([])
const alertMessage = ref('')
const alertType = ref('success')

onMounted(async () => {
  await fetchData()
})

const fetchData = async () => {
  try {
    const [bRes, vRes, fRes] = await Promise.all([
      apiClient.get('/bookings'),
      apiClient.get('/vehicles'),
      apiClient.get('/facilities'),
    ])
    allBookings.value = bRes.data.bookings
    vehicles.value = vRes.data.vehicles
    facilities.value = fRes.data.facilities
  } catch (e) {
    showAlert('Gagal memuat data admin', 'error')
  }
}

const pendingBookings = computed(() => allBookings.value.filter(b => b.status === 'pending'))
const approvedBookings = computed(() => allBookings.value.filter(b => b.status === 'approved'))
const totalItems = computed(() => vehicles.value.length + facilities.value.length)

const isVehicle = (booking) => booking.bookable_type.includes('Vehicle')

const formatDate = (dateStr) => {
  return new Date(dateStr).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
  })
}

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
