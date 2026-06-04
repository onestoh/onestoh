'use client'
import { useState } from 'react'
import { useRouter } from 'next/navigation'
import toast from 'react-hot-toast'
import api from '@/lib/api'
import { useAuthStore } from '@/store/auth'

export default function VerifyOtpPage() {
  const router = useRouter()
  const [otp, setOtp] = useState('')
  const [loading, setLoading] = useState(false)
  const updateUser = useAuthStore((s) => s.updateUser)

  const handleVerify = async (e: React.FormEvent) => {
    e.preventDefault()
    if (otp.length !== 6) return toast.error('Enter 6-digit OTP')
    setLoading(true)
    try {
      const res = await api.post('/auth/verify-otp', { otp })
      toast.success(res.data.message)
      const meRes = await api.get('/auth/me')
      updateUser(meRes.data.data)
      router.push('/dashboard')
    } catch (err: any) {
      toast.error(err.response?.data?.message || 'Invalid OTP')
    } finally {
      setLoading(false)
    }
  }

  const resend = async () => {
    try {
      await api.post('/auth/resend-otp')
      toast.success('OTP resent to your phone.')
    } catch {
      toast.error('Failed to resend OTP.')
    }
  }

  return (
    <div className="min-h-screen flex items-center justify-center px-4 bg-brand-dark">
      <div className="w-full max-w-sm card">
        <h1 className="text-2xl font-bold text-center mb-2">Verify Your Phone</h1>
        <p className="text-brand-muted text-center text-sm mb-6">Enter the 6-digit code sent to your phone.</p>

        <form onSubmit={handleVerify} className="space-y-4">
          <input
            value={otp}
            onChange={e => setOtp(e.target.value.replace(/\D/g,'').slice(0,6))}
            className="input text-center text-2xl tracking-[0.5em] font-mono"
            placeholder="000000"
            maxLength={6}
          />
          <button type="submit" disabled={loading} className="btn-primary w-full">
            {loading ? 'Verifying...' : 'Verify'}
          </button>
        </form>

        <button onClick={resend} className="w-full text-brand-amber text-sm mt-4 hover:underline">
          Resend OTP
        </button>
      </div>
    </div>
  )
}
