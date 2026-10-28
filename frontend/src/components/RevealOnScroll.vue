<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'

defineProps({
  tag: { type: String, default: 'div' },
  delay: { type: Number, default: 0 },
})

const el = ref(null)
const visible = ref(false)
let observer = null

onMounted(() => {
  if (!('IntersectionObserver' in window)) {
    visible.value = true
    return
  }

  observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          visible.value = true
          observer.disconnect()
        }
      })
    },
    { threshold: 0.1, rootMargin: '0px 0px -40px 0px' }
  )

  observer.observe(el.value)
})

onBeforeUnmount(() => {
  if (observer) observer.disconnect()
})
</script>

<template>
  <component
    :is="tag"
    ref="el"
    class="reveal"
    :class="{ 'reveal-visible': visible }"
    :style="{ transitionDelay: `${delay}ms` }"
  >
    <slot />
  </component>
</template>

<style scoped>
.reveal {
  opacity: 0;
  transform: translateY(18px);
  transition: opacity 0.7s cubic-bezier(0.22, 1, 0.36, 1), transform 0.7s cubic-bezier(0.22, 1, 0.36, 1);
  will-change: opacity, transform;
}

.reveal-visible {
  opacity: 1;
  transform: none;
}
</style>