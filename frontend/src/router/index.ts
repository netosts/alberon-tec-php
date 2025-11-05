import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'HomeIndex',
      component: () => import('../views/HomeIndex.vue'),
      meta: { layout: 'HomeLayout' },
    },
    {
      path: '/test',
      name: 'TestIndex',
      component: () => import('../views/TestIndex.vue'),
      meta: { layout: 'HomeLayout' },
    },
  ],
})

export default router
