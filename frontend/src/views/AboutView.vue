<script setup>
import { RouterLink } from 'vue-router'
import { IconTarget, IconBulb, IconUsers, IconArrowRight } from '@tabler/icons-vue'
import AppHeader from '../components/AppHeader.vue'
import AppFooter from '../components/AppFooter.vue'
import RevealOnScroll from '../components/RevealOnScroll.vue'

const pillars = [
  {
    title: 'Misión',
    text: 'Reunir en un solo lugar contenido técnico claro y verificado para que equipos y profesionales avancen más rápido.',
    icon: IconTarget,
  },
  {
    title: 'Enfoque',
    text: 'Transformación digital, analítica, cloud y desarrollo, con énfasis en desarrollo web, bases de datos y backend.',
    icon: IconBulb,
  },
  {
    title: 'Comunidad',
    text: 'Un espacio pensado para aprender, compartir hallazgos y construir en equipo sobre bases sólidas.',
    icon: IconUsers,
  },
]
</script>

<template>
  <div class="min-h-screen flex flex-col bg-slate-50">
    <AppHeader />

    <section class="relative overflow-hidden bg-brand-dark text-slate-100">
      <div
        class="about-float pointer-events-none absolute -top-24 -left-24 h-72 w-72 rounded-full bg-brand-teal/10 blur-3xl"
        aria-hidden="true"
      ></div>
      <div
        class="about-float about-float-delay pointer-events-none absolute -bottom-28 -right-20 h-80 w-80 rounded-full bg-brand-teal/10 blur-3xl"
        aria-hidden="true"
      ></div>

      <div class="relative max-w-4xl mx-auto px-4 py-10 sm:py-14 md:py-20 text-center">
        <h1 class="rise text-3xl sm:text-4xl md:text-5xl font-bold mb-3 sm:mb-4" style="--i: 0">
          Acerca de TuringInsights
        </h1>
        <p class="rise text-sm sm:text-base text-slate-300 max-w-2xl mx-auto" style="--i: 1">
          Un portal de recursos técnicos creado para impulsar el aprendizaje continuo y la colaboración entre equipos.
        </p>
      </div>
    </section>

    <main class="flex-1">
      <section class="max-w-6xl mx-auto px-4 py-10 sm:py-14">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
          <RevealOnScroll v-for="(p, i) in pillars" :key="p.title" :delay="i * 120">
            <div class="group h-full bg-white rounded-xl shadow-sm p-6 transition duration-300 hover:-translate-y-1 hover:shadow-lg">
              <div class="inline-flex items-center justify-center w-12 h-12 rounded-lg bg-brand-teal/10 text-brand-teal mb-4 transition-transform duration-300 group-hover:scale-110 group-hover:-rotate-6">
                <component :is="p.icon" :size="26" />
              </div>
              <h2 class="text-lg font-bold text-brand-dark mb-2">{{ p.title }}</h2>
              <p class="text-sm text-slate-600">{{ p.text }}</p>
            </div>
          </RevealOnScroll>
        </div>
      </section>

      <section class="max-w-4xl mx-auto px-4 pb-12 sm:pb-16">
        <RevealOnScroll>
          <div class="bg-white rounded-2xl shadow-sm p-6 sm:p-10 text-center">
            <h2 class="text-xl sm:text-2xl font-bold text-brand-dark mb-3">Empieza a explorar</h2>
            <p class="text-sm sm:text-base text-slate-600 max-w-xl mx-auto mb-6">
              Revisa el catálogo de recursos o escríbenos si quieres proponer contenido o dejar comentarios.
            </p>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-center gap-3">
              <RouterLink
                :to="{ name: 'home', hash: '#recursos' }"
                class="group inline-flex items-center justify-center gap-2 bg-brand-teal text-brand-dark font-semibold px-5 py-2.5 rounded-lg hover:bg-brand-teal-light hover:-translate-y-0.5 hover:shadow-lg hover:shadow-brand-teal/20 active:scale-95 transition duration-200"
              >
                Ver recursos
                <IconArrowRight :size="16" class="transition-transform duration-300 group-hover:translate-x-1" />
              </RouterLink>
              <RouterLink
                :to="{ name: 'contact' }"
                class="inline-flex items-center justify-center gap-2 border border-slate-300 text-slate-700 font-semibold px-5 py-2.5 rounded-lg hover:border-brand-teal hover:text-brand-dark hover:-translate-y-0.5 active:scale-95 transition duration-200"
              >
                Contactar
              </RouterLink>
            </div>
          </div>
        </RevealOnScroll>
      </section>
    </main>

    <AppFooter />
  </div>
</template>

<style scoped>
.rise {
  animation: rise 0.8s cubic-bezier(0.22, 1, 0.36, 1) both;
  animation-delay: calc(var(--i, 0) * 120ms + 100ms);
}

.about-float {
  animation: float 10s ease-in-out infinite;
}

.about-float-delay {
  animation-delay: -5s;
}

@keyframes rise {
  from {
    opacity: 0;
    transform: translateY(18px);
  }
  to {
    opacity: 1;
    transform: none;
  }
}

@keyframes float {
  0%,
  100% {
    transform: translate3d(0, 0, 0) scale(1);
  }
  50% {
    transform: translate3d(20px, -16px, 0) scale(1.08);
  }
}
</style>