'use client'
import { useState } from 'react'
import Link from 'next/link'
import { useRouter, useSearchParams } from 'next/navigation'
import { useForm } from 'react-hook-form'
import { zodResolver } from '@hookform/resolvers/zod'
import { z } from 'zod'
import toast from 'react-hot-toast'
import api from '@/lib/api'
import { useAuthStore } from '@/store/auth'

const schema = z.object({
  name:            z.string().min(2, 'Name too short'),
  email:           z.string().email('Invalid email'),
  phone:           z.string().min(7, 'Invalid phone'),
  password:        z.string().min(8, 'Min 8 characters').regex(/[A-Za-z]/, 'Must contain letters').regex(/[0-9]/, 'Must contain numbers').regex(/[^A-Za-z0-9]/, 'Must contain symbol'),
  password_confirmation: z.string(),
  role:            z.enum(['client', 'owner', 'broker']),
  country:         z.enum(['KE','UG','TZ','NG','GH','ZA']),
  referral_code:   z.string().optional(),
}).refine(d => d.password === d.password_confirmation, { message: 'Passwords do not match', path: ['password_confirmation'] })

type FormData = z.infer<typeof schema>

export default function RegisterPage() {
  const router = useRouter()
  const params = useSearchParams()
  const setAuth = useAuthStore((s) => s.setAuth)
  const [loading, setLoading] = useState(false)

  const { register, handleSubmit, formState: { errors } } = useForm<FormData>({
    resolver: zodResolver(schema),
    defaultValues: { role: (params.get('role') as any) || 'client', country: 'KE' },
  })

  const onSubmit = async (data: FormData) => {
    setLoading(true)
    try {
      const res = await api.post('/auth/register', data)
      setAuth(res.data.data.user, res.data.data.token)
      toast.success('Account created! Please verify your phone.')
      router.push('/verify-otp')
    } catch (err: any) {
      const msg = err.response?.data?.message || 'Registration failed.'
      toast.error(msg)
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="min-h-screen flex items-center justify-center px-4 py-12 bg-brand-dark">
      <div className="w-full max-w-lg">
        <div className="text-center mb-8">
          <Link href="/" className="text-2xl font-bold">
            <span className="text-brand-amber">Hustle</span>Konnect
          </Link>
          <p className="text-brand-muted mt-2">Create your free account</p>
        </div>

        <div className="card space-y-5">
          <form onSubmit={handleSubmit(onSubmit)} className="space-y-5">
            {/* Role selector */}
            <div>
              <label className="label">I want to</label>
              <div className="grid grid-cols-3 gap-2">
                {(['client','owner','broker'] as const).map(r => (
                  <label key={r} className="cursor-pointer">
                    <input type="radio" {...register('role')} value={r} className="sr-only" />
                    <div className="border border-brand-border rounded-lg p-3 text-center text-sm capitalize hover:border-brand-amber transition-colors has-[:checked]:border-brand-amber has-[:checked]:text-brand-amber">
                      {r === 'client' ? '🚗 Rent/Buy' : r === 'owner' ? '🏭 List Assets' : '🤝 Broker'}
                    </div>
                  </label>
                ))}
              </div>
              {errors.role && <p className="text-red-400 text-xs mt-1">{errors.role.message}</p>}
            </div>

            <div className="grid grid-cols-2 gap-4">
              <div>
                <label className="label">Full Name</label>
                <input {...register('name')} className="input" placeholder="John Doe" />
                {errors.name && <p className="text-red-400 text-xs mt-1">{errors.name.message}</p>}
              </div>
              <div>
                <label className="label">Country</label>
                <select {...register('country')} className="input">
                  {[['KE','🇰🇪 Kenya'],['UG','🇺🇬 Uganda'],['TZ','🇹🇿 Tanzania'],['NG','🇳🇬 Nigeria'],['GH','🇬🇭 Ghana'],['ZA','🇿🇦 South Africa']].map(([v,l]) => (
                    <option key={v} value={v}>{l}</option>
                  ))}
                </select>
              </div>
            </div>

            <div>
              <label className="label">Email Address</label>
              <input type="email" {...register('email')} className="input" placeholder="you@example.com" />
              {errors.email && <p className="text-red-400 text-xs mt-1">{errors.email.message}</p>}
            </div>

            <div>
              <label className="label">Phone Number</label>
              <input {...register('phone')} className="input" placeholder="+254700000000" />
              {errors.phone && <p className="text-red-400 text-xs mt-1">{errors.phone.message}</p>}
            </div>

            <div className="grid grid-cols-2 gap-4">
              <div>
                <label className="label">Password</label>
                <input type="password" {...register('password')} className="input" placeholder="Min 8 chars" />
                {errors.password && <p className="text-red-400 text-xs mt-1">{errors.password.message}</p>}
              </div>
              <div>
                <label className="label">Confirm Password</label>
                <input type="password" {...register('password_confirmation')} className="input" placeholder="Repeat password" />
                {errors.password_confirmation && <p className="text-red-400 text-xs mt-1">{errors.password_confirmation.message}</p>}
              </div>
            </div>

            <div>
              <label className="label">Referral Code (optional)</label>
              <input {...register('referral_code')} className="input" placeholder="Enter code if you have one" />
            </div>

            <button type="submit" disabled={loading} className="btn-primary w-full">
              {loading ? 'Creating account...' : 'Create Account'}
            </button>
          </form>

          <p className="text-center text-brand-muted text-sm">
            Already have an account?{' '}
            <Link href="/login" className="text-brand-amber hover:underline">Sign in</Link>
          </p>
        </div>
      </div>
    </div>
  )
}
