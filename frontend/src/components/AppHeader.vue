<script setup>
import { ref, watch } from 'vue'
import { RouterLink, useRouter, useRoute } from 'vue-router'
import { IconBrain, IconLayoutDashboard, IconLogout, IconUser, IconMenu2, IconX } from '@tabler/icons-vue'
import { useAuth } from '../composables/useAuth'
import { useSectionNav } from '../composables/useSectionNav'

const router = useRouter()
const route = useRoute()
const { isAuthenticated, isAdmin, user, logout } = useAuth()
const { goHome } = useSectionNav()

const menuOpen = ref(false)

function closeMenu() {
  menuOpen.value = false
}

function onHome() {
  closeMenu()
  goHome()
}

watch(() => route.fullPath, closeMenu)

async function onLogout() {
  closeMenu()
  await logout()
  router.push('/')
}
</script>

<template>
  <header class="bg-brand-dark text-slate-100 sticky top-0 z-50">
    <div class="max-w-6xl mx-auto px-4 py-3 sm:py-4 flex items-center justify-between">
      <a href="/" class="group flex items-center gap-2" @click.prevent="onHome">
        <IconBrain
          :size="26"
          class="text-brand-teal transition-transform duration-500 group-hover:rotate-12 group-hover:scale-110"
        />
        <span class="font-bold text-lg">TuringInsights</span>
      </a>

      <nav class="hidden lg:flex items-center gap-6 text-sm">
        <template v-if="isAuthenticated">
          <span class="inline-flex items-center gap-1.5 text-slate-300">
            <IconUser :size="16" />
            {{ user?.name }}
          </span>

          <RouterLink
            v-if="isAdmin"
            to="/admin"
            class="inline-flex items-center gap-1.5 bg-brand-teal text-brand-dark font-semibold px-3 py-1.5 rounded-md hover:bg-brand-teal-light hover:-translate-y-0.5 hover:shadow-lg hover:shadow-brand-teal/20 active:scale-95 transition duration-200"
          >
            <IconLayoutDashboard :size="16" />
            Dashboard
          </RouterLink>

          <button
            type="button"
            @click="onLogout"
            class="inline-flex items-center gap-1.5 text-slate-300 hover:text-brand-teal active:scale-95 transition duration-200"
          >
            <IconLogout :size="16" />
            Cerrar sesión
          </button>
        </template>

        <RouterLink
          v-else
          to="/login"
          class="bg-brand-teal text-brand-dark font-semibold px-3 py-1.5 rounded-md hover:bg-brand-teal-light hover:-translate-y-0.5 hover:shadow-lg hover:shadow-brand-teal/20 active:scale-95 transition duration-200"
        >
          Iniciar sesión
        </RouterLink>
      </nav>

      <button
        type="button"
        class="lg:hidden p-2 -mr-2 rounded-md hover:bg-white/10 active:scale-90 transition duration-200"
        :aria-expanded="menuOpen"
        :aria-label="menuOpen ? 'Cerrar menú' : 'Abrir menú'"
        @click="menuOpen = !menuOpen"
      >
        <Transition name="icon" mode="out-in">
          <IconX v-if="menuOpen" key="close" :size="24" />
          <IconMenu2 v-else key="open" :size="24" />
        </Transition>
      </button>
    </div>

    <Transition name="menu">
      <nav
        v-if="menuOpen"
        class="lg:hidden border-t border-white/10 max-h-[70vh] overflow-y-auto"
      >
        <div class="max-w-6xl mx-auto px-4 py-3 flex flex-col gap-1 text-sm">
          <template v-if="isAuthenticated">
            <span
              class="menu-item inline-flex items-center gap-1.5 px-2 py-3 text-slate-300"
              style="--i: 0"
            >
              <IconUser :size="16" />
              {{ user?.name }}
            </span>

            <RouterLink
              v-if="isAdmin"
              to="/admin"
              class="menu-item inline-flex items-center justify-center gap-1.5 bg-brand-teal text-brand-dark font-semibold px-3 py-2.5 rounded-md hover:bg-brand-teal-light active:scale-95 transition"
              style="--i: 1"
              @click="closeMenu"
            >
              <IconLayoutDashboard :size="16" />
              Dashboard
            </RouterLink>

            <button
              type="button"
              @click="onLogout"
              class="menu-item inline-flex items-center gap-1.5 px-2 py-3 text-slate-300 hover:text-brand-teal transition"
              style="--i: 2"
            >
              <IconLogout :size="16" />
              Cerrar sesión
            </button>
          </template>

          <RouterLink
            v-else
            to="/login"
            class="menu-item text-center bg-brand-teal text-brand-dark font-semibold px-3 py-2.5 rounded-md hover:bg-brand-teal-light active:scale-95 transition"
            style="--i: 0"
            @click="closeMenu"
          >
            Iniciar sesión
          </RouterLink>
        </div>
      </nav>
    </Transition>
  </header>
</template>

<style scoped>
.icon-enter-active,
.icon-leave-active {
  transition: transform 0.2s ease, opacity 0.2s ease;
}

.icon-enter-from {
  opacity: 0;
  transform: rotate(-90deg) scale(0.8);
}

.icon-leave-to {
  opacity: 0;
  transform: rotate(90deg) scale(0.8);
}

.menu-enter-active,
.menu-leave-active {
  overflow: hidden;
  transition: max-height 0.35s cubic-bezier(0.22, 1, 0.36, 1), opacity 0.25s ease;
}

.menu-enter-from,
.menu-leave-to {
  max-height: 0;
  opacity: 0;
}

.menu-enter-to,
.menu-leave-from {
  max-height: 70vh;
  opacity: 1;
}

.menu-enter-active .menu-item {
  transition: opacity 0.35s ease, transform 0.35s cubic-bezier(0.22, 1, 0.36, 1);
  transition-delay: calc(var(--i, 0) * 45ms + 60ms);
}

.menu-enter-from .menu-item {
  opacity: 0;
  transform: translateX(-10px);
}
</style>