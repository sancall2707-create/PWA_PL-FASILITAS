<template>
  <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-lg p-6 sm:p-8 border border-gray-100">
    <div class="border-b pb-4 mb-6">
      <h1 class="text-2xl font-bold text-gray-800 flex items-center">
        <span class="mr-2">📝</span> Form Pengajuan Peminjaman
      </h1>
      <p class="text-gray-600 text-sm mt-1">Lengkapi formulir di bawah ini untuk mengajukan peminjaman fasilitas</p>
    </div>

    <!-- Error Alert -->
    <div v-if="errorMessage" class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 text-sm rounded">
      {{ errorMessage }}
    </div>

    <form @submit.prevent="handleSubmit" class="space-y-6">
      <!-- 1. Tipe Peminjaman -->
      <div>
        <label class="block text-sm font-bold text-gray-700 mb-2">Tipe Peminjaman *</label>
        <div class="grid grid-cols-2 gap-4">
          <button type="button" @click="form.bookable_type = 'App\\Models\\Vehicle'" :class="form.bookable_type.includes('Vehicle') ? 'bg-blue-600 text-white font-bold' : 'bg-gray-100 text-gray-700'" class="p-3 rounded-lg border text-center transition">
            🚐 Kendaraan Operasional
          </button>
          <button type="button" @click="form.bookable_type = 'App\\Models\\Facility'" :class="form.bookable_type.includes('Facility') ? 'bg-green-600 text-white font-bold' : 'bg-gray-100 text-gray-700'" class="p-3 rounded-lg border text-center transition">
            🏛️ Ruangan & Lapangan
          </button>
        </div>
      </div>

      <!-- 2. Pilih Item (Kendaraan / Tempat) -->
      <div>
        <label class="block text-sm font-bold text-gray-700 mb-1">
          {{ isVehicle ? 'Pilih Kendaraan *' : 'Pilih Ruangan/Lapangan *' }}
        </label>
        <select v-model="form.bookable_id" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 p-3 border">
          <option value="" disabled>-- Pilih Item --</option>
          <template v-if="isVehicle">
            <option v-for="item in vehicles" :key="item.id" :value="item.id">
              {{ item.name }} (Plat: {{ item.plate_number || '-' }})
            </option>
          </template>
          <template v-else>
            <option v-for="item in facilities" :key="item.id" :value="item.id">
              {{ item.name }} ({{ item.location }})
            </option>
          </template>
        </select>
      </div>

      <!-- 3. Kategori Unit / Unit Pengaju -->
      <div>
        <label class="block text-sm font-bold text-gray-700 mb-1">Kategori / Unit Pengaju *</label>
        <select v-model="form.category_id" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 p-3 border">
          <option value="" disabled>-- Pilih Kategori --</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">
            {{ cat.name }}
          </option>
        </select>
      </div>

      <!-- Form Khusus Kendaraan -->
      <template v-if="isVehicle">
        <div>
          <label class="block text-sm font-bold text-gray-700 mb-1">Driver / Pengemudi</label>
          <input type="text" v-model="form.driver" placeholder="Nama driver (jika ada)" class="w-full border-gray-300 rounded-lg p-3 border">
        </div>

        <div>
          <label class="block text-sm font-bold text-gray-700 mb-1">Keperluan Peminjaman *</label>
          <input type="text" v-model="form.purpose" required placeholder="Tujuan / keperluan penggunaan kendaraan" class="w-full border-gray-300 rounded-lg p-3 border">
        </div>
      </template>

      <!-- Form Khusus Ruangan / Tempat -->
      <template v-else>
        <div>
          <label class="block text-sm font-bold text-gray-700 mb-1">Penyelenggara / Institusi *</label>
          <input type="text" v-model="form.organizer" required placeholder="Contoh: Gereja Bunda Theresa Cikarang / Panitia SMA" class="w-full border-gray-300 rounded-lg p-3 border">
        </div>

        <div>
          <label class="block text-sm font-bold text-gray-700 mb-1">Nama Kegiatan *</label>
          <input type="text" v-model="form.event_name" required placeholder="Nama acara/kegiatan" class="w-full border-gray-300 rounded-lg p-3 border">
        </div>

        <div>
          <label class="block text-sm font-bold text-gray-700 mb-1">Deskripsi Singkat Kegiatan</label>
          <textarea v-model="form.event_description" rows="2" placeholder="Penjelasan singkat mengenai acara" class="w-full border-gray-300 rounded-lg p-3 border"></textarea>
        </div>

        <div>
          <label class="block text-sm font-bold text-gray-700 mb-1">Fasilitas yang Dibutuhkan (Jumlah/Angka)</label>
          <input type="number" v-model.number="form.requested_facilities_qty" placeholder="Jumlah kursi, meja, atau unit fasilitas" class="w-full border-gray-300 rounded-lg p-3 border">
        </div>
      </template>

      <!-- 4. Penanggung Jawab & Kontak -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-bold text-gray-700 mb-1">Penanggung Jawab *</label>
          <input type="text" v-model="form.responsible_person" required placeholder="Nama lengkap penanggung jawab" class="w-full border-gray-300 rounded-lg p-3 border">
        </div>
        <div>
          <label class="block text-sm font-bold text-gray-700 mb-1">No HP/WA Penanggung Jawab *</label>
          <input type="text" v-model="form.responsible_phone" required placeholder="08xxxxxxxxxx" class="w-full border-gray-300 rounded-lg p-3 border">
        </div>
      </div>

      <!-- 5. Waktu & Tanggal -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Mulai *</label>
          <input type="date" v-model="form.date_from" required class="w-full border-gray-300 rounded-lg p-3 border">
        </div>
        <div>
          <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Selesai *</label>
          <input type="date" v-model="form.date_to" required class="w-full border-gray-300 rounded-lg p-3 border">
        </div>
        <div>
          <label class="block text-sm font-bold text-gray-700 mb-1">Jam Mulai *</label>
          <input type="time" v-model="form.time_from" required class="w-full border-gray-300 rounded-lg p-3 border">
        </div>
        <div>
          <label class="block text-sm font-bold text-gray-700 mb-1">Jam Selesai *</label>
          <input type="time" v-model="form.time_to" required class="w-full border-gray-300 rounded-lg p-3 border">
        </div>
      </div>

      <!-- 6. Status Konfirmasi Sekolah -->
      <div>
        <label class="block text-sm font-bold text-gray-700 mb-1">Status Konfirmasi Sekolah *</label>
        <select v-model="form.confirmation_status" required class="w-full border-gray-300 rounded-lg p-3 border">
          <option value="belum">Belum Dikonfirmasi</option>
          <option value="sudah">Sudah Dikonfirmasi</option>
        </select>
      </div>

      <!-- Submit Button -->
      <div class="pt-4 flex items-center justify-end space-x-4 border-t">
        <router-link to="/" class="text-gray-600 hover:text-gray-800 font-semibold text-sm">
          Batal
        </router-link>
        <button type="submit" :disabled="loading" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg transition">
          {{ loading ? 'Mengirim...' : 'Kirim Pengajuan' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useBookingStore } from '../stores/booking.js'
import { useAuthStore } from '../stores/auth.js'

const route = useRoute()
const router = useRouter()
const bookingStore = useBookingStore()
const authStore = useAuthStore()

const loading = ref(false)
const errorMessage = ref('')

const form = ref({
  category_id: '',
  bookable_type: route.query.type === 'facility' ? 'App\\Models\\Facility' : 'App\\Models\\Vehicle',
  bookable_id: route.query.id ? parseInt(route.query.id) : '',
  driver: '',
  purpose: '',
  organizer: '',
  event_name: '',
  event_description: '',
  responsible_person: authStore.userName || '',
  responsible_phone: authStore.user?.phone_number || '',
  requested_facilities_qty: null,
  date_from: '',
  date_to: '',
  time_from: '08:00',
  time_to: '16:00',
  confirmation_status: 'belum',
  contact_info: '',
})

const isVehicle = computed(() => form.value.bookable_type.includes('Vehicle'))
const vehicles = computed(() => bookingStore.vehicles)
const facilities = computed(() => bookingStore.facilities)
const categories = computed(() => bookingStore.categories)

onMounted(async () => {
  await Promise.all([
    bookingStore.fetchVehicles(),
    bookingStore.fetchFacilities(),
    bookingStore.fetchCategories(),
  ])
})

const handleSubmit = async () => {
  loading.value = true
  errorMessage.value = ''
  try {
    await bookingStore.createBooking(form.value)
    router.push('/bookings')
  } catch (e) {
    errorMessage.value = e.response?.data?.message || 'Terjadi kesalahan saat menyimpan pengajuan.'
  } finally {
    loading.value = false
  }
}
</script>
