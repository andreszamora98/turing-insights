import { ref, computed } from 'vue'
import api from '../services/api'

const token = ref(localStorage.getItem('auth_token') || null)
const user = ref(JSON.parse(localStorage.getItem('auth_user') || 'null'))

export function useAuth() {
  const isAuthenticated = computed(() => !!token.value)
  const isAdmin = computed(() => user.value?.role === 'admin')

  async function login(email, password) {
    const { data } = await api.post('/login', { email, password })
    token.value = data.access_token
    user.value = data.user
    localStorage.setItem('auth_token', data.access_token)
    localStorage.setItem('auth_user', JSON.stringify(data.user))
    return data.user
  }

  async function logout() {
    try {
      await api.post('/logout')
    } catch (e) {
      // token expirado o invalido: se limpia igual
    }
    token.value = null
    user.value = null
    localStorage.removeItem('auth_token')
    localStorage.removeItem('auth_user')
  }

  async function fetchMe() {
    try {
      const { data } = await api.get('/me')
      user.value = data
      localStorage.setItem('auth_user', JSON.stringify(data))
      return data
    } catch (e) {
      token.value = null
      user.value = null
      localStorage.removeItem('auth_token')
      localStorage.removeItem('auth_user')
      return null
    }
  }

  return { token, user, isAuthenticated, isAdmin, login, logout, fetchMe }
}