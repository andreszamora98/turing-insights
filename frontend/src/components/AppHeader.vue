<script setup>
import { RouterLink, useRouter } from 'vue-router'
import { IconBrain, IconLayoutDashboard, IconLogout, IconUser } from '@tabler/icons-vue'
import { useAuth } from '../composables/useAuth'

const router = useRouter()
const { isAuthenticated, isAdmin, user, logout } = useAuth()

async function onLogout() {
  await logout()
  router.push('/')
}
</script>

<template>
  <header class="bg-brand-dark text-slate-100">
    <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
      <RouterLink to="/" class="flex items-center gap-2">
        <IconBrain :size="26" class="text-brand-teal" />
        <span class="font-bold text-lg">TuringInsights</span>
      </RouterLink>

      <nav class="flex items-center gap-6 text-sm">
        <RouterLink to="/" class="hover:text-brand-teal transition">Inicio</RouterLink>
        <a href="/#recursos" class="hover:text-brand-teal transition">Recursos</a>
        <a href="/#temas" class="hover:text-brand-teal transition">Temas</a>

        <template v-if="isAuthenticated">
          <span class="inline-flex items-center gap-1.5 text-slate-300">
            <IconUser :size="16" />
            {{ user?.name }}
          </span>

          <RouterLink
            v-if="isAdmin"
            to="/admin"
            class="inline-flex items-center gap-1.5 bg-brand-teal text-brand-dark font-semibold px-3 py-1.5 rounded-md hover:bg-brand-teal-light transition"
          >
            <IconLayoutDashboard :size="16" />
            Dashboard
          </RouterLink>

          <button
            type="button"
            @click="onLogout"
            class="inline-flex items-center gap-1.5 text-slate-300 hover:text-brand-teal transition"
          >
            <IconLogout :size="16" />
            Cerrar sesión
          </button>
        </template>

        <RouterLink
          v-else
          to="/login"
          class="bg-brand-teal text-brand-dark font-semibold px-3 py-1.5 rounded-md hover:bg-brand-teal-light transition"
        >
          Iniciar sesión
        </RouterLink>
      </nav>
    </div>
  </header>
</template>