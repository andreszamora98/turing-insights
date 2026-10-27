<script setup>
import { IconBrandGithub, IconBrain } from '@tabler/icons-vue'
</script>

<template>
  <footer class="bg-brand-dark text-slate-300">
    <div class="max-w-6xl mx-auto px-4 py-10 flex flex-col md:flex-row gap-8 justify-between">
      <div class="max-w-sm">
        <div class="flex items-center gap-2 text-slate-100 font-bold text-lg mb-2">
          <IconBrain :size="22" />
          <span>TuringInsights</span>
        </div>
        <p class="text-sm">
          Portal de recursos técnicos sobre transformación digital, analítica y desarrollo.
        </p>
      </div>

      <div class="flex flex-col sm:flex-row gap-6 text-sm">
        <a href="#" class="hover:text-brand-teal transition">Acerca de</a>
        <a href="#" class="hover:text-brand-teal transition">Contacto</a>
        <a href="#recursos" class="hover:text-brand-teal transition">Recursos</a>
        <a
          href="https://github.com/tu-usuario/turing-insights"
          target="_blank"
          rel="noopener"
          class="flex items-center gap-1.5 hover:text-brand-teal transition"
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