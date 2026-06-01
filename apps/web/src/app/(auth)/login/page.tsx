'use client'
import { useState } from 'react'
import Link from 'next/link'
import { useRouter } from 'next/navigation'
import { useForm } from 'react-hook-form'
import { zodResolver } from '@hookform/resolvers/zod'
import { z } from 'zod'
import { useAuthStore } from '@/lib/store'
import api from '@/lib/api'
import toast from 'react-hot-toast'
import { Eye, EyeOff } from 'lucide-react'

const schema = z.object({
  login: z.string().min(3, 'Enter your email or phone'),
  password: z.string().min(6, 'Password must be at least 6 characters'),
})

type FormData = z.infer<typeof schema>

export default function LoginPage() {
  const router = useRouter()
  const { setUser, setToken } = useAuthStore()
  const [showPw, setShowPw] = useState(false)
  const [loading, setLoading] = useState(false)

  const {
    register,
    handleSubmit,
    formState: { errors },
  } = useForm<FormData>({ resolver: zodResolver(schema) })

  const onSubmit = async (data: FormData) => {
    setLoading(true)
    try {
      const res = await api.post('/api/auth/login', {
        login: data.login,
        password: data.password,
      })
      const { user, token } = res.data.data
      setUser(user)
      setToken(token)
      toast.success(`Welcome back, ${user.name.split(' ')[0]}!`)

      // Route by role
      if (user.role === 'super_admin') router.push('/admin')
      else if (user.role === 'yard_owner' || user.role === 'individual_owner') router.push('/owner')
      else if (user.role === 'broker') router.push('/broker')
      else router.push('/client')
    } catch (err: unknown) {
      const msg =
        err && typeof err === 'object' && 'response' in err
          ? (err as { response?: { data?: { message?: string } } }).response?.data?.message
          : undefined
      toast.error(msg || 'Invalid credentials')
    } finally {
      setLoading(false)
    }
  }

  return (
    <div>
      <h1 className="text-2xl font-serif font-semibold text-[#DCE5F2] mb-1">Welcome back</h1>
      <p className="text-sm text-[#7088A8] mb-8">Sign in to your TheOnlineYard account</p>

      <form onSubmit={handleSubmit(onSubmit)} className="space-y-5">
        <div>
          <label className="block text-xs font-mono tracking-widest uppercase text-[#E8922A] mb-2">
            Email or Phone
          </label>
          <input
            {...register('login')}
            type="text"
            placeholder="email@example.com or 07XX XXX XXX"
            className="w-full bg-[#141D2B] border border-white/10 rounded-lg px-4 py-2.5 text-[#DCE5F2] placeholder:text-[#7088A8] outline-none focus:border-[#E8922A]/50"
          />
          {errors.login && <p className="mt-1.5 text-xs text-[#E05252]">{errors.login.message}</p>}
        </div>

        <div>
          <label className="block text-xs font-mono tracking-widest uppercase text-[#E8922A] mb-2">
            Password
          </label>
          <div className="relative">
            <input
              {...register('password')}
              type={showPw ? 'text' : 'password'}
              placeholder="Your password"
              className="w-full bg-[#141D2B] border border-white/10 rounded-lg px-4 py-2.5 pr-10 text-[#DCE5F2] placeholder:text-[#7088A8] outline-none focus:border-[#E8922A]/50"
            />
            <button
              type="button"
              onClick={() => setShowPw(!showPw)}
              className="absolute right-3 top-1/2 -translate-y-1/2 text-[#7088A8] hover:text-[#DCE5F2]"
            >
              {showPw ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
            </button>
          </div>
          {errors.password && <p className="mt-1.5 text-xs text-[#E05252]">{errors.password.message}</p>}
        </div>

        <div className="flex items-center justify-between">
          <label className="flex items-center gap-2 text-sm text-[#7088A8] cursor-pointer">
            <input type="checkbox" className="accent-[#E8922A]" />
            Remember me
          </label>
          <Link href="/forgot-password" className="text-sm text-[#E8922A] hover:text-[#F5B050]">
            Forgot password?
          </Link>
        </div>

        <button
          type="submit"
          disabled={loading}
          className="w-full bg-[#E8922A] text-[#080C12] font-semibold py-3 rounded-lg hover:bg-[#F5B050] transition-colors disabled:opacity-50"
        >
          {loading ? 'Signing in...' : 'Sign In'}
        </button>
      </form>

      <p className="mt-6 text-center text-sm text-[#7088A8]">
        Don&apos;t have an account?{' '}
        <Link href="/register" className="text-[#E8922A] hover:text-[#F5B050]">Create one</Link>
      </p>
    </div>
  )
}
