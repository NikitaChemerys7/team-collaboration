import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

import HomePage from '../views/HomePage.vue'
import AboutPage from '../views/AboutPage.vue'
import ConferencesPage from '../views/ConferencesPage.vue'
import ConferenceDetailPage from '../views/ConferenceDetailPage.vue'
import SubpagePage from '../views/SubpagePage.vue'
import ContactPage from '../views/ContactPage.vue'
import NotFoundPage from '../views/NotFoundPage.vue'

import LoginPage from '../views/auth/LoginPage.vue'
import RegistrationPage from '../views/auth/RegistrationPage.vue'

import DashboardPage from '../views/dashboard/DashboardPage.vue'
import ProfilePage from '../views/dashboard/ProfilePage.vue'
import ManageConferencePage from '../views/dashboard/ManageConferencePage.vue'
import ManageUsersPage from '../views/dashboard/ManageUsersPage.vue'
import ManageSubpagesPage from '../views/dashboard/ManageSubpagesPage.vue'
import EditSubpagePage from '../views/dashboard/EditSubpagePage.vue'

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomePage,
    meta: { title: 'Home' }
  },
  {
    path: '/about',
    name: 'about',
    component: AboutPage,
    meta: { title: 'About' }
  },
  {
    path: '/conferences',
    name: 'conferences',
    component: ConferencesPage,
    meta: { title: 'Conferences' }
  },
  {
    path: '/conferences/:id',
    name: 'conference-detail',
    component: ConferenceDetailPage,
    meta: { title: 'Conference Details' }
  },
  {
    path: '/conferences/:conferenceId/:subpageId',
    name: 'subpage',
    component: SubpagePage,
    meta: { title: 'Subpage' }
  },
  {
    path: '/contact',
    name: 'contact',
    component: ContactPage,
    meta: { title: 'Contact' }
  },
  { path: '/conference/:id', component: ConferenceDetailPage, props: true },
  
  {
    path: '/login',
    name: 'login',
    component: LoginPage,
    meta: { title: 'Login', publicOnly: true }
  },
  {
    path: '/registration',
    name: 'registration',
    component: RegistrationPage,
    meta: { title: 'Registration', publicOnly: true }
  },
  
  {
    path: '/dashboard',
    name: 'dashboard',
    component: DashboardPage,
    meta: { title: 'Dashboard', requiresAuth: true, roles: ['admin', 'editor'] }
  },
  {
    path: '/profile',
    name: 'profile',
    component: ProfilePage,
    meta: { title: 'Profile', requiresAuth: true }
  },
  {
    path: '/manage-conference/:id?',
    name: 'manage-conference',
    component: ManageConferencePage,
    meta: { title: 'Manage Conference', requiresAuth: true, roles: ['admin'] }
  },
  {
    path: '/manage-users',
    name: 'manage-users',
    component: ManageUsersPage,
    meta: { title: 'Manage Users', requiresAuth: true, roles: ['admin'] }
  },
  {
    path: '/manage-subpages/:conferenceId',
    name: 'manage-subpages',
    component: ManageSubpagesPage,
    meta: { title: 'Manage Subpages', requiresAuth: true }
  },
  {
    path: '/edit-subpage/:conferenceId/:subpageId?',
    name: 'edit-subpage',
    component: EditSubpagePage,
    meta: { title: 'Edit Subpage', requiresAuth: true }
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: NotFoundPage,
    meta: { title: 'Page Not Found' }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    } else {
      return { top: 0 }
    }
  }
})

router.beforeEach((to, from, next) => {
  document.title = `${to.meta.title || 'Page'} | University Consortium CMS`
  
  const authStore = useAuthStore()
  const { isLoggedIn, user } = authStore
  
  if (to.meta.publicOnly && isLoggedIn) {
    return next('/')
  }
  
  if (to.meta.requiresAuth && !isLoggedIn) {
    return next('/login')
  }
  
  if (to.meta.roles && isLoggedIn) {
    const hasRequiredRole = to.meta.roles.includes(user.role)
    if (!hasRequiredRole) {
        return next('/')
    }
  }
  
  next()
})

export default router