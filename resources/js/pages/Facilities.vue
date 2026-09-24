<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between border-b pb-4">
      <div>
        <h1 class="text-3xl font-bold text-green-900 flex items-center">
          <span class="mr-2">🏛️</span> Ruangan & Lapangan
        </h1>
        <p class="text-gray-600 text-sm mt-1">Daftar fasilitas ruang yang dapat dipinjam</p>
      </div>
      <router-link to="/booking/new?type=facility" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow text-sm transition">
        + Pinjam Fasilitas
      </router-link>
    </div>

    <!-- Facilities Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <div v-for="facility in facilities" :key="facility.id" class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 hover:shadow-lg transition flex flex-col justify-between">
        <div class="p-6">
          <!-- Icon -->
          <div class="w-16 h-16 bg-green-100 text-green-800 rounded-full flex items-center justify-center text-3xl font-bold mb-4">
            {{ getFacilityIcon(facility.name) }}
          </div>

          <!-- Title & Details -->
          <h2 class="text-xl font-bold text-gray-800 mb-2">{{ facility.name }}</h2>
          <div class="space-y-1 text-sm text-gray-600">
            <p><span class="font-semibold">Lokasi:</span> {{ facility.location || '-' }}</p>
            <p><span class="font-semibold">Kapasitas:</span> {{ facility.capacity ? facility.capacity + ' orang' : '-' }}</p>
          </div>
        </div>

        <!-- Action Footer -->
        <div class="p-4 bg-gray-50 border-t flex items-center justify-between">
          <span class="text-xs px-2 py-1 rounded font-bold" :class="facility.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
            {{ facility.is_active ? 'Tersedia' : 'Tidak Tersedia' }}
          </span>

          <router-link :to="`/booking/new?type=facility&id=${facility.id}`" class="bg-green-600 hover:bg-green-700 text-white text-xs font-bold py-2 px-4 rounded transition">
            Pilih & Ajukan
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, computed } from 'vue'
import { useBookingStore } from '../stores/booking.js'

const bookingStore = useBookingStore()

onMounted(async () => {
  await bookingStore.fetchFacilities()
})

const facilities = computed(() => bookingStore.facilities)

const getFacilityIcon = (name) => {
  if (name.toLowerCase().includes('aula')) return '🏛️'
  if (name.toLowerCase().includes('lapangan')) return '⚽'
  if (name.toLowerCase().includes('lab')) return '🔬'
  return '🏢'
}
</script>
