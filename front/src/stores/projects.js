import { reactive } from 'vue'
import api from '../lib/axios'

export const projectStore = reactive({
  projects: [],
  stats: { total: 0, done: 0, recent: [] },
  loading: false,
  currentProjectId: null,
})

export async function fetchProjects() {
  projectStore.loading = true
  try {
    const { data } = await api.get('/api/projects')
    projectStore.projects = data.projects || []
  } catch(e) { console.error(e) }
  finally { projectStore.loading = false }
}

export async function fetchStats() {
  try {
    const { data } = await api.get('/api/projects/stats')
    projectStore.stats = data
  } catch(e) { console.error(e) }
}

export async function createProject(payload) {
  try {
    const { data } = await api.post('/api/projects', payload)
    projectStore.projects.unshift(data.project)
    projectStore.currentProjectId = data.project.id
    return data.project
  } catch(e) { console.error(e); return null }
}

export async function updateProject(id, payload) {
  try {
    const { data } = await api.patch(`/api/projects/${id}`, payload)
    const i = projectStore.projects.findIndex(p => p.id === id)
    if (i !== -1) projectStore.projects[i] = data.project
    return data.project
  } catch(e) { console.error(e); return null }
}

export async function deleteProject(id) {
  try {
    await api.delete(`/api/projects/${id}`)
    projectStore.projects = projectStore.projects.filter(p => p.id !== id)
  } catch(e) { console.error(e) }
}

export async function updateAnalyse(id, payload) {
  try {
    await api.post(`/api/projects/${id}/analyse`, payload)
  } catch(e) { console.error(e) }
}
