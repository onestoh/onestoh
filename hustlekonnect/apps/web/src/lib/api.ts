import axios, { AxiosError } from 'axios'

const api = axios.create({
  baseURL: process.env.NEXT_PUBLIC_API_URL,
  headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
  timeout: 30000,
})

api.interceptors.request.use((config) => {
  if (typeof window !== 'undefined') {
    const token = localStorage.getItem('hk_token')
    if (token) config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

api.interceptors.response.use(
  (res) => res,
  (error: AxiosError) => {
    if (error.response?.status === 401) {
      if (typeof window !== 'undefined') {
        localStorage.removeItem('hk_token')
        localStorage.removeItem('hk_user')
        window.location.href = '/login'
      }
    }
    return Promise.reject(error)
  }
)

export default api
