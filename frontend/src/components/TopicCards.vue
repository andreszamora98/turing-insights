<script setup>
import RevealOnScroll from './RevealOnScroll.vue'

defineProps({
  topics: { type: Array, required: true },
  active: { type: String, default: '' },
})
const emit = defineEmits(['select'])
</script>

<template>
  <div id="temas" class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
    <RevealOnScroll v-for="(t, i) in topics" :key="t.slug" :delay="i * 90">
      <button
        type="button"
        @click="emit('select', t.slug)"
        class="group w-full bg-brand-teal text-brand-dark rounded-xl p-4 lg:p-6 flex items-center gap-3 lg:gap-4 text-left hover:bg-brand-teal-light hover:-translate-y-1 hover:shadow-lg hover:shadow-brand-teal/20 active:scale-[0.98] transition duration-300 ring-2"
        :class="active === t.slug ? 'ring-brand-dark' : 'ring-transparent'"
      >
        <component
          :is="t.icon"
          :size="28"
          class="shrink-0 transition-transform duration-300 group-hover:scale-110 group-hover:-rotate-6"
        />
        <span class="font-semibold text-base lg:text-lg">{{ t.name }}</span>
      </button>
    </RevealOnScroll>
  </div>
</template>