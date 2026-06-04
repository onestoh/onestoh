import { create } from 'zustand'
import AsyncStorage from '@react-native-async-storage/async-storage'

export interface User {
  id: number; name: string; email: string; phone: string
  role: string; kyc_status: string; country: string
  preferred_currency: string; trust_score: number
  wallet_balance: number; is_active: boolean
}

interface AuthState {
  user: User | null
  token: string | null
  isAuthenticated: boolean
  setAuth: (user: User, token: string) => Promise<void>
  updateUser: (user: User) => void
  logout: () => Promise<void>
  hydrate: () => Promise<void>
}

export const useAuthStore = create<AuthState>((set) => ({
  user: null,
  token: null,
  isAuthenticated: false,
  setAuth: async (user, token) => {
    await AsyncStorage.setItem('hk_token', token)
    await AsyncStorage.setItem('hk_user', JSON.stringify(user))
    set({ user, token, isAuthenticated: true })
  },
  updateUser: (user) => set({ user }),
  logout: async () => {
    await AsyncStorage.multiRemove(['hk_token', 'hk_user'])
    set({ user: null, token: null, isAuthenticated: false })
  },
  hydrate: async () => {
    const token = await AsyncStorage.getItem('hk_token')
    const raw   = await AsyncStorage.getItem('hk_user')
    if (token && raw) {
      set({ token, user: JSON.parse(raw), isAuthenticated: true })
    }
  },
}))
