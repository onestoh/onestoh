'use client'
import { useState } from 'react'
import { useRouter } from 'next/navigation'
import toast from 'react-hot-toast'
import api from '@/lib/api'
import { useAuthStore } from '@/store/auth'
import { Shield, Upload, CheckCircle } from 'lucide-react'

export default function KycPage() {
  const router = useRouter()
  const { user, updateUser } = useAuthStore()
  const [form, setForm] = useState({ document_type: 'national_id', document_number: '', country: user?.country || 'KE' })
  const [file, setFile] = useState<File | null>(null)
  const [loading, setLoading] = useState(false)

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    if (!file) return toast.error('Please upload your document')

    const formData = new FormData()
    Object.entries(form).forEach(([k, v]) => formData.append(k, v))
    formData.append('document_file', file)

    setLoading(true)
    try {
      await api.post('/kyc/submit', formData, { headers: { 'Content-Type': 'multipart/form-data' }})
      const meRes = await api.get('/auth/me')
      updateUser(meRes.data.data)
      toast.success('KYC submitted! Usually verified within 24 hours.')
      router.push('/dashboard')
    } catch (err: any) {
      toast.error(err.response?.data?.message || 'KYC submission failed.')
    } finally {
      setLoading(false)
    }
  }

  if (user?.kyc_status === 'approved') {
    return (
      <div className="min-h-screen flex items-center justify-center bg-brand-dark">
        <div className="card text-center max-w-sm">
          <CheckCircle size={64} className="text-green-400 mx-auto mb-4" />
          <h2 className="text-2xl font-bold mb-2">KYC Verified!</h2>
          <p className="text-brand-muted">Your identity has been verified. You can now book and pay.</p>
        </div>
      </div>
    )
  }

  return (
    <div className="min-h-screen flex items-center justify-center px-4 bg-brand-dark">
      <div className="w-full max-w-lg card">
        <div className="flex items-center gap-3 mb-6">
          <Shield size={32} className="text-brand-amber" />
          <div>
            <h1 className="text-2xl font-bold">KYC Verification</h1>
            <p className="text-brand-muted text-sm">Required to book and make payments</p>
          </div>
        </div>

        <form onSubmit={handleSubmit} className="space-y-5">
          <div>
            <label className="label">Document Type</label>
            <select className="input" value={form.document_type} onChange={e => setForm(f => ({...f, document_type: e.target.value}))}>
              <option value="national_id">National ID</option>
              <option value="passport">Passport</option>
              <option value="drivers_license">Driver's License</option>
              <option value="business_reg">Business Registration</option>
            </select>
          </div>
          <div>
            <label className="label">Document Number</label>
            <input className="input" required placeholder="Enter document number" value={form.document_number} onChange={e => setForm(f => ({...f, document_number: e.target.value}))} />
          </div>
          <div>
            <label className="label">Country</label>
            <select className="input" value={form.country} onChange={e => setForm(f => ({...f, country: e.target.value}))}>
              {[['KE','Kenya'],['UG','Uganda'],['TZ','Tanzania'],['NG','Nigeria'],['GH','Ghana'],['ZA','South Africa']].map(([v,l]) => <option key={v} value={v}>{l}</option>)}
            </select>
          </div>
          <div>
            <label className="label">Upload Document (JPG, PNG or PDF, max 5MB)</label>
            <label className="border-2 border-dashed border-brand-border rounded-lg p-6 flex flex-col items-center cursor-pointer hover:border-brand-amber transition-colors">
              <Upload size={32} className="text-brand-muted mb-2" />
              <span className="text-brand-muted text-sm">{file ? file.name : 'Click to upload or drag & drop'}</span>
              <input type="file" className="sr-only" accept=".jpg,.jpeg,.png,.pdf" onChange={e => setFile(e.target.files?.[0] || null)} />
            </label>
          </div>
          <button type="submit" disabled={loading} className="btn-primary w-full">
            {loading ? 'Submitting...' : 'Submit KYC'}
          </button>
        </form>
      </div>
    </div>
  )
}
