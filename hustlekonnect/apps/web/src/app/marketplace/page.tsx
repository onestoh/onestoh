'use client'
import { useEffect, useState, Suspense } from 'react'
import Link from 'next/link'
import { useSearchParams } from 'next/navigation'
import toast from 'react-hot-toast'
import api from '@/lib/api'
import { Asset } from '@/lib/types'
import { Search, Filter, Star } from 'lucide-react'

function MarketplaceInner() {
  const params = useSearchParams()
  const [assets, setAssets] = useState<Asset[]>([])
  const [loading, setLoading] = useState(true)
  const [filters, setFilters] = useState({
    country: params.get('country') || '',
    type: '',
    min_price: '',
    max_price: '',
    sort: 'newest',
  })

  const fetchAssets = async () => {
    setLoading(true)
    try {
      const query = new URLSearchParams()
      Object.entries(filters).forEach(([k, v]) => { if (v) query.set(k, v) })
      const res = await api.get(`/listings/assets?${query}`)
      setAssets(res.data.data || res.data || [])
    } catch {
      toast.error('Failed to load listings')
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => { fetchAssets() }, [filters])

  return (
    <div className="min-h-screen bg-brand-dark">
      <nav className="bg-brand-surface border-b border-brand-border px-4 py-3 flex justify-between items-center">
        <Link href="/" className="text-xl font-bold"><span className="text-brand-amber">Hustle</span>Konnect</Link>
        <div className="flex gap-3">
          <Link href="/login" className="btn-ghost text-sm">Log In</Link>
          <Link href="/register" className="btn-primary text-sm">Sign Up</Link>
        </div>
      </nav>

      <div className="max-w-7xl mx-auto px-4 py-8">
        <h1 className="text-3xl font-bold mb-6">Browse Vehicles & Machinery</h1>

        {/* Filters */}
        <div className="card mb-8 flex flex-wrap gap-4 items-end">
          <div className="flex-1 min-w-[140px]">
            <label className="label">Country</label>
            <select className="input" value={filters.country} onChange={e => setFilters(f => ({...f, country: e.target.value}))}>
              <option value="">All Countries</option>
              {[['KE','🇰🇪 Kenya'],['UG','🇺🇬 Uganda'],['TZ','🇹🇿 Tanzania'],['NG','🇳🇬 Nigeria'],['GH','🇬🇭 Ghana'],['ZA','🇿🇦 South Africa']].map(([v,l]) => <option key={v} value={v}>{l}</option>)}
            </select>
          </div>
          <div className="flex-1 min-w-[140px]">
            <label className="label">Type</label>
            <select className="input" value={filters.type} onChange={e => setFilters(f => ({...f, type: e.target.value}))}>
              <option value="">All Types</option>
              {['car','truck','bus','excavator','crane','grader','loader','trailer'].map(t => <option key={t} value={t} className="capitalize">{t}</option>)}
            </select>
          </div>
          <div className="flex-1 min-w-[120px]">
            <label className="label">Min Price (KES)</label>
            <input type="number" className="input" placeholder="0" value={filters.min_price} onChange={e => setFilters(f => ({...f, min_price: e.target.value}))} />
          </div>
          <div className="flex-1 min-w-[120px]">
            <label className="label">Max Price (KES)</label>
            <input type="number" className="input" placeholder="Any" value={filters.max_price} onChange={e => setFilters(f => ({...f, max_price: e.target.value}))} />
          </div>
          <div className="flex-1 min-w-[140px]">
            <label className="label">Sort By</label>
            <select className="input" value={filters.sort} onChange={e => setFilters(f => ({...f, sort: e.target.value}))}>
              <option value="newest">Newest</option>
              <option value="price_asc">Price: Low to High</option>
              <option value="price_desc">Price: High to Low</option>
              <option value="rating">Top Rated</option>
            </select>
          </div>
        </div>

        {/* Results */}
        {loading ? (
          <div className="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            {Array(8).fill(0).map((_, i) => (
              <div key={i} className="card animate-pulse h-72 bg-brand-surface" />
            ))}
          </div>
        ) : assets.length === 0 ? (
          <div className="text-center py-20">
            <Search size={64} className="text-brand-muted mx-auto mb-4" />
            <p className="text-xl text-brand-muted">No vehicles found matching your filters.</p>
          </div>
        ) : (
          <div className="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            {assets.map(asset => (
              <Link key={asset.id} href={`/asset/${asset.id}`} className="card group hover:border-brand-amber transition-colors">
                <div className="h-40 bg-brand-dark rounded-lg mb-4 overflow-hidden">
                  {asset.media?.[0] ? (
                    <img src={asset.media[0].url} alt={asset.title} className="w-full h-full object-cover group-hover:scale-105 transition-transform" />
                  ) : (
                    <div className="w-full h-full flex items-center justify-center text-4xl">🚗</div>
                  )}
                </div>
                <h3 className="font-semibold truncate">{asset.title}</h3>
                <p className="text-brand-muted text-sm">{asset.yard?.city}, {asset.yard?.country}</p>
                <div className="flex justify-between items-center mt-3">
                  <div>
                    <span className="text-brand-amber font-bold text-lg">KES {asset.daily_rate?.toLocaleString()}</span>
                    <span className="text-brand-muted text-xs">/day</span>
                  </div>
                  {asset.rating && (
                    <div className="flex items-center gap-1 text-sm">
                      <Star size={14} className="text-yellow-400 fill-yellow-400" />
                      <span>{asset.rating.toFixed(1)}</span>
                    </div>
                  )}
                </div>
              </Link>
            ))}
          </div>
        )}
      </div>
    </div>
  )
}

export default function MarketplacePage() {
  return <Suspense><MarketplaceInner /></Suspense>
}
