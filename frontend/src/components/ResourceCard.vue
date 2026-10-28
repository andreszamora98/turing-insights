<script setup>
import { ref } from 'vue'
import { RouterLink } from 'vue-router'
import { IconArrowRight } from '@tabler/icons-vue'

defineProps({
  resource: { type: Object, required: true },
})

const loaded = ref(false)

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
        :src="resource.cover_url || 'https://picsum.photos/seed/fallback/800/450'"
        :alt="resource.title"
        class="w-full h-full object-cover transition duration-700 group-hover:scale-105"
        :class="loaded ? 'opacity-100' : 'opacity-0'"
        loading="lazy"
        @load="loaded = true"
        @error="loaded = true"
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
      >
        Leer recurso
        <IconArrowRight :size="16" class="transition-transform duration-300 group-hover:translate-x-1" />
      </RouterLink>
    </div>
  </article>
</template>