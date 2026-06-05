import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import LoginView from '../views/LoginView.vue'
import RegisterView from '../views/RegisterView.vue'
import DashboardView from '../views/DashboardView.vue'
import UploadView from '../views/UploadView.vue'
import AnalysisView from '../views/AnalysisView.vue'
import ProfileView from '../views/ProfileView.vue'
import MyTasksView from '../views/MyTasksView.vue'


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
  component: DashboardView
},
{
  path: '/upload',
  name: 'upload',
  component: UploadView
},
{
  path: '/analysis',
  name: 'analysis',
  component: AnalysisView
},
{
  path: '/profile',
  name: 'profile',
  component: ProfileView
},
{
  path: '/tasks',
  name: 'tasks',
  component: MyTasksView
}
  ]
})

export default router