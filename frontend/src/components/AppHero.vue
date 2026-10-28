<template>
  <section id="inicio" class="relative overflow-hidden bg-brand-dark text-slate-100">
    <div
      class="hero-glow pointer-events-none absolute -top-24 -left-24 h-72 w-72 rounded-full bg-brand-teal/10 blur-3xl"
      aria-hidden="true"
    ></div>
    <div
      class="hero-glow hero-glow-delay pointer-events-none absolute -bottom-28 -right-20 h-80 w-80 rounded-full bg-brand-teal/10 blur-3xl"
      aria-hidden="true"
    ></div>

    <div class="relative max-w-4xl mx-auto px-4 py-10 sm:py-14 md:py-20 text-center">
      <h1 class="hero-rise text-3xl sm:text-4xl md:text-5xl font-bold mb-3 sm:mb-4" style="--i: 0">
        Aprende. Explora. Construye.
      </h1>
      <p
        class="hero-rise text-sm sm:text-base text-slate-300 max-w-2xl mx-auto mb-6 sm:mb-8"
        style="--i: 1"
      >
        Recursos técnicos sobre transformación digital, analítica, cloud y desarrollo
        para impulsar tu carrera y la de tu equipo.
      </p>
      <a
        href="#recursos"
        class="hero-rise inline-block bg-brand-teal text-brand-dark font-semibold px-5 py-2.5 sm:px-6 sm:py-3 rounded-lg hover:bg-brand-teal-light hover:-translate-y-0.5 hover:shadow-lg hover:shadow-brand-teal/20 active:scale-95 transition duration-200"
        style="--i: 2"
      >
        Explorar recursos
      </a>
    </div>
  </section>
</template>

<style scoped>
.hero-rise {
  animation: hero-rise 0.8s cubic-bezier(0.22, 1, 0.36, 1) both;
  animation-delay: calc(var(--i, 0) * 120ms + 100ms);
}

.hero-glow {
  animation: hero-float 10s ease-in-out infinite;
}

.hero-glow-delay {
  animation-delay: -5s;
}

@keyframes hero-rise {
  from {
    opacity: 0;
    transform: translateY(18px);
  }
  to {
    opacity: 1;
    transform: none;
  }
}

@keyframes hero-float {
  0%,
  100% {
    transform: translate3d(0, 0, 0) scale(1);
  }
  50% {
    transform: translate3d(20px, -16px, 0) scale(1.08);
  }
}
</style>