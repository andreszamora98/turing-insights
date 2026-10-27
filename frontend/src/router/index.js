import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'
import ResourceDetail from '../views/ResourceDetail.vue'

export default createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', name: 'home', component: Home },
    { path: '/recursos/:slug', name: 'resource-detail', component: ResourceDetail },
  ],
  scrollBehavior: () => ({ top: 0 }),
})