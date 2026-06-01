'use client'
import { useState, useEffect } from 'react'
import Link from 'next/link'
import { useAuthStore } from '@/lib/store'
import api from '@/lib/api'
import type { Asset, Booking, Wallet } from '@/lib/types'
import { Badge } from '@/components/ui/Badge'
import { Car, TrendingUp, Calendar, Wallet as WalletIcon, Plus, Eye } from 'lucide-react'

export default function OwnerDashboard() {
  const { user } = useAuthStore()
  const [assets, setAssets] = useState<Asset[]>([])
  const [bookings, setBookings] = useState<Booking[]>([])
  const [wallet, setWallet] = useState<Wallet | null>(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    Promise.all([
      api.get('/api/owner/assets?per_page=6'),
      api.get('/api/owner/bookings?per_page=5'),
      api.get('/api/wallet'),
    ])
      .then(([assetsRes, bookingsRes, walletRes]) => {
        setAssets(assetsRes.data.data || [])
        setBookings(bookingsRes.data.data || [])
        setWallet(walletRes.data.data)
      })
      .catch(() => {})
      .finally(() => setLoading(false))
  }, [])

  const activeAssets = assets.filter((a) => a.status === 'active')
  const pendingBookings = bookings.filter((b) =>
    ['pending_payment', 'payment_processing', 'confirmed', 'owner_notified'].includes(b.status)
  )

  return (
    <div>
      <div className="flex items-center justify-between mb-8">
        <div>
          <h1 className="text-2xl font-serif font-semibold text-[#DCE5F2]">
            {user?.company_name || user?.name?.split(' ')[0]}&apos;s Dashboard
          </h1>
          <p className="text-sm text-[#7088A8] mt-1">Manage your fleet and earnings</p>
        </div>
        <Link
          href="/owner/fleet/new"
          className="flex items-center gap-2 bg-[#E8922A] text-[#080C12] font-semibold px-4 py-2 rounded-lg hover:bg-[#F5B050] transition-colors text-sm"
        >
          <Plus className="w-4 h-4" /> List Asset
        </Link>
      </div>

      {/* Stats */}
      <div className="grid grid-cols-2 md:grid-cols-4 gap-5 mb-8">
        {[
          { icon: <Car className="w-5 h-5" />, value: assets.length, label: 'Total Assets', color: 'text-[#E8922A]', bg: 'bg-[#E8922A]/10' },
          { icon: <Eye className="w-5 h-5" />, value: activeAssets.length, label: 'Active Listings', color: 'text-[#2ECC8A]', bg: 'bg-[#2ECC8A]/10' },
          { icon: <Calendar className="w-5 h-5" />, value: pendingBookings.length, label: 'Pending Bookings', color: 'text-[#4A9FE0]', bg: 'bg-[#4A9FE0]/10' },
          { icon: <WalletIcon className="w-5 h-5" />, value: wallet ? `KES ${Number(wallet.balance).toLocaleString()}` : '—', label: 'Balance', color: 'text-[#E8922A]', bg: 'bg-[#E8922A]/10' },
        ].map((stat) => (
          <div key={stat.label} className="bg-[#141D2B] border border-white/5 rounded-xl p-5">
            <div className={`w-10 h-10 rounded-xl ${stat.bg} flex items-center justify-center ${stat.color} mb-3`}>
              {stat.icon}
            </div>
            <p className="text-2xl font-bold font-mono text-[#DCE5F2]">{stat.value}</p>
            <p className="text-xs text-[#7088A8] mt-1">{stat.label}</p>
          </div>
        ))}
      </div>

      {/* Fleet overview */}
      <div className="mb-8">
        <div className="flex items-center justify-between mb-4">
          <h2 className="font-semibold text-[#DCE5F2]">Your Fleet</h2>
          <Link href="/owner/fleet" className="text-sm text-[#E8922A] hover:text-[#F5B050]">View all</Link>
        </div>
        {loading ? (
          <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            {[1,2,3].map((i) => <div key={i} className="h-24 bg-[#141D2B] rounded-xl animate-pulse" />)}
          </div>
        ) : assets.length === 0 ? (
          <div className="bg-[#141D2B] border border-white/5 rounded-xl p-8 text-center">
            <Car className="w-10 h-10 text-[#7088A8] mx-auto mb-3" />
            <p className="text-[#DCE5F2] font-medium">No assets listed yet</p>
            <Link href="/owner/fleet/new" className="inline-block mt-4 bg-[#E8922A] text-[#080C12] font-semibold px-5 py-2 rounded-lg text-sm hover:bg-[#F5B050] transition-colors">
              List Your First Asset
            </Link>
          </div>
        ) : (
          <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            {assets.map((asset) => (
              <div key={asset.id} className="bg-[#141D2B] border border-white/5 rounded-xl p-4">
                <div className="flex items-center justify-between mb-2">
                  <p className="text-sm font-medium text-[#DCE5F2] truncate">{asset.make} {asset.model}</p>
                  {asset.status === 'active' ? (
                    <Badge variant="green">Live</Badge>
                  ) : (
                    <Badge variant="muted">{asset.status.replace(/_/g, ' ')}</Badge>
                  )}
                </div>
                <p className="text-xs text-[#7088A8] capitalize">{asset.category.replace(/_/g, ' ')}</p>
                <p className="text-xs font-mono text-[#E8922A] mt-2">
                  {asset.daily_rate ? `KES ${Number(asset.daily_rate).toLocaleString()}/day` : 'Rate not set'}
                </p>
              </div>
            ))}
          </div>
        )}
      </div>

      {/* Recent bookings */}
      <div>
        <div className="flex items-center justify-between mb-4">
          <h2 className="font-semibold text-[#DCE5F2]">Recent Bookings</h2>
          <Link href="/owner/bookings" className="text-sm text-[#E8922A] hover:text-[#F5B050]">View all</Link>
        </div>
        {bookings.length === 0 ? (
          <p className="text-sm text-[#7088A8]">No bookings received yet.</p>
        ) : (
          <div className="space-y-3">
            {bookings.slice(0, 5).map((booking) => (
              <div key={booking.id} className="bg-[#141D2B] border border-white/5 rounded-xl p-4 flex items-center justify-between gap-4">
                <div>
                  <p className="text-sm font-medium text-[#DCE5F2]">
                    {booking.asset ? `${booking.asset.make} ${booking.asset.model}` : `Booking #${booking.id.slice(0, 8)}`}
                  </p>
                  <p className="text-xs text-[#7088A8]">
                    {booking.client?.name || 'Client'} &bull; {booking.rental_type.replace(/_/g, ' ')}
                  </p>
                </div>
                <div className="flex items-center gap-3">
                  <span className="text-sm font-mono font-bold text-[#E8922A]">
                    KES {Number(booking.total_amount).toLocaleString()}
                  </span>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  )
}
