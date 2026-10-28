<script setup>
defineProps({
  topics: { type: Array, required: true },
  active: { type: String, default: '' },
})
const emit = defineEmits(['select'])
</script>

<template>
  <div id="temas" class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
    <button
      v-for="t in topics"
      :key="t.slug"
      type="button"
      @click="emit('select', t.slug)"
      class="bg-brand-teal text-brand-dark rounded-xl p-4 lg:p-6 flex items-center gap-3 lg:gap-4 text-left hover:bg-brand-teal-light transition ring-2"
      :class="active === t.slug ? 'ring-brand-dark' : 'ring-transparent'"
    >
      <component :is="t.icon" :size="28" class="shrink-0" />
      <span class="font-semibold text-base lg:text-lg">{{ t.name }}</span>
    </button>
  </div>
</template>