import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'HomeIndex',
      component: () => import('../views/HomeIndex.vue'),
    },
    // {
    //   path: '/test',
    //   name: 'TestIndex',
    //   component: () => import('../views/TestIndex.vue'),
    // },
  ],
})

export default router
