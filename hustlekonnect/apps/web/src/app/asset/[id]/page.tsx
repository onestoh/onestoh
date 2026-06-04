'use client'
import { useEffect, useState } from 'react'
import Link from 'next/link'
import { useParams, useRouter } from 'next/navigation'
import toast from 'react-hot-toast'
import api from '@/lib/api'
import { Asset, Booking } from '@/lib/types'
import { Star, MapPin, Shield, Calendar, Car } from 'lucide-react'
import { useAuthStore } from '@/store/auth'

export default function AssetDetailPage() {
  const { id } = useParams()
  const router = useRouter()
  const { user } = useAuthStore()
  const [asset, setAsset] = useState<Asset | null>(null)
  const [loading, setLoading] = useState(true)
  const [booking, setBooking] = useState({ start_date: '', end_date: '', with_driver: false, currency: 'KES' })
  const [submitting, setSubmitting] = useState(false)

  useEffect(() => {
    api.get(`/listings/assets/${id}`)
      .then(r => setAsset(r.data.data))
      .catch(() => toast.error('Asset not found'))
      .finally(() => setLoading(false))
  }, [id])

  const handleBook = async (e: React.FormEvent) => {
    e.preventDefault()
    if (!user) { router.push('/login'); return }
    if (user.kyc_status !== 'approved') { toast.error('Complete KYC verification to book.'); router.push('/kyc'); return }

    setSubmitting(true)
    try {
      const res = await api.post('/bookings', { asset_id: Number(id), ...booking })
      toast.success('Booking created! Proceed to payment.')
      router.push(`/dashboard`)
    } catch (err: any) {
      toast.error(err.response?.data?.message || 'Booking failed.')
    } finally {
      setSubmitting(false)
    }
  }

  if (loading) return <div className="flex items-center justify-center min-h-screen text-brand-muted">Loading...</div>
  if (!asset) return <div className="flex items-center justify-center min-h-screen text-brand-muted">Asset not found</div>

  return (
    <div className="min-h-screen bg-brand-dark">
      <nav className="bg-brand-surface border-b border-brand-border px-4 py-3 flex justify-between items-center">
        <Link href="/" className="text-xl font-bold"><span className="text-brand-amber">Hustle</span>Konnect</Link>
        <Link href="/marketplace" className="text-brand-muted hover:text-white">← Back to listings</Link>
      </nav>

      <div className="max-w-6xl mx-auto px-4 py-8 grid lg:grid-cols-3 gap-8">
        {/* Main content */}
        <div className="lg:col-span-2 space-y-6">
          <div className="card">
            <div className="h-64 bg-brand-dark rounded-lg mb-6 overflow-hidden">
              {asset.media?.[0] ? (
                <img src={asset.media[0].url} alt={asset.title} className="w-full h-full object-cover" />
              ) : (
                <div className="w-full h-full flex items-center justify-center text-6xl">🚗</div>
              )}
            </div>

            <div className="flex justify-between items-start">
              <div>
                <h1 className="text-2xl font-bold">{asset.title}</h1>
                <div className="flex items-center gap-2 text-brand-muted text-sm mt-1">
                  <MapPin size={14}/> {asset.yard?.city}, {asset.yard?.country}
                </div>
              </div>
              <div className="text-right">
                <div className="text-2xl font-bold text-brand-amber">KES {asset.daily_rate?.toLocaleString()}</div>
                <div className="text-brand-muted text-xs">per day</div>
              </div>
            </div>

            {asset.rating && (
              <div className="flex items-center gap-1 mt-2">
                <Star size={16} className="text-yellow-400 fill-yellow-400" />
                <span className="font-medium">{asset.rating.toFixed(1)}</span>
              </div>
            )}

            <p className="text-brand-muted mt-4">{asset.description}</p>

            <div className="grid grid-cols-3 gap-4 mt-6">
              {[
                { label: 'Make', value: asset.make },
                { label: 'Model', value: asset.model },
                { label: 'Year', value: asset.year },
              ].map(({label, value}) => (
                <div key={label} className="bg-brand-dark rounded-lg p-3">
                  <div className="text-brand-muted text-xs">{label}</div>
                  <div className="font-semibold mt-1">{value}</div>
                </div>
              ))}
            </div>
          </div>
        </div>

        {/* Booking panel */}
        <div>
          <div className="card sticky top-4">
            <h2 className="text-xl font-bold mb-4 flex items-center gap-2">
              <Calendar size={20} className="text-brand-amber" /> Book This Vehicle
            </h2>

            <form onSubmit={handleBook} className="space-y-4">
              <div>
                <label className="label">Pick-up Date</label>
                <input type="date" className="input" required
                  value={booking.start_date}
                  min={new Date().toISOString().split('T')[0]}
                  onChange={e => setBooking(b => ({...b, start_date: e.target.value}))}
                />
              </div>
              <div>
                <label className="label">Return Date</label>
                <input type="date" className="input" required
                  value={booking.end_date}
                  min={booking.start_date || new Date().toISOString().split('T')[0]}
                  onChange={e => setBooking(b => ({...b, end_date: e.target.value}))}
                />
              </div>
              <label className="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" className="w-4 h-4 accent-brand-amber"
                  checked={booking.with_driver}
                  onChange={e => setBooking(b => ({...b, with_driver: e.target.checked}))}
                />
                <span className="text-sm">Include Driver</span>
              </label>
              <div className="bg-brand-dark rounded-lg p-3 text-xs text-brand-muted flex items-center gap-2">
                <Shield size={14} className="text-brand-amber shrink-0"/>
                Payment held in secure escrow. Released only after successful rental.
              </div>
              <button type="submit" disabled={submitting || !booking.start_date || !booking.end_date} className="btn-primary w-full">
                {submitting ? 'Creating Booking...' : 'Book Now'}
              </button>
            </form>

            <p className="text-brand-muted text-xs text-center mt-3">
              KYC verification required to book.
            </p>
          </div>
        </div>
      </div>
    </div>
  )
}
