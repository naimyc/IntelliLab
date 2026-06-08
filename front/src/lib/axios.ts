import axios from 'axios'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL ?? 'http://localhost:8000',
  withCredentials: true,
  withXSRFToken: true,    // Axios liest XSRF-TOKEN Cookie und setzt X-XSRF-TOKEN Header automatisch
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
})

export default api