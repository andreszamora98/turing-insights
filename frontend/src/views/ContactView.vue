<script setup>
import { ref, computed } from 'vue'
import {
  IconMail, IconBrandGithub, IconUser, IconSend, IconCheck, IconAlertCircle,
} from '@tabler/icons-vue'
import AppHeader from '../components/AppHeader.vue'
import AppFooter from '../components/AppFooter.vue'

const contactEmail = 'contacto@turing-ia.com'
const githubUrl = 'https://github.com/andreszamora98/turing-insights'

const form = ref({ name: '', email: '', subject: 'consulta', message: '' })
const errors = ref({})
const sent = ref(false)

const subjects = [
  { value: 'consulta', label: 'Consulta general' },
  { value: 'contenido', label: 'Proponer contenido' },
  { value: 'error', label: 'Reportar un error' },
  { value: 'otro', label: 'Otro' },
]

const messageLength = computed(() => form.value.message.length)

function validate() {
  const e = {}
  if (!form.value.name.trim()) e.name = 'Ingresa tu nombre'
  if (!/^\S+@\S+\.\S+$/.test(form.value.email)) e.email = 'Ingresa un correo válido'
  if (form.value.message.trim().length < 10) e.message = 'El mensaje debe tener al menos 10 caracteres'
  errors.value = e
  return Object.keys(e).length === 0
}

function onSubmit() {
  sent.value = false
  if (!validate()) return

  const subjectLabel = subjects.find((s) => s.value === form.value.subject)?.label || 'Contacto'
  const subject = encodeURIComponent(`[TuringInsights] ${subjectLabel}`)
  const body = encodeURIComponent(
    `Nombre: ${form.value.name}\nCorreo: ${form.value.email}\n\n${form.value.message}`
  )

  window.location.href = `mailto:${contactEmail}?subject=${subject}&body=${body}`
  sent.value = true
}
</script>

<template>
  <div class="min-h-screen flex flex-col bg-slate-50">
    <AppHeader />

    <main class="flex-1 max-w-6xl mx-auto px-4 py-8 sm:py-12 w-full">
      <div class="rise mb-8 sm:mb-10" style="--i: 0">
        <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-brand-dark mb-2">Contacto</h1>
        <p class="text-sm sm:text-base text-slate-500 max-w-2xl">
          Escríbenos para resolver dudas, proponer contenido o reportar cualquier problema.
        </p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <aside class="space-y-4 lg:col-span-1">
          <a
            :href="`mailto:${contactEmail}`"
            class="rise group flex items-start gap-4 bg-white rounded-xl shadow-sm p-5 transition duration-300 hover:-translate-y-1 hover:shadow-lg"
            style="--i: 1"
          >
            <div class="inline-flex items-center justify-center w-11 h-11 rounded-lg bg-brand-teal/10 text-brand-teal shrink-0 transition-transform duration-300 group-hover:scale-110">
              <IconMail :size="22" />
            </div>
            <div class="min-w-0">
              <p class="font-semibold text-brand-dark">Correo</p>
              <p class="text-sm text-slate-600 break-words">{{ contactEmail }}</p>
            </div>
          </a>

          <a
            :href="githubUrl"
            target="_blank"
            rel="noopener noreferrer"
            class="rise group flex items-start gap-4 bg-white rounded-xl shadow-sm p-5 transition duration-300 hover:-translate-y-1 hover:shadow-lg"
            style="--i: 2"
          >
            <div class="inline-flex items-center justify-center w-11 h-11 rounded-lg bg-brand-teal/10 text-brand-teal shrink-0 transition-transform duration-300 group-hover:scale-110">
              <IconBrandGithub :size="22" />
            </div>
            <div class="min-w-0">
              <p class="font-semibold text-brand-dark">GitHub</p>
              <p class="text-sm text-slate-600">Código fuente del proyecto</p>
            </div>
          </a>
        </aside>

        <section class="rise bg-white rounded-2xl shadow-sm p-5 sm:p-8 lg:col-span-2" style="--i: 2">
          <form @submit.prevent="onSubmit" class="space-y-4" novalidate>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Nombre</label>
                <div class="flex items-center border border-slate-300 hover:border-slate-400 rounded-lg px-3 focus-within:ring-2 focus-within:ring-brand-teal transition duration-200">
                  <IconUser :size="18" class="text-slate-400 shrink-0" />
                  <input
                    v-model="form.name"
                    type="text"
                    autocomplete="name"
                    class="flex-1 min-w-0 px-2 py-2.5 bg-transparent focus:outline-none"
                  />
                </div>
                <Transition name="field">
                  <p v-if="errors.name" class="text-xs text-red-600 mt-1">{{ errors.name }}</p>
                </Transition>
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Correo</label>
                <div class="flex items-center border border-slate-300 hover:border-slate-400 rounded-lg px-3 focus-within:ring-2 focus-within:ring-brand-teal transition duration-200">
                  <IconMail :size="18" class="text-slate-400 shrink-0" />
                  <input
                    v-model="form.email"
                    type="email"
                    autocomplete="email"
                    class="flex-1 min-w-0 px-2 py-2.5 bg-transparent focus:outline-none"
                  />
                </div>
                <Transition name="field">
                  <p v-if="errors.email" class="text-xs text-red-600 mt-1">{{ errors.email }}</p>
                </Transition>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Asunto</label>
              <select
                v-model="form.subject"
                class="w-full border border-slate-300 hover:border-slate-400 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-brand-teal transition duration-200"
              >
                <option v-for="s in subjects" :key="s.value" :value="s.value">{{ s.label }}</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Mensaje</label>
              <textarea
                v-model="form.message"
                rows="6"
                maxlength="1000"
                class="w-full border border-slate-300 hover:border-slate-400 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-teal transition duration-200"
              ></textarea>
              <div class="flex items-start justify-between gap-3 mt-1">
                <Transition name="field">
                  <p v-if="errors.message" class="text-xs text-red-600">{{ errors.message }}</p>
                </Transition>
                <span class="text-xs text-slate-400 ml-auto">{{ messageLength }}/1000</span>
              </div>
            </div>

            <Transition name="field">
              <div
                v-if="sent"
                class="flex items-center gap-2 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg px-3 py-2"
              >
                <IconCheck :size="16" class="shrink-0" />
                Se abrió tu cliente de correo con el mensaje listo para enviar.
              </div>
            </Transition>

            <Transition name="field">
              <div
                v-if="Object.keys(errors).length > 0"
                class="flex items-center gap-2 text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg px-3 py-2"
              >
                <IconAlertCircle :size="16" class="shrink-0" />
                Revisa los campos marcados antes de continuar.
              </div>
            </Transition>

            <div class="flex justify-end pt-2">
              <button
                type="submit"
                class="group w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-brand-teal hover:bg-brand-teal-light text-brand-dark font-semibold px-6 py-2.5 rounded-lg hover:-translate-y-0.5 hover:shadow-lg hover:shadow-brand-teal/20 active:scale-95 transition duration-200"
              >
                <IconSend :size="18" class="transition-transform duration-300 group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
                Enviar mensaje
              </button>
            </div>
          </form>
        </section>
      </div>
    </main>

    <AppFooter />
  </div>
</template>

<style scoped>
.rise {
  animation: rise 0.65s cubic-bezier(0.22, 1, 0.36, 1) both;
  animation-delay: calc(var(--i, 0) * 80ms);
}

.field-enter-active,
.field-leave-active {
  transition: opacity 0.25s ease, transform 0.25s ease;
}

.field-enter-from,
.field-leave-to {
  opacity: 0;
  transform: translateY(-4px);
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