import { RouteRecordRaw } from 'vue-router'
import authentication from 'src/router/middleware/authentication'
import notAuthentication from 'src/router/middleware/not-authentication'

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      {
        path: '',
        name: 'index',
        component: () => import('pages/IndexPage.vue'),
      },
      // {
      //   path: '/albums',
      //   name: 'Albums',
      //   component: () => import('pages/AlbumsPage.vue'),
      // },
    ],
    beforeEnter: authentication,
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('pages/LoginByCredentials.vue'),
    beforeEnter: notAuthentication,
  },
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue'),
  },
]

export default routes
