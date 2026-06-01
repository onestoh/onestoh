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
import type { UserRole } from '@/lib/types'
import { Eye, EyeOff, Check } from 'lucide-react'

const schema = z.object({
  name: z.string().min(2, 'Name is required'),
  email: z.string().email('Valid email required'),
  phone: z.string().regex(/^(07|01|\+254)[0-9]{8,9}$/, 'Valid Kenyan phone required'),
  password: z.string().min(8, 'Password must be at least 8 characters'),
  password_confirmation: z.string(),
  role: z.enum(['client', 'individual_owner', 'yard_owner', 'broker', 'driver']),
  company_name: z.string().optional(),
  referral_code: z.string().optional(),
}).refine((d) => d.password === d.password_confirmation, {
  message: 'Passwords do not match',
  path: ['password_confirmation'],
})

type FormData = z.infer<typeof schema>

const ROLES: { value: UserRole; label: string; description: string; icon: string }[] = [
  { value: 'client', label: 'Renter / Client', description: 'I want to rent vehicles or machinery', icon: '🚗' },
  { value: 'individual_owner', label: 'Individual Owner', description: 'I own 1-3 vehicles to rent out', icon: '👤' },
  { value: 'yard_owner', label: 'Yard / Fleet Owner', description: 'I own a fleet or rental yard', icon: '🏭' },
  { value: 'broker', label: 'Broker / Agent', description: 'I connect clients with vehicle owners', icon: '🤝' },
  { value: 'driver', label: 'Chauffeur / Driver', description: 'I provide driving services', icon: '💼' },
]

