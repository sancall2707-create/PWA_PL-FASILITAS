import { defineStore } from 'pinia'
import apiClient from '../services/api.js'

export const useBookingStore = defineStore('booking', {
  state: () => ({
    vehicles: [],
    facilities: [],
    categories: [],
    bookings: [],
    loading: false,
    error: null,
  }),

  actions: {
    async fetchVehicles() {
      try {
        const response = await apiClient.get('/vehicles')
        this.vehicles = response.data.vehicles
      } catch (e) {
        this.error = 'Gagal memuat daftar kendaraan'
      }
    },

    async fetchFacilities() {
      try {
        const response = await apiClient.get('/facilities')
        this.facilities = response.data.facilities
      } catch (e) {
        this.error = 'Gagal memuat daftar fasilitas'
      }
    },

    async fetchCategories() {
      try {
        const response = await apiClient.get('/categories')
        this.categories = response.data.categories
      } catch (e) {
        this.error = 'Gagal memuat kategori'
      }
    },

    async fetchBookings() {
      this.loading = true
      try {
        const response = await apiClient.get('/bookings')
        this.bookings = response.data.bookings
      } catch (e) {
        this.error = 'Gagal memuat riwayat peminjaman'
      } finally {
        this.loading = false
      }
    },

    async createBooking(data) {
      this.loading = true
      try {
        const response = await apiClient.post('/bookings', data)
        this.bookings.unshift(response.data.booking)
        return response.data
      } catch (e) {
        this.error = e.response?.data?.message || 'Gagal membuat peminjaman'
        throw e
      } finally {
        this.loading = false
      }
    },

    async updateBooking(id, data) {
      try {
        const response = await apiClient.put(`/bookings/${id}`, data)
        const index = this.bookings.findIndex(b => b.id === id)
        if (index !== -1) this.bookings[index] = response.data.booking
        return response.data
      } catch (e) {
        this.error = e.response?.data?.message || 'Gagal memperbarui peminjaman'
        throw e
      }
    },

    async deleteBooking(id) {
      try {
        await apiClient.delete(`/bookings/${id}`)
        this.bookings = this.bookings.filter(b => b.id !== id)
      } catch (e) {
        this.error = e.response?.data?.message || 'Gagal menghapus peminjaman'
        throw e
      }
    },
  },
})