<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import {
  IconPlus, IconEdit, IconTrash, IconSearch, IconAlertTriangle, IconCheck, IconX, IconArrowLeft,
  IconChevronLeft, IconChevronRight,
} from '@tabler/icons-vue'
import api from '../services/api'
import AppHeader from '../components/AppHeader.vue'
import AppFooter from '../components/AppFooter.vue'

const resources = ref([])
const topics = ref([])
const loading = ref(true)
const error = ref(null)
const search = ref('')
const toast = ref(null)

const currentPage = ref(1)
const lastPage = ref(1)
const total = ref(0)
const perPage = 3
const transitionKey = ref(0)

const showModal = ref(false)
const editing = ref(null)
const form = ref({ title: '', excerpt: '', content: '', cover_url: '', topic_id: '', published_at: '' })
const formErrors = ref({})
const saving = ref(false)

const showConfirm = ref(false)
const deleting = ref(null)
const deletingBusy = ref(false)

let toastTimer = null
let searchTimer = null

const pageNumbers = computed(() => {
  const t = lastPage.value
  const c = currentPage.value
  if (t <= 7) return Array.from({ length: t }, (_, i) => i + 1)
  const pages = [1]
  if (c > 3) pages.push('…')
  const start = Math.max(2, c - 1)
  const end = Math.min(t - 1, c + 1)
  for (let i = start; i <= end; i++) pages.push(i)
  if (c < t - 2) pages.push('…')
  pages.push(t)
  return pages
})

function pad(n) {
  return String(n).padStart(2, '0')
}

