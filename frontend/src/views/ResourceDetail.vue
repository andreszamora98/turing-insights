<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, RouterLink } from 'vue-router'
import { IconArrowLeft, IconCalendar, IconUser } from '@tabler/icons-vue'
import api from '../services/api'
import AppHeader from '../components/AppHeader.vue'
import AppFooter from '../components/AppFooter.vue'

const route = useRoute()
const resource = ref(null)
const loading = ref(true)
const error = ref(null)
const imageFailed = ref(false)

const fallback = '/images/fallback-cover.jpg'

function resolveCover() {
  if (imageFailed.value) return fallback
  return resource.value?.cover_url || fallback
}

function formatDate(value) {
  if (!value) return ''
  const d = new Date(value)
  return new Intl.DateTimeFormat('es-MX', {
    day: '2-digit', month: 'long', year: 'numeric',
  }).format(d)
}

onMounted(async () => {
  try {
    const { data } = await api.get(`/resources/${route.params.slug}`)
    resource.value = data.data
  } catch (e) {
    if (e.response?.status === 404) {
      error.value = 'El recurso no existe o no está publicado.'
    } else {
      error.value = e.response?.data?.message || e.message
    }
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div class="min-h-screen flex flex-col bg-slate-50">
    <AppHeader />

    <main class="flex-1 max-w-4xl mx-auto px-4 py-8 sm:py-12 w-full">
      <RouterLink
        to="/"
        class="group rise inline-flex items-center gap-2 text-sm text-slate-600 hover:text-brand-dark transition-colors duration-200 mb-6 sm:mb-8"
        style="--i: 0"
      >
        <IconArrowLeft :size="18" class="transition-transform duration-300 group-hover:-translate-x-1" />
        Volver al catálogo
      </RouterLink>

      <div v-if="loading" class="animate-pulse" aria-busy="true">
        <div class="w-full h-44 sm:h-60 md:h-72 bg-slate-200 rounded-xl mb-6 sm:mb-8"></div>
        <div class="h-5 w-24 bg-slate-200 rounded mb-4"></div>
        <div class="h-8 w-3/4 bg-slate-200 rounded mb-4"></div>
        <div class="h-4 w-1/2 bg-slate-200 rounded mb-8"></div>
        <div class="space-y-3">
          <div class="h-4 w-full bg-slate-200 rounded"></div>
          <div class="h-4 w-full bg-slate-200 rounded"></div>
          <div class="h-4 w-5/6 bg-slate-200 rounded"></div>
        </div>
      </div>

      <div v-else-if="error" class="rise text-center py-16" style="--i: 1">
        <p class="text-red-600 mb-6">{{ error }}</p>
        <RouterLink
          to="/"
          class="inline-block bg-brand-teal text-brand-dark font-semibold px-5 py-2.5 rounded-lg hover:bg-brand-teal-light hover:-translate-y-0.5 hover:shadow-lg hover:shadow-brand-teal/20 active:scale-95 transition duration-200"
        >
          Ir al catálogo
        </RouterLink>
      </div>

      <article v-else-if="resource" class="min-w-0">
        <div
          v-if="resource.cover_url"
          class="rise w-full h-44 sm:h-60 md:h-72 overflow-hidden rounded-xl mb-6 sm:mb-8 bg-slate-200"
          :class="{ 'animate-pulse': !coverLoaded }"
          style="--i: 1"
        >
          <img
            :src="resolveCover()"
            :alt="resource.title"
            class="w-full h-44 sm:h-60 md:h-72 object-cover rounded-xl mb-8"
            @error="imageFailed = true"
          />
        </div>

        <span
          class="rise inline-block text-xs font-semibold text-brand-teal bg-brand-teal/10 px-2 py-1 rounded mb-3 sm:mb-4"
          style="--i: 2"
        >
          {{ resource.topic?.name }}
        </span>

        <h1
          class="rise text-2xl sm:text-3xl md:text-4xl font-bold text-brand-dark mb-4 break-words"
          style="--i: 3"
        >
          {{ resource.title }}
        </h1>

        <div
          class="rise flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-slate-500 mb-6 sm:mb-8 pb-6 sm:pb-8 border-b border-slate-200"
          style="--i: 4"
        >
          <span class="inline-flex items-center gap-1.5">
            <IconUser :size="16" />
            {{ resource.author?.name }}
          </span>
          <span class="inline-flex items-center gap-1.5">
            <IconCalendar :size="16" />
            {{ formatDate(resource.published_at) }}
          </span>
        </div>

        <p
          class="rise text-base sm:text-lg text-slate-700 mb-6 font-medium break-words"
          style="--i: 5"
        >
          {{ resource.excerpt }}
        </p>

        <div
          class="rise prose prose-slate max-w-none whitespace-pre-line break-words text-slate-700 leading-relaxed"
          style="--i: 6"
        >
          {{ resource.content }}
        </div>
      </article>
    </main>

    <AppFooter />
  </div>
</template>

<style scoped>
.rise {
  animation: rise 0.65s cubic-bezier(0.22, 1, 0.36, 1) both;
  animation-delay: calc(var(--i, 0) * 70ms);
}

@keyframes rise {
  from {
    opacity: 0;
    transform: translateY(14px);
  }
  to {
    opacity: 1;
    transform: none;
  }
}
</style>