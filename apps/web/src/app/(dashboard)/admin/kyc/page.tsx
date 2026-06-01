'use client'
import { useState, useEffect } from 'react'
import api from '@/lib/api'
import { Badge } from '@/components/ui/Badge'
import { CheckCircle2, XCircle, Eye, Clock, Search } from 'lucide-react'
import toast from 'react-hot-toast'
import { format } from 'date-fns'

interface KycSubmission {
  id: number
  user_id: number
  user: {
    id: number
    name: string
    email: string
    phone: string
    role: string
  }
  id_type: 'national_id' | 'passport' | 'driving_licence'
  id_front_url: string
  id_back_url: string | null
  selfie_url: string
  status: 'pending' | 'approved' | 'rejected'
  rejection_reason: string | null
  submitted_at: string
  reviewed_at: string | null
}

export default function AdminKycPage() {
  const [submissions, setSubmissions] = useState<KycSubmission[]>([])
  const [loading, setLoading] = useState(true)
  const [search, setSearch] = useState('')
  const [filter, setFilter] = useState<'all' | 'pending' | 'approved' | 'rejected'>('pending')
  const [reviewing, setReviewing] = useState<KycSubmission | null>(null)
  const [rejectReason, setRejectReason] = useState('')
  const [actionLoading, setActionLoading] = useState(false)

  useEffect(() => {
    fetchSubmissions()
  }, [filter])

  const fetchSubmissions = async () => {
    setLoading(true)
    try {
      const params: Record<string, string> = {}
      if (filter !== 'all') params.status = filter
      const res = await api.get('/api/admin/kyc', { params })
      setSubmissions(res.data.data || [])
    } catch {
      setSubmissions([])
    } finally {
      setLoading(false)
    }
  }

  const handleApprove = async (id: number) => {
    setActionLoading(true)
    try {
      await api.post(`/api/admin/kyc/${id}/approve`)
      toast.success('KYC approved')
      setReviewing(null)
      fetchSubmissions()
    } catch {
      toast.error('Failed to approve')
    } finally {
      setActionLoading(false)
    }
  }

  const handleReject = async (id: number) => {
    if (!rejectReason.trim()) { toast.error('Provide a rejection reason'); return }
    setActionLoading(true)
    try {
      await api.post(`/api/admin/kyc/${id}/reject`, { reason: rejectReason })
      toast.success('KYC rejected')
      setReviewing(null)
      setRejectReason('')
      fetchSubmissions()
    } catch {
      toast.error('Failed to reject')
    } finally {
      setActionLoading(false)
    }
  }

  const filtered = submissions.filter(
    (s) =>
      s.user.name.toLowerCase().includes(search.toLowerCase()) ||
      s.user.email.toLowerCase().includes(search.toLowerCase()) ||
      s.user.phone.includes(search)
  )

  return (
    <div>
      <div className="mb-8">
        <h1 className="text-2xl font-serif font-semibold text-[#DCE5F2]">KYC Review Queue</h1>
        <p className="text-sm text-[#7088A8] mt-1">Verify identity documents for new users</p>
      </div>

      {/* Filters */}
      <div className="flex flex-col sm:flex-row gap-3 mb-6">
        <div className="relative flex-1">
          <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[#7088A8]" />
          <input
            type="text"
            placeholder="Search by name, email, or phone..."
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            className="w-full bg-[#141D2B] border border-white/10 rounded-lg pl-10 pr-4 py-2.5 text-sm text-[#DCE5F2] placeholder:text-[#7088A8] outline-none focus:border-[#E8922A]/50"
          />
        </div>
        <div className="flex gap-2">
          {(['all', 'pending', 'approved', 'rejected'] as const).map((f) => (
            <button
              key={f}
              onClick={() => setFilter(f)}
              className={`px-3 py-2 rounded-lg text-xs font-mono capitalize transition-colors ${
                filter === f ? 'bg-[#E8922A] text-[#080C12]' : 'bg-[#141D2B] text-[#7088A8] hover:text-[#DCE5F2]'
              }`}
            >
              {f}
            </button>
          ))}
        </div>
      </div>

      {/* Table */}
      {loading ? (
        <div className="space-y-3">
          {[1,2,3,4].map((i) => <div key={i} className="h-16 bg-[#141D2B] rounded-xl animate-pulse" />)}
        </div>
      ) : filtered.length === 0 ? (
        <div className="text-center py-16">
          <Clock className="w-10 h-10 text-[#7088A8] mx-auto mb-3" />
          <p className="text-[#DCE5F2] font-medium">No submissions found</p>
        </div>
      ) : (
        <div className="space-y-3">
          {filtered.map((sub) => (
            <div key={sub.id} className="bg-[#141D2B] border border-white/5 rounded-xl p-4 flex items-center justify-between gap-4">
              <div className="flex items-center gap-4">
                <div className="w-10 h-10 rounded-full bg-[#E8922A]/20 flex items-center justify-center text-[#E8922A] font-bold">
                  {sub.user.name[0].toUpperCase()}
                </div>
                <div>
                  <p className="text-sm font-medium text-[#DCE5F2]">{sub.user.name}</p>
                  <p className="text-xs text-[#7088A8]">{sub.user.email} &bull; {sub.user.phone}</p>
                  <p className="text-xs text-[#7088A8] capitalize">
                    {sub.user.role.replace(/_/g, ' ')} &bull; {sub.id_type.replace(/_/g, ' ')}
                  </p>
                </div>
              </div>
              <div className="flex items-center gap-3">
                <div className="text-right">
                  <p className="text-xs text-[#7088A8] font-mono">
                    {format(new Date(sub.submitted_at), 'dd MMM yyyy, HH:mm')}
                  </p>
                  {sub.status === 'pending' && <Badge variant="amber"><Clock className="w-3 h-3" /> Pending</Badge>}
                  {sub.status === 'approved' && <Badge variant="green"><CheckCircle2 className="w-3 h-3" /> Approved</Badge>}
                  {sub.status === 'rejected' && <Badge variant="red"><XCircle className="w-3 h-3" /> Rejected</Badge>}
                </div>
                {sub.status === 'pending' && (
                  <button
                    onClick={() => setReviewing(sub)}
                    className="flex items-center gap-1.5 px-3 py-1.5 bg-[#E8922A]/10 text-[#E8922A] text-xs rounded-lg hover:bg-[#E8922A]/20 transition-colors"
                  >
                    <Eye className="w-3.5 h-3.5" /> Review
                  </button>
                )}
              </div>
            </div>
          ))}
        </div>
      )}

      {/* Review modal */}
      {reviewing && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div className="absolute inset-0 bg-black/70 backdrop-blur-sm" onClick={() => setReviewing(null)} />
          <div className="relative w-full max-w-2xl bg-[#141D2B] border border-white/10 rounded-2xl shadow-2xl overflow-hidden">
            <div className="px-6 py-4 border-b border-white/5 flex items-center justify-between">
              <h2 className="font-semibold text-[#DCE5F2]">Review: {reviewing.user.name}</h2>
              <button onClick={() => setReviewing(null)} className="text-[#7088A8] hover:text-[#DCE5F2]">✕</button>
            </div>
            <div className="p-6 space-y-5">
              <div className="grid grid-cols-2 gap-3">
                <div>
                  <p className="text-xs text-[#7088A8] mb-1">ID Front</p>
                  {/* eslint-disable-next-line @next/next/no-img-element */}
                  <img src={reviewing.id_front_url} alt="ID front" className="w-full h-40 object-cover rounded-lg bg-[#0B1018]" />
                </div>
                {reviewing.id_back_url && (
                  <div>
                    <p className="text-xs text-[#7088A8] mb-1">ID Back</p>
                    {/* eslint-disable-next-line @next/next/no-img-element */}
                    <img src={reviewing.id_back_url} alt="ID back" className="w-full h-40 object-cover rounded-lg bg-[#0B1018]" />
                  </div>
                )}
                <div>
                  <p className="text-xs text-[#7088A8] mb-1">Selfie</p>
                  {/* eslint-disable-next-line @next/next/no-img-element */}
                  <img src={reviewing.selfie_url} alt="Selfie" className="w-full h-40 object-cover rounded-lg bg-[#0B1018]" />
                </div>
              </div>

              <div>
                <label className="block text-xs font-mono tracking-widest uppercase text-[#E8922A] mb-2">
                  Rejection reason (if rejecting)
                </label>
                <textarea
                  value={rejectReason}
                  onChange={(e) => setRejectReason(e.target.value)}
                  placeholder="e.g. ID is expired, image blurry, selfie doesn't match ID..."
                  rows={3}
                  className="w-full bg-[#0B1018] border border-white/10 rounded-lg px-4 py-2.5 text-sm text-[#DCE5F2] placeholder:text-[#7088A8] outline-none focus:border-[#E8922A]/50 resize-none"
                />
              </div>

              <div className="flex gap-3">
                <button
                  onClick={() => handleReject(reviewing.id)}
                  disabled={actionLoading}
                  className="flex-1 flex items-center justify-center gap-2 py-3 bg-[#E05252]/10 border border-[#E05252]/30 text-[#E05252] rounded-xl hover:bg-[#E05252] hover:text-white transition-colors font-medium disabled:opacity-50"
                >
                  <XCircle className="w-4 h-4" /> Reject
                </button>
                <button
                  onClick={() => handleApprove(reviewing.id)}
                  disabled={actionLoading}
                  className="flex-1 flex items-center justify-center gap-2 py-3 bg-[#2ECC8A]/10 border border-[#2ECC8A]/30 text-[#2ECC8A] rounded-xl hover:bg-[#2ECC8A] hover:text-[#080C12] transition-colors font-medium disabled:opacity-50"
                >
                  <CheckCircle2 className="w-4 h-4" /> Approve
                </button>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  )
}
