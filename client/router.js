import Vue from 'vue'
import Router from 'vue-router'
import { scrollBehavior } from '~/utils'

Vue.use(Router)

const page = path => () => import(`~/pages/${path}`).then(m => m.default || m)

const routes = [
  { path: '/', name: 'index', component: page('Index') },
  { path: '/poi/:id', name: 'poi', component: page('poi/_id.vue') },
  { path: '/routes', name: 'routes', component: page('Routes') },
  { path: '/routes/:id', name: 'route', component: page('routes/_id.vue') },
  { path: '/region/:id', name: 'region', component: page('region/_id.vue') },
  { path: '/secure', name: 'secure', component: page('secure') },
  { path: '/izbrannoe', name: 'izbrannoe', component: page('Izbrannoe') },

  { path: '/login', name: 'login', component: page('auth/login.vue') },
  { path: '/logout', name: 'logout', component: page('auth/login.vue') },
  { path: '/register', name: 'register', component: page('auth/register.vue') },
  { path: '/password/reset', name: 'password.request', component: page('auth/password/email.vue') },
  { path: '/password/reset/:token', name: 'password.reset', component: page('auth/password/reset.vue') },
  { path: '/email/verify/:id', name: 'verification.verify', component: page('auth/verification/verify.vue') },
  { path: '/email/resend', name: 'verification.resend', component: page('auth/verification/resend.vue') },

  {
    path: '/settings', 
    name: 'settings',
    component: page('settings/index.vue'),
    children: [
      { path: 'profile', name: 'settings.profile', component: page('settings/profile.vue') },
      { path: 'password', name: 'settings.password', component: page('settings/password.vue') }
    ]
  }
]

export function createRouter () {
  return new Router({
    routes,
    scrollBehavior,
    mode: 'history'
  })
}
