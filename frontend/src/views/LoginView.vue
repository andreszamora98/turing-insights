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

    <main class="relative flex-1 flex items-center justify-center overflow-hidden px-4 py-8 sm:py-16">
      <div
        class="login-float pointer-events-none absolute -top-24 -left-24 h-72 w-72 rounded-full bg-brand-teal/10 blur-3xl"
        aria-hidden="true"
      ></div>
      <div
        class="login-float login-float-delay pointer-events-none absolute -bottom-28 -right-20 h-80 w-80 rounded-full bg-brand-teal/10 blur-3xl"
        aria-hidden="true"
      ></div>

      <div class="login-card relative w-full max-w-md bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 p-5 sm:p-8">
        <h1 class="stagger text-xl sm:text-2xl font-bold text-brand-dark mb-1" style="--i: 0">Iniciar sesión</h1>
        <p class="stagger text-sm text-slate-500 mb-5 sm:mb-6" style="--i: 1">
          Accede para gestionar los recursos de TuringInsights.
        </p>

        <form @submit.prevent="onSubmit" class="space-y-4">
          <div class="stagger" style="--i: 2">
            <label class="block text-sm font-medium text-slate-700 mb-1">Correo</label>
            <div class="flex items-center border border-slate-300 hover:border-slate-400 rounded-lg px-3 focus-within:ring-2 focus-within:ring-brand-teal transition duration-200">
              <IconMail :size="18" class="text-slate-400 shrink-0" />
              <input
                v-model="email"
                type="email"
                required
                autocomplete="email"
                placeholder="admin@turing-ia.com"
                class="flex-1 min-w-0 px-2 py-2.5 bg-transparent focus:outline-none"
              />
            </div>
          </div>

          <div class="stagger" style="--i: 3">
            <label class="block text-sm font-medium text-slate-700 mb-1">Contraseña</label>
            <div class="flex items-center border border-slate-300 hover:border-slate-400 rounded-lg px-3 focus-within:ring-2 focus-within:ring-brand-teal transition duration-200">
              <IconLock :size="18" class="text-slate-400 shrink-0" />
              <input
                v-model="password"
                type="password"
                required
                autocomplete="current-password"
                placeholder="••••••••"
                class="flex-1 min-w-0 px-2 py-2.5 bg-transparent focus:outline-none"
              />
            </div>
          </div>

          <Transition name="alert">
            <div v-if="error" class="flex items-center gap-2 text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg px-3 py-2">
              <IconAlertCircle :size="16" class="shrink-0" />
              {{ error }}
            </div>
          </Transition>

          <button
            type="submit"
            :disabled="loading"
            class="stagger inline-flex items-center justify-center gap-2 w-full bg-brand-teal hover:bg-brand-teal-light text-brand-dark font-semibold py-2.5 rounded-lg hover:shadow-lg hover:shadow-brand-teal/20 active:scale-[0.98] transition duration-200 disabled:opacity-50 disabled:hover:shadow-none"
            style="--i: 4"
          >
            <span
              v-if="loading"
              class="h-4 w-4 rounded-full border-2 border-brand-dark/30 border-t-brand-dark animate-spin"
            ></span>
            {{ loading ? 'Ingresando...' : 'Ingresar' }}
          </button>
        </form>

        <div class="stagger mt-5 sm:mt-6 text-xs text-slate-500 border-t border-slate-200 pt-4 break-words" style="--i: 5">
          <p class="font-medium text-slate-600 mb-1">Credenciales de prueba</p>
          <p>Admin: admin@turing-ia.com / password</p>
          <p>User: user@turing-ia.com / password</p>
        </div>
      </div>
    </main>

    <AppFooter />
  </div>
</template>

<style scoped>
.login-card {
  animation: card-in 0.7s cubic-bezier(0.22, 1, 0.36, 1) both;
  animation-delay: 0.1s;
}

.stagger {
  animation: fade-up 0.6s cubic-bezier(0.22, 1, 0.36, 1) both;
  animation-delay: calc(var(--i, 0) * 80ms + 250ms);
}

.login-float {
  animation: float 10s ease-in-out infinite;
}

.login-float-delay {
  animation-delay: -5s;
}

.alert-enter-active {
  animation: alert-in 0.45s ease both;
}

.alert-leave-active {
  transition: opacity 0.2s ease;
}

.alert-leave-to {
  opacity: 0;
}

@keyframes card-in {
  from {
    opacity: 0;
    transform: translateY(28px) scale(0.97);
  }
  to {
    opacity: 1;
    transform: none;
  }
}

@keyframes fade-up {
  from {
    opacity: 0;
    transform: translateY(12px);
  }
  to {
    opacity: 1;
    transform: none;
  }
}

@keyframes float {
  0%,
  100% {
    transform: translate3d(0, 0, 0) scale(1);
  }
  50% {
    transform: translate3d(20px, -16px, 0) scale(1.08);
  }
}

@keyframes alert-in {
  0% {
    opacity: 0;
    transform: translateY(-6px);
  }
  30% {
    opacity: 1;
    transform: translateX(-5px);
  }
  50% {
    transform: translateX(5px);
  }
  70% {
    transform: translateX(-3px);
  }
  100% {
    transform: translateX(0);
  }
}
</style>