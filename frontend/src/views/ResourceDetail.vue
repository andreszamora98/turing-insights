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

    <main class="flex-1 max-w-4xl mx-auto px-4 py-12 w-full">
      <RouterLink
        to="/"
        class="inline-flex items-center gap-2 text-sm text-slate-600 hover:text-brand-dark transition mb-8"
      >
        <IconArrowLeft :size="18" />
        Volver al catálogo
      </RouterLink>

      <div v-if="loading" class="text-center py-16 text-slate-500">
        Cargando recurso...
      </div>

      <div v-else-if="error" class="text-center py-16">
        <p class="text-red-600 mb-6">{{ error }}</p>
        <RouterLink
          to="/"
          class="inline-block bg-brand-teal text-brand-dark font-semibold px-5 py-2.5 rounded-lg hover:bg-brand-teal-light transition"
        >
          Ir al catálogo
        </RouterLink>
      </div>

      <article v-else-if="resource">
        <img
          v-if="resource.cover_url"
          :src="resource.cover_url"
          :alt="resource.title"
          class="w-full h-72 object-cover rounded-xl mb-8"
        />

        <span class="inline-block text-xs font-semibold text-brand-teal bg-brand-teal/10 px-2 py-1 rounded mb-4">
          {{ resource.topic?.name }}
        </span>

        <h1 class="text-3xl md:text-4xl font-bold text-brand-dark mb-4">
          {{ resource.title }}
        </h1>

        <div class="flex flex-wrap items-center gap-4 text-sm text-slate-500 mb-8 pb-8 border-b border-slate-200">
          <span class="inline-flex items-center gap-1.5">
            <IconUser :size="16" />
            {{ resource.author?.name }}
          </span>
          <span class="inline-flex items-center gap-1.5">
            <IconCalendar :size="16" />
            {{ formatDate(resource.published_at) }}
          </span>
        </div>

        <p class="text-lg text-slate-700 mb-6 font-medium">
          {{ resource.excerpt }}
        </p>

        <div class="prose prose-slate max-w-none whitespace-pre-line text-slate-700 leading-relaxed">
          {{ resource.content }}
        </div>
      </article>
    </main>

    <AppFooter />
  </div>
</template>