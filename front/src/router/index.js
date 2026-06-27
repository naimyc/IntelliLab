import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import LoginView from '../views/LoginView.vue'
import RegisterView from '../views/RegisterView.vue'
import DashboardView from '../views/DashboardView.vue'
import UploadView from '../views/UploadView.vue'
import AnalysisView from '../views/AnalysisView.vue'
import ProfileView from '../views/ProfileView.vue'
import MyTasksView from '../views/MyTasksView.vue'
import { useAuthStore } from '../stores/auth'
import ChatView from '../views/ChatView.vue'
import EditorView from '../views/EditorView.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView
    },

    {
      path: '/login',
      name: 'login',
      component: LoginView
    },
    {
      path: '/register',
      name: 'register',
      component: RegisterView
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: DashboardView,
      meta: { auth_required: true }
    },
    {
      path: '/upload',
      name: 'upload',
      component: UploadView,
      meta: { auth_required: true }
    },
    {
      path: '/editor',
      name: 'editor',
      component: EditorView,
      meta: { auth_required: true }
    },
    {
      path: '/analysis',
      name: 'analysis',
      component: AnalysisView,
      meta: { auth_required: true }
    },
    {
      path: '/profile',
      name: 'profile',
      component: ProfileView,
      meta: { auth_required: true }
    },
    {
      path: '/tasks',
      name: 'tasks',
      component: MyTasksView,
      meta: { auth_required: true }
    },
    {
      path: '/chat',
      name: 'chat',
      component: ChatView,
      meta: { auth_required: true }
    }
  ]
})

router.beforeEach(async (to, from) => {
  const authStore = useAuthStore()

  if (!authStore.initialized) {
    await authStore.init()
  }

  if (to.meta.auth_required && !authStore.isLoggedIn) {
    return '/login'
  }
})

export default router