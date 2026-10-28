<script setup>
import { ref } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { IconArrowRight, IconLock } from '@tabler/icons-vue'
import { useAuth } from '../composables/useAuth'

defineProps({
  resource: { type: Object, required: true },
})

const router = useRouter()
const { isAuthenticated } = useAuth()
const loaded = ref(false)
const showLoginNotice = ref(false)
const imageFailed = ref(false)

const fallback = '/images/fallback-cover.jpg'

function resolveCover(resource) {
  if (imageFailed.value) return fallback
  return resource.cover_url || fallback
}

function onReadClick(event) {
  if (isAuthenticated.value) return
  event.preventDefault()
  showLoginNotice.value = true
}

function goToLogin() {
  showLoginNotice.value = false
  router.push({ name: 'login', query: { redirect: router.currentRoute.value.fullPath } })
}

function closeNotice() {
  showLoginNotice.value = false
}

function formatDate(value) {
  if (!value) return ''
  const d = new Date(value)
  return new Intl.DateTimeFormat('es-MX', {
    day: '2-digit', month: 'short', year: 'numeric',
  }).format(d)
}
</script>

<template>
  <article class="group bg-white rounded-xl shadow-sm overflow-hidden flex flex-col transition duration-300 hover:-translate-y-1 hover:shadow-lg">
    <div class="h-48 overflow-hidden bg-slate-200" :class="{ 'animate-pulse': !loaded }">
      <img
        :src="resolveCover(resource)"
        :alt="resource.title"
        class="w-full h-full object-cover transition duration-700 group-hover:scale-105"
        :class="loaded ? 'opacity-100' : 'opacity-0'"
        loading="lazy"
        @load="loaded = true"
        @error="imageFailed = true; loaded = true"
      />
    </div>

    <div class="p-5 flex flex-col flex-1">
      <span class="inline-block text-xs font-semibold text-brand-teal bg-brand-teal/10 px-2 py-1 rounded self-start mb-3">
        {{ resource.topic?.name }}
      </span>

      <h3 class="text-lg font-bold text-brand-dark mb-2 line-clamp-2">
        {{ resource.title }}
      </h3>

      <p class="text-sm text-slate-600 mb-4 line-clamp-3 flex-1">
        {{ resource.excerpt }}
      </p>

      <div class="text-xs text-slate-500 mb-4">
        {{ resource.author?.name }} · {{ formatDate(resource.published_at) }}
      </div>

      <RouterLink
        :to="{ name: 'resource-detail', params: { slug: resource.slug } }"
        class="inline-flex items-center gap-1 text-brand-teal font-semibold text-sm hover:text-brand-dark transition-colors duration-200"
        @click="onReadClick"
      >
        Leer recurso
        <IconArrowRight :size="16" class="transition-transform duration-300 group-hover:translate-x-1" />
      </RouterLink>
    </div>

    <Transition name="modal">
      <div
        v-if="showLoginNotice"
        class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50"
        @click.self="closeNotice"
      >
        <div class="bg-white rounded-xl shadow-xl w-full max-w-sm p-6 text-center">
          <div class="inline-flex items-center justify-center w-12 h-12 bg-brand-teal/10 text-brand-teal rounded-full mb-4">
            <IconLock :size="24" />
          </div>
          <h3 class="text-lg font-bold text-brand-dark mb-2">Inicia sesión para leer</h3>
          <p class="text-sm text-slate-600 mb-6">
            El contenido completo de los recursos está disponible para usuarios registrados.
          </p>
          <div class="flex flex-col-reverse sm:flex-row sm:justify-center gap-3">
            <button
              type="button"
              @click="closeNotice"
              class="px-4 py-2 text-slate-600 hover:text-brand-dark transition"
            >
              Cancelar
            </button>
            <button
              type="button"
              @click="goToLogin"
              class="inline-flex items-center justify-center gap-2 bg-brand-teal hover:bg-brand-teal-light text-brand-dark font-semibold px-4 py-2 rounded-lg transition"
            >
              Iniciar sesión
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </article>
</template>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.25s ease;
}
.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
</style>