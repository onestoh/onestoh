import { create } from 'zustand'
import { persist } from 'zustand/middleware'
import { User } from '@/lib/types'

interface AuthState {
  user: User | null
  token: string | null
  isAuthenticated: boolean
  setAuth: (user: User, token: string) => void
  updateUser: (user: User) => void
  logout: () => void
}

export const useAuthStore = create<AuthState>()(
  persist(
    (set) => ({
      user: null,
      token: null,
      isAuthenticated: false,
      setAuth: (user, token) => {
        localStorage.setItem('hk_token', token)
        set({ user, token, isAuthenticated: true })
      },
      updateUser: (user) => set({ user }),
      logout: () => {
        localStorage.removeItem('hk_token')
        set({ user: null, token: null, isAuthenticated: false })
      },
    }),
    { name: 'hk_auth', partialize: (s) => ({ user: s.user, token: s.token, isAuthenticated: s.isAuthenticated }) }
  )
)
