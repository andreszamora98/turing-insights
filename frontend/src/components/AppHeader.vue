<script setup>
import { RouterLink } from 'vue-router'
import { IconBrain, IconLogin } from '@tabler/icons-vue'
</script>

<template>
  <header class="bg-brand-dark text-slate-100">
    <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
      <RouterLink to="/" class="flex items-center gap-2">
        <IconBrain :size="24" />
        <span class="font-bold text-lg">TuringInsights</span>
      </RouterLink>

      <nav class="flex items-center gap-6 text-sm">
        <a href="#inicio" class="hover:text-brand-teal transition">Inicio</a>
        <a href="#recursos" class="hover:text-brand-teal transition">Recursos</a>
        <a href="#temas" class="hover:text-brand-teal transition">Temas</a>
        <a
          href="#"
          class="flex items-center gap-1.5 bg-brand-teal text-brand-dark font-semibold px-3 py-1.5 rounded-md hover:bg-brand-teal-light transition"
        >
          <IconLogin :size="16" />
          <span>Iniciar sesión</span>
        </a>
      </nav>
    </div>
  </header>
</template>