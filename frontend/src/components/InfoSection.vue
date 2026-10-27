<script setup>
import { IconBook, IconSearch, IconTool } from '@tabler/icons-vue'
</script>

<template>
  <section class="bg-white border-t border-slate-200">
    <div class="max-w-6xl mx-auto px-4 py-16 grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
      <div>
        <div class="mb-3 flex justify-center">
          <IconBook :size="40" />
        </div>
        <h3 class="font-bold text-brand-dark mb-2">Aprende</h3>
        <p class="text-sm text-slate-600">
          Contenido técnico curado para profundizar en las herramientas que mueven la industria.
        </p>
      </div>
      <div>
        <div class="mb-3 flex justify-center">
          <IconSearch :size="40" />
        </div>
        <h3 class="font-bold text-brand-dark mb-2">Descubre</h3>
        <p class="text-sm text-slate-600">
          Explora temas como desarrollo web, bases de datos y backend desde un solo lugar.
        </p>
      </div>
      <div>
        <div class="mb-3 flex justify-center">
          <IconTool :size="40" />
        </div>
        <h3 class="font-bold text-brand-dark mb-2">Construye</h3>
        <p class="text-sm text-slate-600">
          Aplica lo aprendido en tus proyectos y comparte hallazgos con tu equipo.
        </p>
      </div>
    </div>
  </section>
</template>