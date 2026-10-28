import { nextTick } from 'vue'
import { useRoute, useRouter } from 'vue-router'

export function useSectionNav() {
  const route = useRoute()
  const router = useRouter()

  function scrollToId(id) {
    const el = document.getElementById(id)
    if (!el) return
    const header = document.querySelector('header')
    const offset = header ? header.getBoundingClientRect().height : 72
    const top = el.getBoundingClientRect().top + window.scrollY - offset
    window.scrollTo({ top: Math.max(0, top), behavior: 'smooth' })
  }

  async function goToSection(hash) {
    const id = hash.replace(/^#/, '')
    const cleanHash = `#${id}`

    if (route.path !== '/') {
      await router.push({ path: '/', hash: cleanHash })
      await nextTick()
      requestAnimationFrame(() => scrollToId(id))
      return
    }

    if (route.hash !== cleanHash) {
      await router.replace({ path: route.path, hash: cleanHash }).catch(() => {})
    }

    // Esto corre en CADA clic, no solo cuando cambia el hash
    scrollToId(id)
  }

  async function goHome() {
    if (route.path !== '/') {
      await router.push('/')
      return
    }
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }

  return { goToSection, goHome }
}