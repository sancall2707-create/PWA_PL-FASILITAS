<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between border-b pb-4">
      <div>
        <h1 class="text-3xl font-bold text-blue-900 flex items-center">
          <span class="mr-2">🚐</span> Kendaraan Operasional
        </h1>
        <p class="text-gray-600 text-sm mt-1">Daftar kendaraan sekolah yang dapat dipinjam</p>
      </div>
      <router-link to="/booking/new?type=vehicle" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow text-sm transition">
        + Pinjam Kendaraan
      </router-link>
    </div>

    <!-- Vehicles Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <div v-for="vehicle in vehicles" :key="vehicle.id" class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 hover:shadow-lg transition flex flex-col justify-between">
        <div class="p-6">
          <!-- Icon -->
          <div class="w-16 h-16 bg-blue-100 text-blue-800 rounded-full flex items-center justify-center text-3xl font-bold mb-4">
            {{ getVehicleIcon(vehicle.name) }}
          </div>

          <!-- Title & Specs -->
          <h2 class="text-xl font-bold text-gray-800 mb-2">{{ vehicle.name }}</h2>
          <div class="space-y-1 text-sm text-gray-600">
            <p><span class="font-semibold">Plat Nomor:</span> {{ vehicle.plate_number || '-' }}</p>
            <p><span class="font-semibold">Seri:</span> {{ vehicle.series_number || '-' }}</p>
          </div>
        </div>

        <!-- Action Footer -->
        <div class="p-4 bg-gray-50 border-t flex items-center justify-between">
          <span class="text-xs px-2 py-1 rounded font-bold" :class="vehicle.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
            {{ vehicle.is_active ? 'Tersedia' : 'Dalam Perawatan' }}
          </span>

          <router-link :to="`/booking/new?type=vehicle&id=${vehicle.id}`" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-2 px-4 rounded transition">
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
  await bookingStore.fetchVehicles()
})

const vehicles = computed(() => bookingStore.vehicles)

const getVehicleIcon = (name) => {
  if (name.toLowerCase().includes('hiace')) return '🚐'
  if (name.toLowerCase().includes('apv')) return '🚗'
  if (name.toLowerCase().includes('tosa')) return '🛺'
  if (name.toLowerCase().includes('supra')) return '🏍️'
  return '🚘'
}
</script>
