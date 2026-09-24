import { defineStore } from 'pinia'
import apiClient from '../services/api.js'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('auth_user')) || null,
    token: localStorage.getItem('auth_token') || null,
    loading: false,
    error: null,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    isAdmin: (state) => state.user?.role === 'admin',
    userName: (state) => state.user?.name || 'Sahabat PL Deltamas',
  },

  actions: {
    async login(email, password) {
      this.loading = true
      this.error = null
      try {
        const response = await apiClient.post('/login', { email, password })
        this.token = response.data.token
        this.user = response.data.user
        
        localStorage.setItem('auth_token', this.token)
        localStorage.setItem('auth_user', JSON.stringify(this.user))
        return response.data
      } catch (err) {
        this.error = err.response?.data?.message || 'Login gagal'
        throw err
      } finally {
        this.loading = false
      }
    },

    async register(name, email, password, password_confirmation, phone_number) {
      this.loading = true
      this.error = null
      try {
        const response = await apiClient.post('/register', {
          name,
          email,
          password,
          password_confirmation,
          phone_number,
        })
        this.token = response.data.token
        this.user = response.data.user
        
        localStorage.setItem('auth_token', this.token)
        localStorage.setItem('auth_user', JSON.stringify(this.user))
        return response.data
      } catch (err) {
        this.error = err.response?.data?.message || 'Registrasi gagal'
        throw err
      } finally {
        this.loading = false
      }
    },

    async logout() {
      try {
        if (this.token) {
          await apiClient.post('/logout')
        }
      } catch (e) {
        // Abaikan error saat logout
      } finally {
        this.token = null
        this.user = null
        localStorage.removeItem('auth_token')
        localStorage.removeItem('auth_user')
      }
    },
  },
})
