import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'
import ResourceDetail from '../views/ResourceDetail.vue'
import LoginView from '../views/LoginView.vue'
import AboutView from '../views/AboutView.vue'
import ContactView from '../views/ContactView.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', name: 'home', component: Home },
    { path: '/recursos/:slug', name: 'resource-detail', component: ResourceDetail, meta: { requiresAuth: true } },
    { path: '/acerca-de', name: 'about', component: AboutView },
    { path: '/contacto', name: 'contact', component: ContactView },
    { path: '/login', name: 'login', component: LoginView },
    {
      path: '/admin',
      name: 'admin',
      component: () => import('../views/AdminDashboard.vue'),
      meta: { requiresAuth: true, requiresAdmin: true },
    },
  ],
  scrollBehavior(to, from, savedPosition) {
    const samePage = to.path === from.path && from.matched.length > 0
    const delay = samePage ? 0 : 280

    return new Promise((resolve) => {
      setTimeout(() => {
        if (savedPosition) {
          resolve(savedPosition)
          return
        }
        if (to.hash && document.querySelector(to.hash)) {
          resolve({ el: to.hash, behavior: 'smooth' })
          return
        }
        resolve({ top: 0 })
      }, delay)
    })
  },
})

router.beforeEach((to) => {
  const token = localStorage.getItem('auth_token')
  const user = JSON.parse(localStorage.getItem('auth_user') || 'null')

  if (to.meta.requiresAuth && !token) {
    return { name: 'login', query: { redirect: to.fullPath } }
  }

  if (to.meta.requiresAdmin && user?.role !== 'admin') {
    return { name: 'home' }
  }

  if (to.name === 'login' && token) {
    return { name: 'home' }
  }
})

export default router