export default function RegisterPage() {
  const router = useRouter()
  const { setUser, setToken } = useAuthStore()
  const [showPw, setShowPw] = useState(false)
  const [loading, setLoading] = useState(false)

  const {
    register,
    handleSubmit,
    watch,
    setValue,
    formState: { errors },
  } = useForm<FormData>({
    resolver: zodResolver(schema),
    defaultValues: { role: 'client' },
  })

  const selectedRole = watch('role')

  const onSubmit = async (data: FormData) => {
    setLoading(true)
    try {
      const res = await api.post('/api/auth/register', data)
      const { user, token } = res.data.data
      setUser(user)
      setToken(token)
      toast.success('Account created! Please verify your phone.')
      router.push('/verify-otp')
    } catch (err: unknown) {
      const msg =
        err && typeof err === 'object' && 'response' in err
          ? (err as { response?: { data?: { message?: string } } }).response?.data?.message
          : undefined
      toast.error(msg || 'Registration failed')
    } finally {
      setLoading(false)
    }
  }

  return (
    <div>
      <h1 className="text-2xl font-serif font-semibold text-[#DCE5F2] mb-1">Create account</h1>
      <p className="text-sm text-[#7088A8] mb-6">Join Kenya&apos;s leading vehicle rental marketplace</p>

      <form onSubmit={handleSubmit(onSubmit)} className="space-y-5">
        {/* Role selection */}
        <div>
          <label className="block text-xs font-mono tracking-widest uppercase text-[#E8922A] mb-3">
            I am a...
          </label>
          <div className="grid grid-cols-1 gap-2">
            {ROLES.map((r) => (
              <button
                key={r.value}
                type="button"
                onClick={() => setValue('role', r.value as FormData['role'])}
                className={`flex items-center gap-3 p-3 rounded-xl border text-left transition-all ${
                  selectedRole === r.value
                    ? 'border-[#E8922A] bg-[#E8922A]/5'
                    : 'border-white/5 bg-[#141D2B] hover:border-white/20'
                }`}
              >
                <span className="text-xl">{r.icon}</span>
                <div className="flex-1 min-w-0">
                  <p className="text-sm font-medium text-[#DCE5F2]">{r.label}</p>
                  <p className="text-xs text-[#7088A8]">{r.description}</p>
                </div>
                {selectedRole === r.value && (
                  <Check className="w-4 h-4 text-[#E8922A] shrink-0" />
                )}
              </button>
            ))}
          </div>
          {errors.role && <p className="mt-1.5 text-xs text-[#E05252]">{errors.role.message}</p>}
        </div>

        <div className="grid grid-cols-1 gap-4">
          <div>
            <label className="block text-xs font-mono tracking-widest uppercase text-[#E8922A] mb-2">Full Name</label>
            <input {...register('name')} placeholder="John Kamau" className="w-full bg-[#141D2B] border border-white/10 rounded-lg px-4 py-2.5 text-[#DCE5F2] placeholder:text-[#7088A8] outline-none focus:border-[#E8922A]/50" />
            {errors.name && <p className="mt-1.5 text-xs text-[#E05252]">{errors.name.message}</p>}
          </div>
          <div>
            <label className="block text-xs font-mono tracking-widest uppercase text-[#E8922A] mb-2">Email</label>
            <input {...register('email')} type="email" placeholder="you@example.com" className="w-full bg-[#141D2B] border border-white/10 rounded-lg px-4 py-2.5 text-[#DCE5F2] placeholder:text-[#7088A8] outline-none focus:border-[#E8922A]/50" />
            {errors.email && <p className="mt-1.5 text-xs text-[#E05252]">{errors.email.message}</p>}
          </div>
          <div>
            <label className="block text-xs font-mono tracking-widest uppercase text-[#E8922A] mb-2">Phone (M-Pesa)</label>
            <input {...register('phone')} type="tel" placeholder="0712 345 678" className="w-full bg-[#141D2B] border border-white/10 rounded-lg px-4 py-2.5 text-[#DCE5F2] placeholder:text-[#7088A8] outline-none focus:border-[#E8922A]/50" />
            {errors.phone && <p className="mt-1.5 text-xs text-[#E05252]">{errors.phone.message}</p>}
          </div>
          {(selectedRole === 'yard_owner' || selectedRole === 'broker') && (
            <div>
              <label className="block text-xs font-mono tracking-widest uppercase text-[#E8922A] mb-2">Company / Business Name</label>
              <input {...register('company_name')} placeholder="Kamau Motors Ltd" className="w-full bg-[#141D2B] border border-white/10 rounded-lg px-4 py-2.5 text-[#DCE5F2] placeholder:text-[#7088A8] outline-none focus:border-[#E8922A]/50" />
            </div>
          )}
          <div>
            <label className="block text-xs font-mono tracking-widest uppercase text-[#E8922A] mb-2">Password</label>
            <div className="relative">
              <input {...register('password')} type={showPw ? 'text' : 'password'} placeholder="Min 8 characters" className="w-full bg-[#141D2B] border border-white/10 rounded-lg px-4 py-2.5 pr-10 text-[#DCE5F2] placeholder:text-[#7088A8] outline-none focus:border-[#E8922A]/50" />
              <button type="button" onClick={() => setShowPw(!showPw)} className="absolute right-3 top-1/2 -translate-y-1/2 text-[#7088A8] hover:text-[#DCE5F2]">
                {showPw ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
              </button>
            </div>
            {errors.password && <p className="mt-1.5 text-xs text-[#E05252]">{errors.password.message}</p>}
          </div>
          <div>
            <label className="block text-xs font-mono tracking-widest uppercase text-[#E8922A] mb-2">Confirm Password</label>
            <input {...register('password_confirmation')} type="password" placeholder="Repeat password" className="w-full bg-[#141D2B] border border-white/10 rounded-lg px-4 py-2.5 text-[#DCE5F2] placeholder:text-[#7088A8] outline-none focus:border-[#E8922A]/50" />
            {errors.password_confirmation && <p className="mt-1.5 text-xs text-[#E05252]">{errors.password_confirmation.message}</p>}
          </div>
          <div>
            <label className="block text-xs font-mono tracking-widest uppercase text-[#E8922A] mb-2">Referral Code (Optional)</label>
            <input {...register('referral_code')} placeholder="e.g. YARD-ABC123" className="w-full bg-[#141D2B] border border-white/10 rounded-lg px-4 py-2.5 text-[#DCE5F2] placeholder:text-[#7088A8] outline-none focus:border-[#E8922A]/50" />
          </div>
        </div>

        <p className="text-xs text-[#7088A8]">
          By registering you agree to our{' '}
          <Link href="/terms" className="text-[#E8922A]">Terms of Service</Link> and{' '}
          <Link href="/privacy" className="text-[#E8922A]">Privacy Policy</Link>.
        </p>

        <button
          type="submit"
          disabled={loading}
          className="w-full bg-[#E8922A] text-[#080C12] font-semibold py-3 rounded-lg hover:bg-[#F5B050] transition-colors disabled:opacity-50"
        >
          {loading ? 'Creating account...' : 'Create Account'}
        </button>
      </form>

      <p className="mt-6 text-center text-sm text-[#7088A8]">
        Already have an account?{' '}
        <Link href="/login" className="text-[#E8922A] hover:text-[#F5B050]">Sign in</Link>
      </p>
    </div>
  )
}
