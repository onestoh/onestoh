'use client'
import { useState, useEffect } from 'react'
import Link from 'next/link'
import { useAuthStore } from '@/lib/store'
import api from '@/lib/api'
import type { Booking, Wallet } from '@/lib/types'
import { Badge } from '@/components/ui/Badge'
import { Calendar, Wallet as WalletIcon, Clock, CheckCircle2, AlertCircle, Car } from 'lucide-react'
import { format } from 'date-fns'

function BookingStatusBadge({ status }: { status: Booking['status'] }) {
  if (['confirmed', 'owner_notified', 'client_prepared'].includes(status))
    return <Badge variant="blue">Confirmed</Badge>
  if (status === 'active') return <Badge variant="green">Active</Badge>
  if (status === 'completed' || status === 'closed') return <Badge variant="muted">Completed</Badge>
  if (status.startsWith('cancelled')) return <Badge variant="red">Cancelled</Badge>
  if (status === 'disputed') return <Badge variant="red">Disputed</Badge>
  return <Badge variant="amber">Pending</Badge>
}

export default function ClientDashboard() {
  const { user } = useAuthStore()
  const [bookings, setBookings] = useState<Booking[]>([])
  const [wallet, setWallet] = useState<Wallet | null>(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    Promise.all([
      api.get('/api/bookings?per_page=5'),
      api.get('/api/wallet'),
    ])
      .then(([bookingsRes, walletRes]) => {
        setBookings(bookingsRes.data.data || [])
        setWallet(walletRes.data.data)
      })
      .catch(() => {})
      .finally(() => setLoading(false))
  }, [])

  const upcoming = bookings.filter((b) =>
    ['confirmed', 'owner_notified', 'client_prepared', 'active'].includes(b.status)
  )
  const past = bookings.filter((b) =>
    ['completed', 'closed', 'cancelled_by_client', 'cancelled_by_admin'].includes(b.status)
  )

  return (
    <div>
      <div className="mb-8">
        <h1 className="text-2xl font-serif font-semibold text-[#DCE5F2]">
          Welcome back, {user?.name.split(' ')[0]}
        </h1>
        <p className="text-sm text-[#7088A8] mt-1">Manage your rentals and account</p>
      </div>

      {/* Stats cards */}
      <div className="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
        <div className="bg-[#141D2B] border border-white/5 rounded-xl p-5">
          <div className="flex items-center gap-3 mb-3">
            <div className="w-10 h-10 rounded-xl bg-[#E8922A]/10 flex items-center justify-center">
              <Calendar className="w-5 h-5 text-[#E8922A]" />
            </div>
            <p className="text-sm text-[#7088A8]">Upcoming</p>
          </div>
          <p className="text-3xl font-bold font-mono text-[#DCE5F2]">{upcoming.length}</p>
          <p className="text-xs text-[#7088A8] mt-1">Active bookings</p>
        </div>
        <div className="bg-[#141D2B] border border-white/5 rounded-xl p-5">
          <div className="flex items-center gap-3 mb-3">
            <div className="w-10 h-10 rounded-xl bg-[#2ECC8A]/10 flex items-center justify-center">
              <CheckCircle2 className="w-5 h-5 text-[#2ECC8A]" />
            </div>
            <p className="text-sm text-[#7088A8]">Completed</p>
          </div>
          <p className="text-3xl font-bold font-mono text-[#DCE5F2]">{past.length}</p>
          <p className="text-xs text-[#7088A8] mt-1">Past rentals</p>
        </div>
        <div className="bg-[#141D2B] border border-white/5 rounded-xl p-5">
          <div className="flex items-center gap-3 mb-3">
            <div className="w-10 h-10 rounded-xl bg-[#4A9FE0]/10 flex items-center justify-center">
              <WalletIcon className="w-5 h-5 text-[#4A9FE0]" />
            </div>
            <p className="text-sm text-[#7088A8]">Wallet</p>
          </div>
          <p className="text-3xl font-bold font-mono text-[#DCE5F2]">
            KES {wallet ? Number(wallet.balance).toLocaleString() : '—'}
          </p>
          <p className="text-xs text-[#7088A8] mt-1">Available balance</p>
        </div>
      </div>

      {/* Upcoming bookings */}
      <div className="mb-8">
        <div className="flex items-center justify-between mb-4">
          <h2 className="font-semibold text-[#DCE5F2]">Upcoming Bookings</h2>
          <Link href="/client/bookings" className="text-sm text-[#E8922A] hover:text-[#F5B050]">View all</Link>
        </div>
        {loading ? (
          <div className="space-y-3">
            {[1, 2].map((i) => <div key={i} className="h-20 bg-[#141D2B] rounded-xl animate-pulse" />)}
          </div>
        ) : upcoming.length === 0 ? (
          <div className="bg-[#141D2B] border border-white/5 rounded-xl p-8 text-center">
            <Car className="w-10 h-10 text-[#7088A8] mx-auto mb-3" />
            <p className="text-[#DCE5F2] font-medium">No upcoming bookings</p>
            <p className="text-sm text-[#7088A8] mt-1">Browse our marketplace to find your next rental</p>
            <Link
              href="/"
              className="inline-block mt-4 bg-[#E8922A] text-[#080C12] font-semibold px-5 py-2 rounded-lg hover:bg-[#F5B050] transition-colors text-sm"
            >
              Browse Listings
            </Link>
          </div>
        ) : (
          <div className="space-y-3">
            {upcoming.map((booking) => (
              <div key={booking.id} className="bg-[#141D2B] border border-white/5 rounded-xl p-4 flex items-center justify-between gap-4">
                <div className="flex items-center gap-3">
                  <div className="w-10 h-10 rounded-xl bg-[#E8922A]/10 flex items-center justify-center">
                    <Car className="w-5 h-5 text-[#E8922A]" />
                  </div>
                  <div>
                    <p className="text-sm font-medium text-[#DCE5F2]">
                      {booking.asset ? `${booking.asset.year} ${booking.asset.make} ${booking.asset.model}` : `Booking #${booking.id.slice(0, 8)}`}
                    </p>
                    <p className="text-xs text-[#7088A8] font-mono">
                      {format(new Date(booking.start_at), 'dd MMM')} – {format(new Date(booking.end_at), 'dd MMM yyyy')}
                    </p>
                  </div>
                </div>
                <div className="flex items-center gap-3">
                  <BookingStatusBadge status={booking.status} />
                  <span className="text-sm font-mono font-bold text-[#E8922A]">
                    KES {Number(booking.total_amount).toLocaleString()}
                  </span>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>

      {/* Past bookings */}
      <div>
        <div className="flex items-center justify-between mb-4">
          <h2 className="font-semibold text-[#DCE5F2]">Recent History</h2>
        </div>
        {past.length === 0 ? (
          <p className="text-sm text-[#7088A8]">No past rentals yet.</p>
        ) : (
          <div className="space-y-3">
            {past.slice(0, 3).map((booking) => (
              <div key={booking.id} className="bg-[#141D2B] border border-white/5 rounded-xl p-4 flex items-center justify-between gap-4 opacity-70">
                <div className="flex items-center gap-3">
                  <Clock className="w-5 h-5 text-[#7088A8]" />
                  <div>
                    <p className="text-sm font-medium text-[#DCE5F2]">
                      {booking.asset ? `${booking.asset.make} ${booking.asset.model}` : `Booking #${booking.id.slice(0, 8)}`}
                    </p>
                    <p className="text-xs text-[#7088A8] font-mono">
                      {format(new Date(booking.start_at), 'dd MMM yyyy')}
                    </p>
                  </div>
                </div>
                <BookingStatusBadge status={booking.status} />
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  )
}
