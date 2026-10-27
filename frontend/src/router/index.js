import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'
import ResourceDetail from '../views/ResourceDetail.vue'
import LoginView from '../views/LoginView.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', name: 'home', component: Home },
    { path: '/recursos/:slug', name: 'resource-detail', component: ResourceDetail },
    { path: '/login', name: 'login', component: LoginView },
    {
      path: '/admin',
      name: 'admin',
      component: () => import('../views/AdminDashboard.vue'),
      meta: { requiresAuth: true, requiresAdmin: true },
    },
  ],
  scrollBehavior: () => ({ top: 0 }),
})

router.beforeEach((to) => {
  const token = localStorage.getItem('auth_token')
  const user = JSON.parse(localStorage.getItem('auth_user') || 'null')

  if (to.meta.requiresAuth && !token) {
    return { name: 'login' }
  }

  if (to.meta.requiresAdmin && user?.role !== 'admin') {
    return { name: 'home' }
  }

  if (to.name === 'login' && token) {
    return { name: 'home' }
  }
})

export default router