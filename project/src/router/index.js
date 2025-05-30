import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

<<<<<<< HEAD
=======
// Public pages
>>>>>>> settings-update-3
import HomePage from '../views/HomePage.vue'
import AboutPage from '../views/AboutPage.vue'
import ConferencesPage from '../views/ConferencesPage.vue'
import ConferenceDetailPage from '../views/ConferenceDetailPage.vue'
import SubpagePage from '../views/SubpagePage.vue'
import ContactPage from '../views/ContactPage.vue'
import NotFoundPage from '../views/NotFoundPage.vue'

<<<<<<< HEAD
import LoginPage from '../views/auth/LoginPage.vue'
import RegistrationPage from '../views/auth/RegistrationPage.vue'

=======
// Auth pages
import LoginPage from '../views/auth/LoginPage.vue'
import RegistrationPage from '../views/auth/RegistrationPage.vue'
import ResetPasswordPage from '../views/auth/ResetPasswordPage.vue'

// Admin & Editor pages
>>>>>>> settings-update-3
import DashboardPage from '../views/dashboard/DashboardPage.vue'
import ProfilePage from '../views/dashboard/ProfilePage.vue'
import ManageConferencePage from '../views/dashboard/ManageConferencePage.vue'
import ManageUsersPage from '../views/dashboard/ManageUsersPage.vue'
import ManageSubpagesPage from '../views/dashboard/ManageSubpagesPage.vue'
import EditSubpagePage from '../views/dashboard/EditSubpagePage.vue'

const routes = [
<<<<<<< HEAD
=======
  // Public routes
>>>>>>> settings-update-3
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

<<<<<<< HEAD
router.beforeEach((to, from, next) => {
=======
// Global navigation guard
router.beforeEach((to, from, next) => {
  // Update document title
>>>>>>> settings-update-3
  document.title = `${to.meta.title || 'Page'} | University Consortium CMS`
  
  const authStore = useAuthStore()
  const { isLoggedIn, user } = authStore
  
<<<<<<< HEAD
=======
  // Redirect logged in users away from login page
>>>>>>> settings-update-3
  if (to.meta.publicOnly && isLoggedIn) {
    return next('/')
  }
  
<<<<<<< HEAD
=======
  // Check if route requires authentication
>>>>>>> settings-update-3
  if (to.meta.requiresAuth && !isLoggedIn) {
    return next('/login')
  }
  
<<<<<<< HEAD
=======
  // Check role requirements
>>>>>>> settings-update-3
  if (to.meta.roles && isLoggedIn) {
    const hasRequiredRole = to.meta.roles.includes(user.role)
    if (!hasRequiredRole) {
        return next('/')
    }
  }
  
  next()
})

export default router