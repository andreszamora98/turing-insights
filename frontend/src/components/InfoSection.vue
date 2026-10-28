<script setup>
import { IconBook, IconSearch, IconTool } from '@tabler/icons-vue'
import RevealOnScroll from './RevealOnScroll.vue'
</script>

<template>
  <section class="bg-white border-t border-slate-200">
    <div class="max-w-6xl mx-auto px-4 py-10 sm:py-16 grid grid-cols-1 sm:grid-cols-3 gap-8 sm:gap-6 lg:gap-8 text-center">
      <RevealOnScroll :delay="0" class="group">
        <div class="mb-3 flex justify-center">
          <div class="transition-transform duration-300 group-hover:-translate-y-1 group-hover:scale-110">
            <IconBook :size="40" />
          </div>
        </div>
        <h3 class="font-bold text-brand-dark mb-2">Aprende</h3>
        <p class="text-sm text-slate-600">
          Contenido técnico curado para profundizar en las herramientas que mueven la industria.
        </p>
      </RevealOnScroll>

      <RevealOnScroll :delay="120" class="group">
        <div class="mb-3 flex justify-center">
          <div class="transition-transform duration-300 group-hover:-translate-y-1 group-hover:scale-110">
            <IconSearch :size="40" />
          </div>
        </div>
        <h3 class="font-bold text-brand-dark mb-2">Descubre</h3>
        <p class="text-sm text-slate-600">
          Explora temas como desarrollo web, bases de datos y backend desde un solo lugar.
        </p>
      </RevealOnScroll>

      <RevealOnScroll :delay="240" class="group">
        <div class="mb-3 flex justify-center">
          <div class="transition-transform duration-300 group-hover:-translate-y-1 group-hover:scale-110">
            <IconTool :size="40" />
          </div>
        </div>
        <h3 class="font-bold text-brand-dark mb-2">Construye</h3>
        <p class="text-sm text-slate-600">
          Aplica lo aprendido en tus proyectos y comparte hallazgos con tu equipo.
        </p>
      </RevealOnScroll>
    </div>
  </section>
</template>