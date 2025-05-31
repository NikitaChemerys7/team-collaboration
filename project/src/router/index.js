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
import ResetPasswordPage from '../views/auth/ResetPasswordPage.vue'

import DashboardPage from '../views/dashboard/DashboardPage.vue'
import ProfilePage from '../views/dashboard/ProfilePage.vue'
import ManageConferencePage from '../views/dashboard/ManageConferencePage.vue'
import ManageUsersPage from '../views/dashboard/ManageUsersPage.vue'
import ManageSubpagesPage from '../views/dashboard/ManageSubpagesPage.vue'
import EditSubpagePage from '../views/dashboard/EditSubpagePage.vue'
import MailPage from '../views/dashboard/MailPage.vue'


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
    path: '/dashboard/users',
    name: 'manage-users',
    component: ManageUsersPage,
    meta: {
      requiresAuth: true,
      requiresAdmin: true
    }
  },
  {
    path: '/dashboard/conferences/:conferenceId/subpages',
    name: 'manage-subpages',
    component: () => import('../views/dashboard/ManageSubpagesPage.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/dashboard/conferences/:conferenceId/subpages/new',
    name: 'create-subpage',
    component: () => import('../views/dashboard/EditSubpagePage.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/dashboard/conferences/:conferenceId/subpages/:subpageId',
    name: 'edit-subpage',
    component: () => import('../views/dashboard/EditSubpagePage.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: NotFoundPage,
    meta: { title: 'Page Not Found' }
  },
  {
    path: '/email-verification-waiting',
    name: 'EmailVerificationWaiting',
    component: () => import('../views/auth/EmailVerificationWaitingPage.vue')
  },
  {
    path: '/forgot-password',
    name: 'ForgotPasswordPage',
    component: () => import('../views/auth/ForgotPasswordPage.vue')
  },
  {
    path: '/reset-password/:token',
    name: 'ResetPasswordPage',
    component: ResetPasswordPage
  },
  {
    path: '/dashboard/mail',
    name: 'mail',
    component: MailPage,
    meta: { title: 'Send Mail', requiresAuth: true, roles: ['admin'] }
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