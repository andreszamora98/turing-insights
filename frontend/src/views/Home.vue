<script setup>
import { ref, computed, onMounted } from 'vue'
import { IconWorld, IconDatabase, IconSettings } from '@tabler/icons-vue'
import api from '../services/api'
import AppHeader from '../components/AppHeader.vue'
import AppHero from '../components/AppHero.vue'
import TopicCards from '../components/TopicCards.vue'
import FilterBar from '../components/FilterBar.vue'
import ResourceCard from '../components/ResourceCard.vue'
import InfoSection from '../components/InfoSection.vue'
import AppFooter from '../components/AppFooter.vue'
import RevealOnScroll from '../components/RevealOnScroll.vue'

const topics = [
  { name: 'Desarrollo web', slug: 'desarrollo-web', icon: IconWorld },
  { name: 'Bases de datos', slug: 'bases-de-datos', icon: IconDatabase },
  { name: 'Backend', slug: 'backend', icon: IconSettings },
]

const resources = ref([])
const loading = ref(false)
const error = ref(null)
const currentPage = ref(1)
const lastPage = ref(1)

const filters = ref({ search: '', topic: '', sort: 'recent' })

const hasMore = computed(() => currentPage.value < lastPage.value)
const isFiltered = computed(() => filters.value.search !== '' || filters.value.topic !== '')

async function fetchResources({ reset = false } = {}) {
  loading.value = true
  error.value = null

  if (reset) {
    currentPage.value = 1
    resources.value = []
  }

  try {
    const params = {
      page: currentPage.value,
      per_page: 6,
      sort: filters.value.sort,
    }
    if (filters.value.search) params.search = filters.value.search
    if (filters.value.topic) params.topic = filters.value.topic

    const { data } = await api.get('/resources', { params })

    resources.value = reset ? data.data : [...resources.value, ...data.data]
    lastPage.value = data.last_page
  } catch (e) {
    error.value = e.response?.data?.message || e.message
  } finally {
    loading.value = false
  }
}

function applyFilters() {
  fetchResources({ reset: true })
}

function selectTopic(slug) {
  filters.value.topic = filters.value.topic === slug ? '' : slug
  applyFilters()
}

function setFilters(newFilters) {
  filters.value = newFilters
}

function clearFilters() {
  filters.value = { search: '', topic: '', sort: 'recent' }
  applyFilters()
}

function loadMore() {
  currentPage.value++
  fetchResources()
}

onMounted(() => fetchResources({ reset: true }))
</script>

<template>
  <div class="min-h-screen flex flex-col bg-slate-50">
    <AppHeader />
    <AppHero />

    <main class="flex-1">
      <section class="max-w-6xl mx-auto px-4 py-8 sm:py-12">
        <RevealOnScroll>
          <h2 class="text-xl sm:text-2xl font-bold text-brand-dark mb-4 sm:mb-6">Explora por tema</h2>
        </RevealOnScroll>
        <TopicCards :topics="topics" :active="filters.topic" @select="selectTopic" />
      </section>

      <section id="recursos" class="max-w-6xl mx-auto px-4 pb-6 sm:pb-8 scroll-mt-24">
        <RevealOnScroll>
          <FilterBar
            :filters="filters"
            :topics="topics"
            :is-filtered="isFiltered"
            @update:filters="setFilters"
            @apply="applyFilters"
            @clear="clearFilters"
          />
        </RevealOnScroll>
      </section>

      <section class="max-w-6xl mx-auto px-4 pb-10 sm:pb-16">
        <RevealOnScroll>
          <h2 class="text-xl sm:text-2xl font-bold text-brand-dark mb-4 sm:mb-6">Recursos recientes</h2>
        </RevealOnScroll>

        <div
          v-if="loading && resources.length === 0"
          class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6"
          aria-busy="true"
        >
          <div
            v-for="n in 6"
            :key="n"
            class="bg-white rounded-xl shadow-sm overflow-hidden animate-pulse"
          >
            <div class="h-48 bg-slate-200"></div>
            <div class="p-5 space-y-3">
              <div class="h-4 w-20 bg-slate-200 rounded"></div>
              <div class="h-5 w-3/4 bg-slate-200 rounded"></div>
              <div class="h-3 w-full bg-slate-200 rounded"></div>
              <div class="h-3 w-5/6 bg-slate-200 rounded"></div>
              <div class="h-3 w-1/3 bg-slate-200 rounded"></div>
            </div>
          </div>
        </div>

        <div v-else-if="error && resources.length === 0" class="fade-in text-center py-12 text-red-600">
          Error: {{ error }}
        </div>

        <div v-else-if="resources.length === 0" class="fade-in text-center py-12 text-slate-500">
          No se encontraron recursos con esos filtros.
        </div>

        <TransitionGroup
          v-else
          tag="div"
          name="card"
          appear
          class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6"
        >
          <ResourceCard
            v-for="(r, i) in resources"
            :key="r.id"
            :resource="r"
            :style="{ '--i': i % 6 }"
          />
        </TransitionGroup>

        <div v-if="hasMore" class="mt-8 sm:mt-10 text-center">
          <button
            type="button"
            @click="loadMore"
            :disabled="loading"
            class="inline-flex items-center justify-center gap-2 w-full sm:w-auto bg-brand-teal hover:bg-brand-teal-light text-brand-dark font-semibold px-6 py-3 rounded-lg hover:-translate-y-0.5 hover:shadow-lg hover:shadow-brand-teal/20 active:scale-95 transition duration-200 disabled:opacity-50 disabled:hover:translate-y-0 disabled:hover:shadow-none"
          >
            <span
              v-if="loading"
              class="h-4 w-4 rounded-full border-2 border-brand-dark/30 border-t-brand-dark animate-spin"
            ></span>
            {{ loading ? 'Cargando...' : 'Cargar más recursos' }}
          </button>
        </div>
      </section>

      <InfoSection />
    </main>

    <AppFooter />
  </div>
</template>

<style scoped>
.card-enter-active {
  transition: opacity 0.55s ease, transform 0.55s cubic-bezier(0.22, 1, 0.36, 1);
  transition-delay: calc(var(--i, 0) * 70ms);
}

.card-enter-from {
  opacity: 0;
  transform: translateY(18px) scale(0.98);
}

.fade-in {
  animation: fade-in 0.4s ease both;
}

@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(6px);
  }
  to {
    opacity: 1;
    transform: none;
  }
}
</style>