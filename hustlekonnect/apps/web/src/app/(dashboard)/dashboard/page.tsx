'use client'
import { useEffect, useState } from 'react'
import Link from 'next/link'
import { useRouter } from 'next/navigation'
import toast from 'react-hot-toast'
import api from '@/lib/api'
import { useAuthStore } from '@/store/auth'
import { Booking, Notification } from '@/lib/types'
import { Car, CreditCard, Bell, LogOut, User, CheckCircle, AlertCircle } from 'lucide-react'

export default function DashboardPage() {
  const router = useRouter()
  const { user, logout } = useAuthStore()
  const [bookings, setBookings] = useState<Booking[]>([])
  const [notifications, setNotifications] = useState<Notification[]>([])
  const [walletBalance, setWalletBalance] = useState(0)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    if (!user) { router.push('/login'); return }
    Promise.all([
      api.get('/bookings').then(r => setBookings(r.data.data || [])),
      api.get('/notifications').then(r => setNotifications(r.data.data || [])),
      api.get('/wallet/balance').then(r => setWalletBalance(r.data.data?.balance || 0)),
    ]).catch(() => toast.error('Failed to load dashboard data')).finally(() => setLoading(false))
  }, [user, router])

  const handleLogout = async () => {
    try { await api.post('/auth/logout') } catch {}
    logout()
    router.push('/')
  }

  const statusColor = (s: string) => {
    const map: Record<string,string> = {
      pending_payment: 'text-yellow-400', confirmed: 'text-blue-400',
      active: 'text-green-400', completed: 'text-brand-amber',
      cancelled: 'text-red-400', closed: 'text-brand-muted',
    }
    return map[s] || 'text-brand-muted'
  }

  if (loading) return <div className="flex items-center justify-center min-h-screen"><div className="text-brand-muted">Loading...</div></div>

  return (
    <div className="min-h-screen bg-brand-dark">
      {/* Top nav */}
      <nav className="bg-brand-surface border-b border-brand-border px-4 py-3 flex justify-between items-center">
        <Link href="/" className="text-xl font-bold"><span className="text-brand-amber">Hustle</span>Konnect</Link>
        <div className="flex items-center gap-4">
          <span className="text-brand-muted text-sm">Hello, {user?.name?.split(' ')[0]}</span>
          {user?.kyc_status !== 'approved' && (
            <Link href="/kyc" className="text-xs bg-yellow-500/20 text-yellow-400 border border-yellow-500/30 px-3 py-1 rounded-full flex items-center gap-1">
              <AlertCircle size={12}/> Verify KYC
            </Link>
          )}
          <button onClick={handleLogout} className="text-brand-muted hover:text-white transition-colors">
            <LogOut size={20}/>
          </button>
        </div>
      </nav>

      <div className="max-w-6xl mx-auto px-4 py-8">
        {/* Stats */}
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
          {[
            { icon: Car, label: 'Total Bookings', value: bookings.length, color: 'text-blue-400' },
            { icon: CreditCard, label: 'Wallet Balance', value: `KES ${walletBalance.toLocaleString()}`, color: 'text-green-400' },
            { icon: Bell, label: 'Unread Notifications', value: notifications.filter(n => !n.read_at).length, color: 'text-brand-amber' },
            { icon: User, label: 'Trust Score', value: `${user?.trust_score ?? 0}%`, color: 'text-purple-400' },
          ].map(({ icon: Icon, label, value, color }) => (
            <div key={label} className="card">
              <Icon size={24} className={`${color} mb-3`} />
              <div className="text-2xl font-bold">{value}</div>
              <div className="text-brand-muted text-xs mt-1">{label}</div>
            </div>
          ))}
        </div>

        {/* KYC banner */}
        {user?.kyc_status === 'not_submitted' && (
          <div className="bg-yellow-500/10 border border-yellow-500/30 rounded-xl p-4 mb-6 flex justify-between items-center">
            <div>
              <p className="font-semibold text-yellow-400">Complete KYC Verification</p>
              <p className="text-sm text-brand-muted">Verify your identity to unlock bookings and payments.</p>
            </div>
            <Link href="/kyc" className="btn-primary text-sm">Verify Now</Link>
          </div>
        )}

        <div className="grid lg:grid-cols-3 gap-8">
          {/* Recent bookings */}
          <div className="lg:col-span-2">
            <div className="flex justify-between items-center mb-4">
              <h2 className="text-xl font-bold">Recent Bookings</h2>
              <Link href="/marketplace" className="text-brand-amber text-sm hover:underline">Browse More</Link>
            </div>
            {bookings.length === 0 ? (
              <div className="card text-center py-10">
                <Car size={48} className="text-brand-muted mx-auto mb-4" />
                <p className="text-brand-muted">No bookings yet.</p>
                <Link href="/marketplace" className="btn-primary mt-4 inline-block">Find a Vehicle</Link>
              </div>
            ) : (
              <div className="space-y-3">
                {bookings.slice(0, 5).map(b => (
                  <div key={b.id} className="card flex justify-between items-center">
                    <div>
                      <p className="font-semibold">{b.asset?.title ?? 'Vehicle'}</p>
                      <p className="text-brand-muted text-xs">{b.start_date} → {b.end_date}</p>
                    </div>
                    <div className="text-right">
                      <p className={`text-sm font-medium capitalize ${statusColor(b.status)}`}>{b.status.replace(/_/g,' ')}</p>
                      <p className="text-brand-muted text-xs">KES {b.total_amount_kes?.toLocaleString()}</p>
                    </div>
                  </div>
                ))}
              </div>
            )}
          </div>

          {/* Notifications */}
          <div>
            <h2 className="text-xl font-bold mb-4">Notifications</h2>
            {notifications.length === 0 ? (
              <div className="card text-center py-8">
                <Bell size={36} className="text-brand-muted mx-auto mb-2" />
                <p className="text-brand-muted text-sm">No notifications yet.</p>
              </div>
            ) : (
              <div className="space-y-3 max-h-96 overflow-y-auto">
                {notifications.slice(0, 10).map(n => (
                  <div key={n.id} className={`card ${!n.read_at ? 'border-brand-amber/40' : ''}`}>
                    <p className="font-medium text-sm">{n.title}</p>
                    <p className="text-brand-muted text-xs mt-1">{n.body}</p>
                    {!n.read_at && <div className="w-2 h-2 bg-brand-amber rounded-full mt-2" />}
                  </div>
                ))}
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  )
}
