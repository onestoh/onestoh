import { create } from 'zustand'
import { persist } from 'zustand/middleware'
import type { User } from './types'

interface AuthState {
  user: User | null
  token: string | null
  setUser: (user: User) => void
  setToken: (token: string) => void
  logout: () => void
}

export const useAuthStore = create<AuthState>()(
  persist(
    (set) => ({
      user: null,
      token: null,
      setUser: (user) => set({ user }),
      setToken: (token) => {
        if (typeof window !== 'undefined') localStorage.setItem('auth_token', token)
        set({ token })
      },
      logout: () => {
        if (typeof window !== 'undefined') localStorage.removeItem('auth_token')
        set({ user: null, token: null })
      },
    }),
    { name: 'yard-auth', partialize: (s) => ({ user: s.user, token: s.token }) }
  )
)
