<script setup>
import { ref, computed, onMounted } from 'vue'
import {
  IconPlus, IconEdit, IconTrash, IconSearch, IconAlertTriangle, IconCheck, IconX, IconArrowLeft,
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

const showModal = ref(false)
const editing = ref(null)
const form = ref({ title: '', excerpt: '', content: '', cover_url: '', topic_id: '', published_at: '' })
const formErrors = ref({})
const saving = ref(false)

const showConfirm = ref(false)
const deleting = ref(null)
const deletingBusy = ref(false)

const filtered = computed(() => {
  if (!search.value) return resources.value
  const q = search.value.toLowerCase()
  return resources.value.filter(
    (r) => r.title.toLowerCase().includes(q) || (r.excerpt || '').toLowerCase().includes(q)
  )
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
  setTimeout(() => { toast.value = null }, 3500)
}

async function loadResources() {
  loading.value = true
  error.value = null
  try {
    const { data } = await api.get('/resources', { params: { per_page: 50, sort: 'recent' } })
    resources.value = data.data
  } catch (e) {
    error.value = e.response?.data?.message || e.message
  } finally {
    loading.value = false
  }
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
  } catch (e) {
    showToast(e.response?.data?.message || 'Error al eliminar', 'error')
  } finally {
    deletingBusy.value = false
  }
}

onMounted(async () => {
  await Promise.all([loadResources(), loadTopics()])
})
</script>

<template>
  <div class="min-h-screen flex flex-col bg-slate-50">
    <AppHeader />

    <main class="flex-1 max-w-6xl mx-auto px-4 py-6 sm:py-10 w-full">
      <RouterLink
        to="/"
        class="inline-flex items-center gap-2 text-sm text-slate-600 hover:text-brand-dark transition mb-4 sm:mb-6"
      >
        <IconArrowLeft :size="18" />
        Volver al catálogo
      </RouterLink>

      <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6 sm:mb-8">
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold text-brand-dark mb-1">Dashboard</h1>
          <p class="text-sm text-slate-500">
            Gestiona los recursos publicados en TuringInsights.
          </p>
        </div>
        <button
          type="button"
          @click="openCreate"
          class="inline-flex items-center justify-center gap-2 bg-brand-teal hover:bg-brand-teal-light text-brand-dark font-semibold px-4 py-2.5 rounded-lg transition"
        >
          <IconPlus :size="18" />
          Nuevo recurso
        </button>
      </div>

      <div class="bg-white rounded-xl shadow-sm p-3 sm:p-4 mb-4 sm:mb-6 flex items-center gap-3">
        <IconSearch :size="18" class="text-slate-400 shrink-0" />
        <input
          v-model="search"
          type="text"
          placeholder="Buscar por título o extracto..."
          class="flex-1 min-w-0 border-0 focus:outline-none bg-transparent"
        />
        <span class="text-xs text-slate-500 whitespace-nowrap">
          {{ filtered.length }} de {{ resources.length }}
        </span>
      </div>

      <div v-if="loading" class="text-center py-16 text-slate-500">
        Cargando recursos...
      </div>

      <div v-else-if="error" class="text-center py-16 text-red-600">
        Error: {{ error }}
      </div>

      <div v-else-if="filtered.length === 0" class="text-center py-16 text-slate-500 bg-white rounded-xl">
        No hay recursos que coincidan con la búsqueda.
      </div>

      <div v-else class="bg-white rounded-xl shadow-sm overflow-hidden">
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
            <tbody>
              <tr v-for="r in filtered" :key="r.id" class="border-t border-slate-100 hover:bg-slate-50">
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
                    class="inline-flex items-center gap-1 text-brand-teal hover:text-brand-dark p-2 sm:px-2 sm:py-1 rounded transition"
                    title="Editar"
                    aria-label="Editar"
                  >
                    <IconEdit :size="18" />
                    <span class="hidden sm:inline">Editar</span>
                  </button>
                  <button
                    type="button"
                    @click="confirmDelete(r)"
                    class="inline-flex items-center gap-1 text-red-600 hover:text-red-800 p-2 sm:px-2 sm:py-1 rounded transition sm:ml-1"
                    title="Eliminar"
                    aria-label="Eliminar"
                  >
                    <IconTrash :size="18" />
                    <span class="hidden sm:inline">Eliminar</span>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </main>

    <AppFooter />

    <div
      v-if="showModal"
      class="fixed inset-0 bg-black/50 flex items-center justify-center p-2 sm:p-4 z-40"
      @click.self="closeModal"
    >
      <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[95vh] sm:max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-4 sm:px-6 py-3 sm:py-4 border-b border-slate-200 sticky top-0 bg-white z-10">
          <h2 class="text-lg font-bold text-brand-dark">
            {{ editing ? 'Editar recurso' : 'Nuevo recurso' }}
          </h2>
          <button type="button" @click="closeModal" class="text-slate-500 hover:text-slate-800 p-1" aria-label="Cerrar">
            <IconX :size="20" />
          </button>
        </div>

        <form @submit.prevent="save" class="p-4 sm:p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Título</label>
            <input
              v-model="form.title"
              type="text"
              class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-teal"
            />
            <p v-if="formErrors.title" class="text-xs text-red-600 mt-1">{{ formErrors.title[0] }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Extracto</label>
            <textarea
              v-model="form.excerpt"
              rows="2"
              maxlength="500"
              class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-teal"
            ></textarea>
            <p v-if="formErrors.excerpt" class="text-xs text-red-600 mt-1">{{ formErrors.excerpt[0] }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Contenido</label>
            <textarea
              v-model="form.content"
              rows="6"
              class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-teal"
            ></textarea>
            <p v-if="formErrors.content" class="text-xs text-red-600 mt-1">{{ formErrors.content[0] }}</p>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Tema</label>
              <select
                v-model="form.topic_id"
                class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-teal"
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
                class="w-full min-w-0 border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-teal"
              />
              <p v-if="formErrors.published_at" class="text-xs text-red-600 mt-1">{{ formErrors.published_at[0] }}</p>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">URL de portada</label>
            <input
              v-model="form.cover_url"
              type="url"
              class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-teal"
            />
            <p v-if="formErrors.cover_url" class="text-xs text-red-600 mt-1">{{ formErrors.cover_url[0] }}</p>
          </div>

          <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3 pt-4 border-t border-slate-200">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 text-slate-600 hover:text-brand-dark transition"
            >
              Cancelar
            </button>
            <button
              type="submit"
              :disabled="saving"
              class="inline-flex items-center justify-center gap-2 bg-brand-teal hover:bg-brand-teal-light text-brand-dark font-semibold px-5 py-2 rounded-lg transition disabled:opacity-50"
            >
              <IconCheck :size="18" />
              {{ saving ? 'Guardando...' : 'Guardar' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <div
      v-if="showConfirm"
      class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50"
      @click.self="cancelDelete"
    >
      <div class="bg-white rounded-xl shadow-xl w-full max-w-sm max-h-[95vh] overflow-y-auto p-5 sm:p-6 text-center">
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
            class="px-4 py-2 text-slate-600 hover:text-brand-dark transition"
          >
            Cancelar
          </button>
          <button
            type="button"
            @click="doDelete"
            :disabled="deletingBusy"
            class="inline-flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-lg transition disabled:opacity-50"
          >
            <IconTrash :size="18" />
            {{ deletingBusy ? 'Eliminando...' : 'Eliminar' }}
          </button>
        </div>
      </div>
    </div>

    <div
      v-if="toast"
      class="fixed bottom-4 left-4 right-4 sm:left-auto sm:bottom-6 sm:right-6 z-50 px-4 py-3 rounded-lg shadow-lg text-sm font-medium text-center sm:text-left"
      :class="toast.type === 'error' ? 'bg-red-600 text-white' : 'bg-brand-dark text-slate-100'"
    >
      {{ toast.message }}
    </div>
  </div>
</template>