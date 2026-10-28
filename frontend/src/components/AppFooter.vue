<script setup>
import { RouterLink } from 'vue-router'
import { IconBrandGithub, IconBrain } from '@tabler/icons-vue'
import { useSectionNav } from '../composables/useSectionNav'

const { goToSection, goHome } = useSectionNav()
</script>

<template>
  <footer class="bg-brand-dark text-slate-300">
    <div class="max-w-6xl mx-auto px-4 py-8 sm:py-10 flex flex-col md:flex-row gap-6 md:gap-8 justify-between">
      <div class="group max-w-sm">
        <a href="/" class="flex items-center gap-2 text-slate-100 font-bold text-lg mb-2" @click.prevent="goHome">
          <IconBrain :size="22" class="transition-transform duration-500 group-hover:rotate-12" />
          <span>TuringInsights</span>
        </a>
        <p class="text-sm">
          Portal de recursos técnicos sobre transformación digital, analítica y desarrollo.
        </p>
      </div>

      <div class="flex flex-row flex-wrap gap-x-6 gap-y-3 text-sm">
        <RouterLink
          :to="{ name: 'about' }"
          class="inline-block hover:text-brand-teal hover:-translate-y-0.5 transition duration-200"
        >
          Acerca de
        </RouterLink>
        <RouterLink
          :to="{ name: 'contact' }"
          class="inline-block hover:text-brand-teal hover:-translate-y-0.5 transition duration-200"
        >
          Contacto
        </RouterLink>
        <a
          href="/#recursos"
          class="inline-block hover:text-brand-teal hover:-translate-y-0.5 transition duration-200"
          @click.prevent="goToSection('#recursos')"
        >
          Recursos
        </a>
        <a
          href="https://github.com/andreszamora98/turing-insights"
          target="_blank"
          rel="noopener noreferrer"
          class="flex items-center gap-1.5 hover:text-brand-teal hover:-translate-y-0.5 transition duration-200"
        >
          <IconBrandGithub :size="16" />
          <span>GitHub</span>
        </a>
      </div>
    </div>

    <div class="border-t border-white/10">
      <div class="max-w-6xl mx-auto px-4 py-4 text-xs text-slate-400 text-center">
        © {{ new Date().getFullYear() }} TuringInsights. Todos los derechos reservados.
      </div>
    </div>
  </footer>
</template>