import { createRouter, createWebHistory } from 'vue-router'
import Login from './components/Login.vue'
import Dashboard from './components/Dashboard.vue'
import NotFound from './components/NotFound.vue'
import Register from './components/Register.vue'
import Wizard from './components/Wizard.vue'
import Profile from './components/Profile.vue'

const routes = [
  {
    path: '/',
    redirect: '/login'
  },
  {
    path: '/login',
    name: 'login',
    component: Login,
    meta: { requiresAuth: false }
  },
  {
    path: '/register',
    name: 'register',
    component: Register,
    meta: { requiresAuth: false }
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: Dashboard,
    meta: { requiresAuth: true }
  },
  {
  path: '/activities',
  name: 'activities',
  component: Wizard,
  meta: { requiresAuth: true }
},
 {
  path: '/profile',
  name: 'profile',
  component: Profile,
  meta: { requiresAuth: true }
},
  // La route 404 en Dernier svp //
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: NotFound
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to) => {
  const isAuthenticated = Boolean(localStorage.getItem('jwt_token'))

  if (to.meta.requiresAuth && !isAuthenticated) {
    return { name: 'login' }
  }

  if (to.name === 'login' && isAuthenticated) {
    return { name: 'dashboard' }
  }

  return true
})

export default router
