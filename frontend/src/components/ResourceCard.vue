<script setup>
import { RouterLink } from 'vue-router'
import { IconArrowRight } from '@tabler/icons-vue'
defineProps({
  resource: { type: Object, required: true },
})

function formatDate(value) {
  if (!value) return ''
  const d = new Date(value)
  return new Intl.DateTimeFormat('es-MX', {
    day: '2-digit', month: 'short', year: 'numeric',
  }).format(d)
}
</script>

<template>
  <article class="bg-white rounded-xl shadow-sm overflow-hidden flex flex-col">
    <img
      :src="resource.cover_url || 'https://picsum.photos/seed/fallback/800/450'"
      :alt="resource.title"
      class="w-full h-48 object-cover"
      loading="lazy"
    />

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
        class="inline-flex items-center gap-1 text-brand-teal font-semibold text-sm hover:text-brand-dark transition"
        >
        Leer recurso
        <IconArrowRight :size="16" />
      </RouterLink>
    </div>
  </article>
</template>