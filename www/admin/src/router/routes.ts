import models from 'src/models'
import { RouteRecordRaw } from 'vue-router'
import authentication from 'src/router/middleware/authentication'
import notAuthentication from 'src/router/middleware/not-authentication'

const indexRoute: RouteRecordRaw = {
  path: '/',
  component: () => import('layouts/MainLayout.vue'),
  children: [
    {
      path: '',
      name: 'index',
      component: () => import('pages/IndexPage.vue'),
    },
  ],
  beforeEnter: authentication,
}

Object.entries(models).forEach(([key, value]) => {
  const tempRoute: RouteRecordRaw = {
    path: value.getUrl(),
    name: `table_${key}`,
    props: { model: value },
    component: () => import('pages/models/TablePage.vue'),
    beforeEnter: authentication,
    children: [
      {
        path: value.getUrl() + '/create',
        name: `create_form_${key}`,
        props: { model: value },
        component: () => import('pages/models/FormPage.vue'),
        beforeEnter: authentication,
      },
    ],
  }
  indexRoute.children.push(tempRoute)
})

const routes: RouteRecordRaw[] = [
  indexRoute,
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
