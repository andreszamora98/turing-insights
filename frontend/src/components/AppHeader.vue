<script setup>
import { ref, watch } from 'vue'
import { RouterLink, useRouter, useRoute } from 'vue-router'
import { IconBrain, IconLayoutDashboard, IconLogout, IconUser, IconMenu2, IconX } from '@tabler/icons-vue'
import { useAuth } from '../composables/useAuth'

const router = useRouter()
const route = useRoute()
const { isAuthenticated, isAdmin, user, logout } = useAuth()

const menuOpen = ref(false)

function closeMenu() {
  menuOpen.value = false
}

watch(() => route.fullPath, closeMenu)

async function onLogout() {
  closeMenu()
  await logout()
  router.push('/')
}
</script>

<template>
  <header class="bg-brand-dark text-slate-100 relative">
    <div class="max-w-6xl mx-auto px-4 py-3 sm:py-4 flex items-center justify-between">
      <RouterLink to="/" class="flex items-center gap-2" @click="closeMenu">
        <IconBrain :size="26" class="text-brand-teal" />
        <span class="font-bold text-lg">TuringInsights</span>
      </RouterLink>

      <nav class="hidden lg:flex items-center gap-6 text-sm">
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

      <button
        type="button"
        class="lg:hidden p-2 -mr-2 rounded-md hover:bg-white/10 transition"
        :aria-expanded="menuOpen"
        :aria-label="menuOpen ? 'Cerrar menú' : 'Abrir menú'"
        @click="menuOpen = !menuOpen"
      >
        <IconX v-if="menuOpen" :size="24" />
        <IconMenu2 v-else :size="24" />
      </button>
    </div>

    <nav
      v-if="menuOpen"
      class="lg:hidden border-t border-white/10 max-h-[70vh] overflow-y-auto"
    >
      <div class="max-w-6xl mx-auto px-4 py-3 flex flex-col gap-1 text-sm">
        <RouterLink
          to="/"
          class="px-2 py-3 rounded-md hover:bg-white/5 hover:text-brand-teal transition"
          @click="closeMenu"
        >
          Inicio
        </RouterLink>
        <a
          href="/#recursos"
          class="px-2 py-3 rounded-md hover:bg-white/5 hover:text-brand-teal transition"
          @click="closeMenu"
        >
          Recursos
        </a>
        <a
          href="/#temas"
          class="px-2 py-3 rounded-md hover:bg-white/5 hover:text-brand-teal transition"
          @click="closeMenu"
        >
          Temas
        </a>

        <template v-if="isAuthenticated">
          <span class="inline-flex items-center gap-1.5 px-2 py-3 text-slate-300 border-t border-white/10 mt-1">
            <IconUser :size="16" />
            {{ user?.name }}
          </span>

          <RouterLink
            v-if="isAdmin"
            to="/admin"
            class="inline-flex items-center justify-center gap-1.5 bg-brand-teal text-brand-dark font-semibold px-3 py-2.5 rounded-md hover:bg-brand-teal-light transition"
            @click="closeMenu"
          >
            <IconLayoutDashboard :size="16" />
            Dashboard
          </RouterLink>

          <button
            type="button"
            @click="onLogout"
            class="inline-flex items-center gap-1.5 px-2 py-3 text-slate-300 hover:text-brand-teal transition"
          >
            <IconLogout :size="16" />
            Cerrar sesión
          </button>
        </template>

        <RouterLink
          v-else
          to="/login"
          class="text-center bg-brand-teal text-brand-dark font-semibold px-3 py-2.5 rounded-md hover:bg-brand-teal-light transition mt-1"
          @click="closeMenu"
        >
          Iniciar sesión
        </RouterLink>
      </div>
    </nav>
  </header>
</template>