<script setup>
import { RouterLink } from 'vue-router'
import { IconBrandGithub, IconBrain } from '@tabler/icons-vue'
import { useSectionNav } from '../composables/useSectionNav'

const { goToSection, goHome } = useSectionNav()
</script>

<template>
  <footer class="bg-brand-dark text-slate-300">
    <div class="max-w-6xl mx-auto px-4 py-3 sm:py-4 flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-6 sm:justify-between">
      <a href="/" class="group flex items-center gap-2 text-slate-100 font-bold" @click.prevent="goHome">
        <IconBrain :size="20" class="transition-transform duration-500 group-hover:rotate-12" />
        <span>TuringInsights</span>
      </a>

      <nav class="flex flex-wrap items-center gap-x-5 gap-y-1 text-sm">
        <RouterLink
          :to="{ name: 'about' }"
          class="hover:text-brand-teal hover:-translate-y-0.5 transition duration-200"
        >
          Acerca de
        </RouterLink>
        <RouterLink
          :to="{ name: 'contact' }"
          class="hover:text-brand-teal hover:-translate-y-0.5 transition duration-200"
        >
          Contacto
        </RouterLink>
        <a
          href="/#recursos"
          class="hover:text-brand-teal hover:-translate-y-0.5 transition duration-200"
          @click.prevent="goToSection('#recursos')"
        >
          Recursos
        </a>
        <a
          href="https://github.com/andreszamora98/turing-insights"
          target="_blank"
          rel="noopener noreferrer"
          class="inline-flex items-center gap-1.5 hover:text-brand-teal hover:-translate-y-0.5 transition duration-200"
        >
          <IconBrandGithub :size="16" />
          <span>GitHub</span>
        </a>
      </nav>
    </div>

    <div class="border-t border-white/10">
      <div class="max-w-6xl mx-auto px-4 py-2 text-xs text-slate-400 text-center">
        © {{ new Date().getFullYear() }} TuringInsights
      </div>
    </div>
  </footer>
</template>