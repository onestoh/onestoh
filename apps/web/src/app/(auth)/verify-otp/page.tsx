'use client'
import { useState, useRef } from 'react'
import { useRouter } from 'next/navigation'
import { useAuthStore } from '@/lib/store'
import api from '@/lib/api'
import toast from 'react-hot-toast'
import { MessageSquare, Mail } from 'lucide-react'

export default function VerifyOtpPage() {
  const router = useRouter()
  const { user } = useAuthStore()
  const [otpType, setOtpType] = useState<'phone' | 'email'>('phone')
  const [otp, setOtp] = useState(['', '', '', '', '', ''])
  const [loading, setLoading] = useState(false)
  const [resending, setResending] = useState(false)
  const refs = useRef<(HTMLInputElement | null)[]>([])

  const handleChange = (i: number, val: string) => {
    if (!/^[0-9]?$/.test(val)) return
    const next = [...otp]
    next[i] = val
    setOtp(next)
    if (val && i < 5) refs.current[i + 1]?.focus()
  }

  const handleKeyDown = (i: number, e: React.KeyboardEvent) => {
    if (e.key === 'Backspace' && !otp[i] && i > 0) {
      refs.current[i - 1]?.focus()
    }
  }

  const handlePaste = (e: React.ClipboardEvent) => {
    const text = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6)
    if (text.length === 6) {
      setOtp(text.split(''))
    }
  }

  const handleVerify = async () => {
    const code = otp.join('')
    if (code.length < 6) { toast.error('Enter the 6-digit code'); return }
    setLoading(true)
    try {
      await api.post('/api/auth/verify-otp', { otp: code, type: otpType })
      toast.success('Verified! Please complete KYC.')
      router.push('/kyc')
    } catch (err: unknown) {
      const msg =
        err && typeof err === 'object' && 'response' in err
          ? (err as { response?: { data?: { message?: string } } }).response?.data?.message
          : undefined
      toast.error(msg || 'Invalid code')
    } finally {
      setLoading(false)
    }
  }

  const handleResend = async () => {
    setResending(true)
    try {
      await api.post('/api/auth/resend-otp', { type: otpType })
      toast.success('New code sent')
    } catch {
      toast.error('Failed to resend')
    } finally {
      setResending(false)
    }
  }

  return (
    <div className="text-center">
      <div className="w-16 h-16 rounded-full bg-[#E8922A]/10 flex items-center justify-center mx-auto mb-6">
        <MessageSquare className="w-8 h-8 text-[#E8922A]" />
      </div>
      <h1 className="text-2xl font-serif font-semibold text-[#DCE5F2] mb-1">Verify your account</h1>
      <p className="text-sm text-[#7088A8] mb-6">
        We sent a 6-digit code to {otpType === 'phone' ? user?.phone : user?.email}
      </p>

      {/* Toggle */}
      <div className="flex gap-2 bg-[#141D2B] rounded-xl p-1 mb-8">
        {(['phone', 'email'] as const).map((t) => (
          <button
            key={t}
            onClick={() => setOtpType(t)}
            className={`flex-1 flex items-center justify-center gap-2 py-2 rounded-lg text-sm font-medium transition-colors ${
              otpType === t ? 'bg-[#E8922A] text-[#080C12]' : 'text-[#7088A8] hover:text-[#DCE5F2]'
            }`}
          >
            {t === 'phone' ? <MessageSquare className="w-4 h-4" /> : <Mail className="w-4 h-4" />}
            {t === 'phone' ? 'SMS' : 'Email'}
          </button>
        ))}
      </div>

      {/* OTP inputs */}
      <div className="flex gap-3 justify-center mb-8" onPaste={handlePaste}>
        {otp.map((digit, i) => (
          <input
            key={i}
            ref={(el) => { refs.current[i] = el }}
            type="text"
            inputMode="numeric"
            maxLength={1}
            value={digit}
            onChange={(e) => handleChange(i, e.target.value)}
            onKeyDown={(e) => handleKeyDown(i, e)}
            className="w-12 h-14 text-center text-xl font-bold font-mono bg-[#141D2B] border border-white/10 rounded-xl text-[#DCE5F2] focus:outline-none focus:border-[#E8922A]/50 transition-colors"
          />
        ))}
      </div>

      <button
        onClick={handleVerify}
        disabled={loading || otp.join('').length < 6}
        className="w-full bg-[#E8922A] text-[#080C12] font-semibold py-3 rounded-lg hover:bg-[#F5B050] transition-colors disabled:opacity-50 mb-4"
      >
        {loading ? 'Verifying...' : 'Verify Code'}
      </button>

      <button
        onClick={handleResend}
        disabled={resending}
        className="text-sm text-[#7088A8] hover:text-[#E8922A] transition-colors disabled:opacity-50"
      >
        {resending ? 'Sending...' : "Didn't receive it? Resend"}
      </button>
    </div>
  )
}
