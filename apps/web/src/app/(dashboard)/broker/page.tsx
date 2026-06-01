'use client'
import { useState, useEffect } from 'react'
import { useAuthStore } from '@/lib/store'
import api from '@/lib/api'
import { Copy, Check, TrendingUp, Users, DollarSign, Link2 } from 'lucide-react'
import toast from 'react-hot-toast'

interface BrokerStats {
  total_referrals: number
  clicks: number
  conversions: number
  total_commission: string
  pending_commission: string
  paid_commission: string
}

export default function BrokerDashboard() {
  const { user } = useAuthStore()
  const [stats, setStats] = useState<BrokerStats | null>(null)
  const [loading, setLoading] = useState(true)
  const [copied, setCopied] = useState(false)

  const referralLink = `${typeof window !== 'undefined' ? window.location.origin : 'https://theonlineyard.co.ke'}/ref/${user?.referral_code}`

  useEffect(() => {
    api.get('/api/broker/stats')
      .then((res) => setStats(res.data.data))
      .catch(() => {})
      .finally(() => setLoading(false))
  }, [])

  const copyLink = () => {
    navigator.clipboard.writeText(referralLink)
    setCopied(true)
    toast.success('Referral link copied!')
    setTimeout(() => setCopied(false), 2000)
  }

  return (
    <div>
      <div className="mb-8">
        <h1 className="text-2xl font-serif font-semibold text-[#DCE5F2]">
          Broker Dashboard
        </h1>
        <p className="text-sm text-[#7088A8] mt-1">Track your referrals and commissions</p>
      </div>

      {/* Referral link */}
      <div className="bg-gradient-to-r from-[#E8922A]/10 to-[#F5B050]/5 border border-[#E8922A]/20 rounded-2xl p-6 mb-8">
        <div className="flex items-center gap-2 mb-3">
          <Link2 className="w-5 h-5 text-[#E8922A]" />
          <h2 className="font-semibold text-[#DCE5F2]">Your Referral Link</h2>
        </div>
        <p className="text-sm text-[#7088A8] mb-4">
          Share this link with clients. You earn a commission on every confirmed booking.
        </p>
        <div className="flex gap-2">
          <div className="flex-1 bg-[#0B1018] border border-white/10 rounded-lg px-4 py-2.5 text-sm font-mono text-[#DCE5F2] truncate">
            {referralLink}
          </div>
          <button
            onClick={copyLink}
            className="flex items-center gap-2 px-4 py-2.5 bg-[#E8922A] text-[#080C12] font-semibold rounded-lg hover:bg-[#F5B050] transition-colors text-sm shrink-0"
          >
            {copied ? <Check className="w-4 h-4" /> : <Copy className="w-4 h-4" />}
            {copied ? 'Copied!' : 'Copy'}
          </button>
        </div>
        {user?.referral_code && (
          <p className="text-xs text-[#7088A8] mt-2 font-mono">
            Code: <span className="text-[#E8922A]">{user.referral_code}</span>
          </p>
        )}
      </div>

      {/* Stats grid */}
      <div className="grid grid-cols-2 md:grid-cols-3 gap-5 mb-8">
        {[
          { icon: <Users className="w-5 h-5" />, value: stats?.total_referrals ?? 0, label: 'Total Referrals', color: 'text-[#E8922A]', bg: 'bg-[#E8922A]/10' },
          { icon: <TrendingUp className="w-5 h-5" />, value: stats?.clicks ?? 0, label: 'Link Clicks', color: 'text-[#4A9FE0]', bg: 'bg-[#4A9FE0]/10' },
          { icon: <Check className="w-5 h-5" />, value: stats?.conversions ?? 0, label: 'Conversions', color: 'text-[#2ECC8A]', bg: 'bg-[#2ECC8A]/10' },
          { icon: <DollarSign className="w-5 h-5" />, value: `KES ${Number(stats?.total_commission ?? 0).toLocaleString()}`, label: 'Total Commission', color: 'text-[#E8922A]', bg: 'bg-[#E8922A]/10' },
          { icon: <DollarSign className="w-5 h-5" />, value: `KES ${Number(stats?.pending_commission ?? 0).toLocaleString()}`, label: 'Pending Payout', color: 'text-[#F5B050]', bg: 'bg-amber-500/10' },
          { icon: <DollarSign className="w-5 h-5" />, value: `KES ${Number(stats?.paid_commission ?? 0).toLocaleString()}`, label: 'Paid Out', color: 'text-[#2ECC8A]', bg: 'bg-[#2ECC8A]/10' },
        ].map((stat) => (
          <div key={stat.label} className="bg-[#141D2B] border border-white/5 rounded-xl p-5">
            <div className={`w-10 h-10 rounded-xl ${stat.bg} flex items-center justify-center ${stat.color} mb-3`}>
              {stat.icon}
            </div>
            <p className="text-xl font-bold font-mono text-[#DCE5F2]">{loading ? '—' : stat.value}</p>
            <p className="text-xs text-[#7088A8] mt-1">{stat.label}</p>
          </div>
        ))}
      </div>

      {/* Commission rate info */}
      <div className="bg-[#141D2B] border border-white/5 rounded-xl p-6">
        <h2 className="font-semibold text-[#DCE5F2] mb-4">Commission Structure</h2>
        <div className="space-y-3">
          {[
            { tier: 'Standard', rate: '3%', desc: 'Per confirmed booking via your link', color: 'text-[#4A9FE0]' },
            { tier: 'Licensed Broker', rate: '5%', desc: 'NTSA-licensed brokers get higher rates', color: 'text-[#E8922A]' },
            { tier: 'Top Performer', rate: '7%', desc: '50+ conversions/month bonus tier', color: 'text-[#2ECC8A]' },
          ].map((tier) => (
            <div key={tier.tier} className="flex items-center justify-between py-3 border-b border-white/5 last:border-0">
              <div>
                <p className="text-sm font-medium text-[#DCE5F2]">{tier.tier}</p>
                <p className="text-xs text-[#7088A8]">{tier.desc}</p>
              </div>
              <span className={`text-lg font-bold font-mono ${tier.color}`}>{tier.rate}</span>
            </div>
          ))}
        </div>
        <p className="text-xs text-[#7088A8] mt-4">
          Commissions are paid out automatically via M-Pesa within 24 hours of booking completion.
        </p>
      </div>
    </div>
  )
}
