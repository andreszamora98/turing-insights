import { useRoute, useRouter } from 'vue-router'

export function useSectionNav() {
  const route = useRoute()
  const router = useRouter()

  function goToSection(hash) {
    if (route.name === 'home') {
      const el = document.querySelector(hash)
      if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' })
      return
    }
    router.push({ name: 'home', hash })
  }

  function goHome() {
    if (route.name === 'home') {
      window.scrollTo({ top: 0, behavior: 'smooth' })
      return
    }
    router.push({ name: 'home' })
  }

  return { goToSection, goHome }
}