function toLocalInput(iso) {
  if (!iso) return ''
  const d = new Date(iso)
  return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`
}

function formatDate(iso) {
  if (!iso) return ''
  return new Intl.DateTimeFormat('es-MX', { day: '2-digit', month: 'short', year: 'numeric' }).format(new Date(iso))
}

function showToast(message, type = 'success') {
  toast.value = { message, type }
  if (toastTimer) clearTimeout(toastTimer)
  toastTimer = setTimeout(() => { toast.value = null }, 3500)
}

async function loadResources({ page, animate = false } = {}) {
  loading.value = true
  error.value = null
  try {
    const params = { per_page: perPage, page: page ?? currentPage.value }
    if (search.value) params.search = search.value
    const { data } = await api.get('/resources', { params })
    resources.value = data.data
    lastPage.value = data.last_page
    total.value = data.total
    if (page) currentPage.value = page
    if (animate) transitionKey.value++
  } catch (e) {
    error.value = e.response?.data?.message || e.message
  } finally {
    loading.value = false
  }
}

function goToPage(n) {
  if (loading.value) return
  if (n < 1 || n > lastPage.value || n === currentPage.value) return
  loadResources({ page: n, animate: true })
}

async function loadTopics() {
  try {
    const { data } = await api.get('/topics')
    topics.value = data.data
  } catch (e) {
    topics.value = []
  }
}

function openCreate() {
  editing.value = null
  form.value = {
    title: '',
    excerpt: '',
    content: '',
    cover_url: '',
    topic_id: topics.value[0]?.id || '',
    published_at: '',
  }
  formErrors.value = {}
  showModal.value = true
}

async function openEdit(resource) {
  try {
    const { data } = await api.get(`/resources/${resource.slug}`)
    const full = data.data
    editing.value = full
    form.value = {
      title: full.title,
      excerpt: full.excerpt,
      content: full.content,
      cover_url: full.cover_url || '',
      topic_id: full.topic_id,
      published_at: toLocalInput(full.published_at),
    }
    formErrors.value = {}
    showModal.value = true
  } catch (e) {
    showToast('No se pudo cargar el recurso', 'error')
  }
}

function closeModal() {
  showModal.value = false
  editing.value = null
}

async function save() {
  saving.value = true
  formErrors.value = {}
  try {
    const payload = {
      title: form.value.title,
      excerpt: form.value.excerpt,
      content: form.value.content,
      cover_url: form.value.cover_url || null,
      topic_id: Number(form.value.topic_id),
      published_at: form.value.published_at ? new Date(form.value.published_at).toISOString() : null,
    }

    if (editing.value) {
      await api.put(`/resources/${editing.value.id}`, payload)
      showToast('Recurso actualizado')
    } else {
      await api.post('/resources', payload)
      showToast('Recurso creado')
    }

    closeModal()
    await loadResources()
  } catch (e) {
    if (e.response?.status === 422) {
      formErrors.value = e.response.data.errors || {}
    } else {
      showToast(e.response?.data?.message || 'Error al guardar', 'error')
    }
  } finally {
    saving.value = false
  }
}

function confirmDelete(resource) {
  deleting.value = resource
  showConfirm.value = true
}

function cancelDelete() {
  deleting.value = null
  showConfirm.value = false
}

async function doDelete() {
  if (!deleting.value) return
  deletingBusy.value = true
  try {
    await api.delete(`/resources/${deleting.value.id}`)
    showToast('Recurso eliminado')
    cancelDelete()
    await loadResources()
    if (resources.value.length === 0 && currentPage.value > 1) {
      await loadResources({ page: currentPage.value - 1, animate: true })
    }
  } catch (e) {
    showToast(e.response?.data?.message || 'Error al eliminar', 'error')
  } finally {
    deletingBusy.value = false
  }
}

watch(search, () => {
  if (searchTimer) clearTimeout(searchTimer)
  searchTimer = setTimeout(() => {
    loadResources({ page: 1, animate: true })
  }, 350)
})

onMounted(async () => {
  await Promise.all([loadResources({ page: 1 }), loadTopics()])
})
</script>

<template>
  <div class="min-h-screen flex flex-col bg-slate-50">
    <AppHeader />

    <main class="flex-1 max-w-6xl mx-auto px-4 py-6 sm:py-10 w-full">
      <RouterLink
        to="/"
        class="group rise inline-flex items-center gap-2 text-sm text-slate-600 hover:text-brand-dark transition-colors duration-200 mb-4 sm:mb-6"
        style="--i: 0"
      >
        <IconArrowLeft :size="18" class="transition-transform duration-300 group-hover:-translate-x-1" />
        Volver al catálogo
      </RouterLink>

      <div
        class="rise flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6 sm:mb-8"
        style="--i: 1"
      >
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold text-brand-dark mb-1">Dashboard</h1>
          <p class="text-sm text-slate-500">
            Gestiona los recursos publicados en TuringInsights.
          </p>
        </div>
        <button
          type="button"
          @click="openCreate"
          class="inline-flex items-center justify-center gap-2 bg-brand-teal hover:bg-brand-teal-light text-brand-dark font-semibold px-4 py-2.5 rounded-lg hover:-translate-y-0.5 hover:shadow-lg hover:shadow-brand-teal/20 active:scale-95 transition duration-200"
        >
          <IconPlus :size="18" />
          Nuevo recurso
        </button>
      </div>

      <div
        class="rise bg-white rounded-xl shadow-sm hover:shadow-md focus-within:shadow-md transition-shadow duration-300 p-3 sm:p-4 mb-4 sm:mb-6 flex items-center gap-3"
        style="--i: 2"
      >
        <IconSearch :size="18" class="text-slate-400 shrink-0" />
        <input
          v-model="search"
          type="text"
          placeholder="Buscar por título o extracto..."
          class="flex-1 min-w-0 border-0 focus:outline-none bg-transparent"
        />
        <span class="text-xs text-slate-500 whitespace-nowrap">
          {{ resources.length }} de {{ total }}
        </span>
      </div>

      <div class="relative">
        <Transition name="fade">
          <div
            v-if="loading && resources.length > 0"
            class="absolute inset-x-0 top-0 h-1 bg-brand-teal/10 overflow-hidden rounded-t-xl z-20"
          >
            <div class="h-full w-1/3 bg-brand-teal progress-bar"></div>
          </div>
        </Transition>

        <Transition name="page-fade" mode="out-in">
          <div
            v-if="loading && resources.length === 0"
            key="skeleton"
            class="bg-white rounded-xl shadow-sm p-4 space-y-4 animate-pulse"
            aria-busy="true"
          >
            <div v-for="n in 5" :key="n" class="flex items-center gap-4">
              <div class="flex-1 space-y-2">
                <div class="h-4 w-1/2 bg-slate-200 rounded"></div>
                <div class="h-3 w-3/4 bg-slate-200 rounded"></div>
              </div>
              <div class="h-8 w-16 bg-slate-200 rounded"></div>
            </div>
          </div>

          <div v-else-if="error" key="error" class="text-center py-16 text-red-600">
            Error: {{ error }}
          </div>

          <div
            v-else-if="resources.length === 0"
            key="empty"
            class="text-center py-16 text-slate-500 bg-white rounded-xl"
          >
            No hay recursos que coincidan con la búsqueda.
          </div>

          <div
            v-else
            :key="`page-${transitionKey}`"
            class="bg-white rounded-xl shadow-sm overflow-hidden"
          >
            <div class="overflow-x-auto">
              <table class="min-w-full text-sm">
                <thead class="bg-slate-100 text-slate-600 text-left">
                  <tr>
                    <th class="px-3 sm:px-4 py-3 font-semibold">Título</th>
                    <th class="px-4 py-3 font-semibold hidden sm:table-cell">Tema</th>
                    <th class="px-4 py-3 font-semibold hidden md:table-cell">Autor</th>
                    <th class="px-4 py-3 font-semibold hidden lg:table-cell">Publicado</th>
                    <th class="px-3 sm:px-4 py-3 font-semibold text-right">Acciones</th>
                  </tr>
                </thead>
                <TransitionGroup tag="tbody" name="row" appear>
                  <tr
                    v-for="(r, i) in resources"
                    :key="r.id"
                    :style="{ '--i': Math.min(i, 10) }"
                    class="border-t border-slate-100 hover:bg-slate-50 transition-colors duration-200"
                  >
                    <td class="px-3 sm:px-4 py-3 min-w-[10rem]">
                      <p class="font-medium text-brand-dark">{{ r.title }}</p>
                      <p class="text-xs text-slate-500 line-clamp-1">{{ r.excerpt }}</p>
                      <p class="text-xs text-slate-400 mt-1 sm:hidden">{{ r.topic?.name }}</p>
                      <p class="text-xs text-slate-400 mt-1 lg:hidden">{{ formatDate(r.published_at) }}</p>
                    </td>
                    <td class="px-4 py-3 text-slate-700 hidden sm:table-cell">{{ r.topic?.name }}</td>
                    <td class="px-4 py-3 text-slate-700 hidden md:table-cell">{{ r.author?.name }}</td>
                    <td class="px-4 py-3 text-slate-500 whitespace-nowrap hidden lg:table-cell">{{ formatDate(r.published_at) }}</td>
                    <td class="px-2 sm:px-4 py-3 text-right whitespace-nowrap">
                      <button
                        type="button"
                        @click="openEdit(r)"
                        class="inline-flex items-center gap-1 text-brand-teal hover:text-brand-dark hover:bg-brand-teal/10 active:scale-95 p-2 sm:px-2 sm:py-1 rounded transition duration-200"
                        title="Editar"
                        aria-label="Editar"
                      >
                        <IconEdit :size="18" />
                        <span class="hidden sm:inline">Editar</span>
                      </button>
                      <button
                        type="button"
                        @click="confirmDelete(r)"
                        class="inline-flex items-center gap-1 text-red-600 hover:text-red-800 hover:bg-red-50 active:scale-95 p-2 sm:px-2 sm:py-1 rounded transition duration-200 sm:ml-1"
                        title="Eliminar"
                        aria-label="Eliminar"
                      >
                        <IconTrash :size="18" />
                        <span class="hidden sm:inline">Eliminar</span>
                      </button>
                    </td>
                  </tr>
                </TransitionGroup>
              </table>
            </div>
          </div>
        </Transition>
      </div>

      <Transition name="pager">
        <nav
          v-if="lastPage > 1 && !error"
          class="mt-4 sm:mt-6 flex items-center justify-center gap-1 flex-wrap"
          aria-label="Paginación"
        >
          <button
            type="button"
            @click="goToPage(currentPage - 1)"
            :disabled="currentPage === 1 || loading"
            class="pager-btn"
            aria-label="Página anterior"
          >
            <IconChevronLeft :size="16" />
          </button>

          <template v-for="(p, i) in pageNumbers" :key="i">
            <span v-if="p === '…'" class="px-2 py-1.5 text-slate-400 select-none">…</span>
            <button
              v-else
              type="button"
              @click="goToPage(p)"
              :disabled="loading"
              class="pager-btn"
              :class="p === currentPage
                ? 'bg-brand-teal text-brand-dark font-semibold shadow-sm shadow-brand-teal/30'
                : 'text-slate-600 hover:text-brand-dark hover:bg-slate-100'"
              :aria-current="p === currentPage ? 'page' : undefined"
            >
              {{ p }}
            </button>
          </template>

          <button
            type="button"
            @click="goToPage(currentPage + 1)"
            :disabled="currentPage === lastPage || loading"
            class="pager-btn"
            aria-label="Página siguiente"
          >
            <IconChevronRight :size="16" />
          </button>
        </nav>
      </Transition>
    </main>

    <AppFooter />

    <Transition name="modal">
      <div
        v-if="showModal"
        class="fixed inset-0 bg-black/50 flex items-center justify-center p-2 sm:p-4 z-[60]"
        @click.self="closeModal"
      >
        <div class="modal-panel bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[95vh] sm:max-h-[90vh] overflow-y-auto">
          <div class="flex items-center justify-between px-4 sm:px-6 py-3 sm:py-4 border-b border-slate-200 sticky top-0 bg-white z-10">
            <h2 class="text-lg font-bold text-brand-dark">
              {{ editing ? 'Editar recurso' : 'Nuevo recurso' }}
            </h2>
            <button
              type="button"
              @click="closeModal"
              class="text-slate-500 hover:text-slate-800 hover:rotate-90 p-1 transition duration-200"
              aria-label="Cerrar"
            >
              <IconX :size="20" />
            </button>
          </div>

          <form @submit.prevent="save" class="p-4 sm:p-6 space-y-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Título</label>
              <input
                v-model="form.title"
                type="text"
                class="w-full border border-slate-300 hover:border-slate-400 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-teal transition duration-200"
              />
              <p v-if="formErrors.title" class="text-xs text-red-600 mt-1">{{ formErrors.title[0] }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Extracto</label>
              <textarea
                v-model="form.excerpt"
                rows="2"
                maxlength="500"
                class="w-full border border-slate-300 hover:border-slate-400 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-teal transition duration-200"
              ></textarea>
              <p v-if="formErrors.excerpt" class="text-xs text-red-600 mt-1">{{ formErrors.excerpt[0] }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Contenido</label>
              <textarea
                v-model="form.content"
                rows="6"
                class="w-full border border-slate-300 hover:border-slate-400 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-teal transition duration-200"
              ></textarea>
              <p v-if="formErrors.content" class="text-xs text-red-600 mt-1">{{ formErrors.content[0] }}</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tema</label>
                <select
                  v-model="form.topic_id"
                  class="w-full border border-slate-300 hover:border-slate-400 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-teal transition duration-200"
                >
                  <option disabled value="">Selecciona un tema</option>
                  <option v-for="t in topics" :key="t.id" :value="t.id">{{ t.name }}</option>
                </select>
                <p v-if="formErrors.topic_id" class="text-xs text-red-600 mt-1">{{ formErrors.topic_id[0] }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Fecha de publicación</label>
                <input
                  v-model="form.published_at"
                  type="datetime-local"
                  class="w-full min-w-0 border border-slate-300 hover:border-slate-400 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-teal transition duration-200"
                />
                <p v-if="formErrors.published_at" class="text-xs text-red-600 mt-1">{{ formErrors.published_at[0] }}</p>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">URL de portada</label>
              <input
                v-model="form.cover_url"
                type="url"
                class="w-full border border-slate-300 hover:border-slate-400 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-teal transition duration-200"
              />
              <p v-if="formErrors.cover_url" class="text-xs text-red-600 mt-1">{{ formErrors.cover_url[0] }}</p>
            </div>

            <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3 pt-4 border-t border-slate-200">
              <button
                type="button"
                @click="closeModal"
                class="px-4 py-2 text-slate-600 hover:text-brand-dark transition-colors duration-200"
              >
                Cancelar
              </button>
              <button
                type="submit"
                :disabled="saving"
                class="inline-flex items-center justify-center gap-2 bg-brand-teal hover:bg-brand-teal-light text-brand-dark font-semibold px-5 py-2 rounded-lg hover:shadow-lg hover:shadow-brand-teal/20 active:scale-95 transition duration-200 disabled:opacity-50 disabled:hover:shadow-none"
              >
                <span
                  v-if="saving"
                  class="h-4 w-4 rounded-full border-2 border-brand-dark/30 border-t-brand-dark animate-spin"
                ></span>
                <IconCheck v-else :size="18" />
                {{ saving ? 'Guardando...' : 'Guardar' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Transition>

    <Transition name="modal">
      <div
        v-if="showConfirm"
        class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-[70]"
        @click.self="cancelDelete"
      >
        <div class="modal-panel bg-white rounded-xl shadow-xl w-full max-w-sm max-h-[95vh] overflow-y-auto p-5 sm:p-6 text-center">
          <div class="inline-flex items-center justify-center w-12 h-12 bg-red-100 text-red-600 rounded-full mb-4">
            <IconAlertTriangle :size="24" />
          </div>
          <h3 class="text-lg font-bold text-brand-dark mb-2">Eliminar recurso</h3>
          <p class="text-sm text-slate-600 mb-6 break-words">
            ¿Seguro que deseas eliminar <strong>{{ deleting?.title }}</strong>? Esta acción no se puede deshacer.
          </p>
          <div class="flex flex-col-reverse sm:flex-row sm:justify-center gap-3">
            <button
              type="button"
              @click="cancelDelete"
              class="px-4 py-2 text-slate-600 hover:text-brand-dark transition-colors duration-200"
            >
              Cancelar
            </button>
            <button
              type="button"
              @click="doDelete"
              :disabled="deletingBusy"
              class="inline-flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-lg hover:shadow-lg hover:shadow-red-600/20 active:scale-95 transition duration-200 disabled:opacity-50 disabled:hover:shadow-none"
            >
              <span
                v-if="deletingBusy"
                class="h-4 w-4 rounded-full border-2 border-white/40 border-t-white animate-spin"
              ></span>
              <IconTrash v-else :size="18" />
              {{ deletingBusy ? 'Eliminando...' : 'Eliminar' }}
            </button>
          </div>
        </div>
      </div>
    </Transition>

    <Transition name="toast">
      <div
        v-if="toast"
        class="fixed bottom-4 left-4 right-4 sm:left-auto sm:bottom-6 sm:right-6 z-[80] px-4 py-3 rounded-lg shadow-lg text-sm font-medium text-center sm:text-left"
        :class="toast.type === 'error' ? 'bg-red-600 text-white' : 'bg-brand-dark text-slate-100'"
      >
        {{ toast.message }}
      </div>
    </Transition>
  </div>
</template>

<style scoped>
.rise {
  animation: rise 0.6s cubic-bezier(0.22, 1, 0.36, 1) both;
  animation-delay: calc(var(--i, 0) * 70ms);
}

.fade-in {
  animation: fade-in 0.4s ease both;
}

.row-enter-active {
  transition: opacity 0.35s ease;
  transition-delay: calc(var(--i, 0) * 30ms);
}

.row-leave-active {
  transition: opacity 0.25s ease;
}

.row-enter-from,
.row-leave-to {
  opacity: 0;
}

.page-fade-enter-active {
  transition: opacity 0.35s ease, transform 0.35s cubic-bezier(0.22, 1, 0.36, 1);
}

.page-fade-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}

.page-fade-enter-from {
  opacity: 0;
  transform: translateY(10px);
}

.page-fade-leave-to {
  opacity: 0;
  transform: translateY(-6px);
}

.pager-enter-active,
.pager-leave-active {
  transition: opacity 0.3s ease, transform 0.3s cubic-bezier(0.22, 1, 0.36, 1);
}

.pager-enter-from,
.pager-leave-to {
  opacity: 0;
  transform: translateY(8px);
}

.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-active .modal-panel,
.modal-leave-active .modal-panel {
  transition: transform 0.3s cubic-bezier(0.22, 1, 0.36, 1), opacity 0.3s ease;
}

.modal-enter-from .modal-panel,
.modal-leave-to .modal-panel {
  opacity: 0;
  transform: translateY(18px) scale(0.97);
}

.toast-enter-active,
.toast-leave-active {
  transition: opacity 0.3s ease, transform 0.3s cubic-bezier(0.22, 1, 0.36, 1);
}

.toast-enter-from,
.toast-leave-to {
  opacity: 0;
  transform: translateY(14px);
}

.progress-bar {
  animation: progress-bar 1s linear infinite;
}

@keyframes progress-bar {
  0% { transform: translateX(-100%); }
  100% { transform: translateX(400%); }
}

@keyframes rise {
  from {
    opacity: 0;
    transform: translateY(12px);
  }
  to {
    opacity: 1;
    transform: none;
  }
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

<style>
.pager-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 2rem;
  height: 2rem;
  padding: 0 0.5rem;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  transition: background-color 0.2s ease, color 0.2s ease, transform 0.15s ease, box-shadow 0.2s ease;
}

.pager-btn:not(:disabled):active {
  transform: scale(0.92);
}

.pager-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}
</style>