<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { IconMail, IconLock, IconAlertCircle } from '@tabler/icons-vue'
import { useAuth } from '../composables/useAuth'
import AppHeader from '../components/AppHeader.vue'
import AppFooter from '../components/AppFooter.vue'

const router = useRouter()
const { login } = useAuth()

const email = ref('')
const password = ref('')
const loading = ref(false)
const error = ref(null)

async function onSubmit() {
  error.value = null
  loading.value = true
  try {
    const user = await login(email.value, password.value)
    if (user.role === 'admin') {
      router.push('/admin')
    } else {
      router.push('/')
    }
  } catch (e) {
    error.value = e.response?.data?.message || 'No se pudo iniciar sesión'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="min-h-screen flex flex-col bg-slate-50">
    <AppHeader />

    <main class="flex-1 flex items-center justify-center px-4 py-16">
      <div class="w-full max-w-md bg-white rounded-2xl shadow-sm p-8">
        <h1 class="text-2xl font-bold text-brand-dark mb-1">Iniciar sesión</h1>
        <p class="text-sm text-slate-500 mb-6">
          Accede para gestionar los recursos de TuringInsights.
        </p>

        <form @submit.prevent="onSubmit" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Correo</label>
            <div class="flex items-center border border-slate-300 rounded-lg px-3 focus-within:ring-2 focus-within:ring-brand-teal">
              <IconMail :size="18" class="text-slate-400" />
              <input
                v-model="email"
                type="email"
                required
                autocomplete="email"
                placeholder="admin@turing-ia.com"
                class="flex-1 px-2 py-2.5 bg-transparent focus:outline-none"
              />
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Contraseña</label>
            <div class="flex items-center border border-slate-300 rounded-lg px-3 focus-within:ring-2 focus-within:ring-brand-teal">
              <IconLock :size="18" class="text-slate-400" />
              <input
                v-model="password"
                type="password"
                required
                autocomplete="current-password"
                placeholder="••••••••"
                class="flex-1 px-2 py-2.5 bg-transparent focus:outline-none"
              />
            </div>
          </div>

          <div v-if="error" class="flex items-center gap-2 text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg px-3 py-2">
            <IconAlertCircle :size="16" />
            {{ error }}
          </div>

          <button
            type="submit"
            :disabled="loading"
            class="w-full bg-brand-teal hover:bg-brand-teal-light text-brand-dark font-semibold py-2.5 rounded-lg transition disabled:opacity-50"
          >
            {{ loading ? 'Ingresando...' : 'Ingresar' }}
          </button>
        </form>

        <div class="mt-6 text-xs text-slate-500 border-t border-slate-200 pt-4">
          <p class="font-medium text-slate-600 mb-1">Credenciales de prueba</p>
          <p>Admin: admin@turing-ia.com / password</p>
          <p>User: user@turing-ia.com / password</p>
        </div>
      </div>
    </main>

    <AppFooter />
  </div>
</template>