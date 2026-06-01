'use client'
import { useState, useEffect } from 'react'
import { SearchBar } from '@/components/marketplace/SearchBar'
import { CategoryGrid } from '@/components/marketplace/CategoryGrid'
import { ListingCard } from '@/components/marketplace/ListingCard'
import { FilterSidebar } from '@/components/marketplace/FilterSidebar'
import type { Asset, AssetCategory } from '@/lib/types'
import api from '@/lib/api'
import { Shield, Zap, Star, TrendingUp, Users, Clock } from 'lucide-react'

const EMPTY_FILTERS = {
  categories: [] as AssetCategory[],
  minRate: '',
  maxRate: '',
  county: '',
  selfDrive: false,
  chauffeur: false,
  minYear: '',
  fuelType: '',
  transmission: '',
}

const STATS = [
  { icon: <Users className="w-5 h-5" />, value: '12,400+', label: 'Verified Users' },
  { icon: <TrendingUp className="w-5 h-5" />, value: '3,200+', label: 'Listings' },
  { icon: <Star className="w-5 h-5" />, value: '4.8/5', label: 'Avg Rating' },
  { icon: <Clock className="w-5 h-5" />, value: '< 30min', label: 'Booking Time' },
]

const HOW_IT_WORKS = [
  {
    step: '01',
    title: 'Search & Filter',
    desc: 'Browse 3,200+ verified vehicles and machinery across Kenya. Filter by category, county, date, and budget.',
  },
  {
    step: '02',
    title: 'Book & Pay via M-Pesa',
    desc: 'Select your dates, choose self-drive or chauffeur, and pay securely via M-Pesa STK Push. Funds held in escrow.',
  },
  {
    step: '03',
    title: 'Pick Up & Drive',
    desc: 'Owner confirms. You pick up the asset, complete the rental, and funds are released after your approval.',
  },
]

export default function HomePage() {
  const [assets, setAssets] = useState<Asset[]>([])
  const [loading, setLoading] = useState(true)
  const [filters, setFilters] = useState(EMPTY_FILTERS)
  const [page, setPage] = useState(1)
  const [hasMore, setHasMore] = useState(true)

  useEffect(() => {
    fetchAssets()
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [filters])

  const fetchAssets = async (p = 1) => {
    setLoading(true)
    try {
      const params: Record<string, string | number> = { page: p, per_page: 12 }
      if (filters.categories.length) params.categories = filters.categories.join(',')
      if (filters.minRate) params.min_rate = filters.minRate
      if (filters.maxRate) params.max_rate = filters.maxRate
      if (filters.county) params.county = filters.county
      if (filters.selfDrive) params.self_drive = 1
      if (filters.chauffeur) params.chauffeur = 1
      if (filters.fuelType) params.fuel_type = filters.fuelType
      if (filters.transmission) params.transmission = filters.transmission

      const res = await api.get('/api/assets', { params })
      const data: Asset[] = res.data.data || []
      if (p === 1) setAssets(data)
      else setAssets((prev) => [...prev, ...data])
      setHasMore(data.length === 12)
      setPage(p)
    } catch {
      // Use mock data if API is unavailable
      if (p === 1) setAssets(MOCK_ASSETS)
    } finally {
      setLoading(false)
    }
  }

  return (
    <div>
      {/* Hero */}
      <section className="relative bg-gradient-to-b from-[#0B1018] to-[#080C12] py-20 px-4">
        <div className="absolute inset-0 overflow-hidden">
          <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[400px] bg-[#E8922A]/5 rounded-full blur-3xl" />
        </div>
        <div className="relative max-w-4xl mx-auto text-center">
          <div className="inline-flex items-center gap-2 px-3 py-1 bg-[#E8922A]/10 border border-[#E8922A]/20 rounded-full text-xs text-[#E8922A] font-mono mb-6">
            <span className="w-1.5 h-1.5 rounded-full bg-[#2ECC8A] animate-pulse" />
            3,200+ listings live across Kenya
          </div>
          <h1 className="text-4xl md:text-6xl font-serif font-semibold text-[#DCE5F2] mb-4 leading-tight">
            Rent Vehicles &amp; Machinery
            <span className="block text-[#E8922A]">Across Kenya</span>
          </h1>
          <p className="text-lg text-[#7088A8] mb-10 max-w-2xl mx-auto">
            From a Prado for the weekend to an excavator for your site — book in minutes, pay via M-Pesa, protected by escrow.
          </p>
          <SearchBar />
        </div>
      </section>

      {/* Stats */}
      <section className="border-y border-white/5 bg-[#0B1018]">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 py-8 grid grid-cols-2 md:grid-cols-4 gap-6">
          {STATS.map((s) => (
            <div key={s.label} className="text-center">
              <div className="inline-flex items-center justify-center w-10 h-10 rounded-full bg-[#E8922A]/10 text-[#E8922A] mb-2">
                {s.icon}
              </div>
              <p className="text-2xl font-bold font-mono text-[#DCE5F2]">{s.value}</p>
              <p className="text-xs text-[#7088A8]">{s.label}</p>
            </div>
          ))}
        </div>
      </section>

      {/* Main content */}
      <div className="max-w-7xl mx-auto px-4 sm:px-6 py-10">
        <CategoryGrid />

        {/* Listings + filters */}
        <div className="flex gap-8 mt-4">
          {/* Sidebar */}
          <aside className="w-64 shrink-0">
            <FilterSidebar filters={filters} onChange={setFilters} />
          </aside>

          {/* Listings */}
          <div className="flex-1 min-w-0">
            <div className="flex items-center justify-between mb-5">
              <h2 className="text-lg font-semibold text-[#DCE5F2]">
                {loading ? 'Loading...' : `${assets.length} listings`}
              </h2>
              <select className="bg-[#141D2B] border border-white/10 rounded-lg px-3 py-1.5 text-sm text-[#DCE5F2] outline-none">
                <option>Most Recent</option>
                <option>Price: Low to High</option>
                <option>Price: High to Low</option>
                <option>Top Rated</option>
              </select>
            </div>

            {loading && assets.length === 0 ? (
              <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                {Array.from({ length: 6 }).map((_, i) => (
                  <div key={i} className="bg-[#141D2B] rounded-xl h-72 animate-pulse" />
                ))}
              </div>
            ) : assets.length === 0 ? (
              <div className="text-center py-20">
                <p className="text-4xl mb-4">🔍</p>
                <p className="text-[#DCE5F2] font-semibold">No listings found</p>
                <p className="text-sm text-[#7088A8] mt-2">Try adjusting your filters</p>
              </div>
            ) : (
              <>
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                  {assets.map((asset) => (
                    <ListingCard key={asset.id} asset={asset} />
                  ))}
                </div>
                {hasMore && (
                  <div className="text-center mt-10">
                    <button
                      onClick={() => fetchAssets(page + 1)}
                      disabled={loading}
                      className="px-8 py-3 border border-[#E8922A] text-[#E8922A] rounded-xl hover:bg-[#E8922A] hover:text-[#080C12] transition-colors font-medium disabled:opacity-50"
                    >
                      {loading ? 'Loading...' : 'Load More'}
                    </button>
                  </div>
                )}
              </>
            )}
          </div>
        </div>
      </div>

      {/* How it works */}
      <section className="bg-[#0B1018] border-t border-white/5 py-16 px-4">
        <div className="max-w-5xl mx-auto">
          <div className="text-center mb-12">
            <h2 className="text-3xl font-serif font-semibold text-[#DCE5F2]">How It Works</h2>
            <p className="text-[#7088A8] mt-2">Book a vehicle or machine in 3 simple steps</p>
          </div>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            {HOW_IT_WORKS.map((item) => (
              <div key={item.step} className="relative">
                <div className="text-5xl font-bold font-mono text-[#E8922A]/10 mb-4">{item.step}</div>
                <h3 className="font-semibold text-[#DCE5F2] mb-2">{item.title}</h3>
                <p className="text-sm text-[#7088A8] leading-relaxed">{item.desc}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Trust section */}
      <section className="py-16 px-4">
        <div className="max-w-5xl mx-auto">
          <div className="text-center mb-12">
            <h2 className="text-3xl font-serif font-semibold text-[#DCE5F2]">Built for Trust</h2>
            <p className="text-[#7088A8] mt-2">Every transaction protected from start to finish</p>
          </div>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            {[
              { icon: <Shield className="w-6 h-6" />, title: 'KYC Verified', desc: 'All users verify identity via government ID. No anonymous transactions.' },
              { icon: <Zap className="w-6 h-6" />, title: 'M-Pesa Escrow', desc: 'Payments held in escrow until rental is complete. Automatic refunds on cancellation.' },
              { icon: <Star className="w-6 h-6" />, title: 'Trust Scores', desc: 'Owners and clients earn reputation scores. Dispute resolution by our team within 24h.' },
            ].map((item) => (
              <div key={item.title} className="bg-[#141D2B] border border-white/5 rounded-xl p-6">
                <div className="w-12 h-12 rounded-xl bg-[#E8922A]/10 flex items-center justify-center text-[#E8922A] mb-4">
                  {item.icon}
                </div>
                <h3 className="font-semibold text-[#DCE5F2] mb-2">{item.title}</h3>
                <p className="text-sm text-[#7088A8] leading-relaxed">{item.desc}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* CTA */}
      <section className="bg-gradient-to-r from-[#E8922A]/10 to-[#F5B050]/5 border-t border-[#E8922A]/10 py-16 px-4">
        <div className="max-w-4xl mx-auto grid md:grid-cols-2 gap-8">
          <div className="bg-[#141D2B] border border-white/5 rounded-2xl p-8">
            <h3 className="text-xl font-serif font-semibold text-[#DCE5F2] mb-2">Own a Vehicle or Machine?</h3>
            <p className="text-sm text-[#7088A8] mb-6">List it on TheOnlineYard and earn while it&apos;s not in use. Full control over pricing, availability, and bookings.</p>
            <a href="/register?role=individual_owner" className="inline-block bg-[#E8922A] text-[#080C12] font-semibold px-6 py-3 rounded-lg hover:bg-[#F5B050] transition-colors">
              List Your Asset
            </a>
          </div>
          <div className="bg-[#141D2B] border border-white/5 rounded-2xl p-8">
            <h3 className="text-xl font-serif font-semibold text-[#DCE5F2] mb-2">Become a Broker</h3>
            <p className="text-sm text-[#7088A8] mb-6">Earn commission on every booking you refer. Share your link, track clicks, get paid automatically via M-Pesa.</p>
            <a href="/register?role=broker" className="inline-block border border-[#E8922A] text-[#E8922A] font-semibold px-6 py-3 rounded-lg hover:bg-[#E8922A] hover:text-[#080C12] transition-colors">
              Join as Broker
            </a>
          </div>
        </div>
      </section>
    </div>
  )
}

// Mock data for when API is unavailable
const MOCK_ASSETS: Asset[] = [
  {
    id: 1, yard_id: null, owner_id: 1, category: 'suv_4x4', sub_type: null,
    make: 'Toyota', model: 'Land Cruiser V8', year: 2021,
    fuel_type: 'Diesel', transmission: 'Automatic', seats: 8,
    status: 'active', listing_mode: 'rent_only',
    is_self_drive_enabled: false, is_chauffeur_enabled: true,
    hourly_rate: null, daily_rate: '15000', weekly_rate: '85000', monthly_rate: null,
    security_deposit: '30000', pickup_county: 'Nairobi', pickup_area: 'Westlands',
    features: null, description: 'Luxury V8 with experienced chauffeur.',
    average_rating: '4.9', total_reviews: 87, total_completed_rentals: 120,
    is_published: true, media: [],
  },
  {
    id: 2, yard_id: null, owner_id: 2, category: 'passenger_car', sub_type: null,
    make: 'Suzuki', model: 'Swift', year: 2022,
    fuel_type: 'Petrol', transmission: 'Manual', seats: 5,
    status: 'active', listing_mode: 'rent_only',
    is_self_drive_enabled: true, is_chauffeur_enabled: false,
    hourly_rate: null, daily_rate: '3500', weekly_rate: '20000', monthly_rate: '60000',
    security_deposit: '10000', pickup_county: 'Nairobi', pickup_area: 'Karen',
    features: null, description: 'Economical city car, great on fuel.',
    average_rating: '4.7', total_reviews: 43, total_completed_rentals: 65,
    is_published: true, media: [],
  },
  {
    id: 3, yard_id: null, owner_id: 3, category: 'excavator', sub_type: null,
    make: 'Komatsu', model: 'PC200-8', year: 2019,
    fuel_type: 'Diesel', transmission: 'Automatic', seats: null,
    status: 'active', listing_mode: 'rent_only',
    is_self_drive_enabled: false, is_chauffeur_enabled: true,
    hourly_rate: '8000', daily_rate: '55000', weekly_rate: null, monthly_rate: null,
    security_deposit: '100000', pickup_county: 'Nakuru', pickup_area: 'Industrial Area',
    features: null, description: '20-ton excavator with operator. Available for site work.',
    average_rating: '4.8', total_reviews: 22, total_completed_rentals: 38,
    is_published: true, media: [],
  },
  {
    id: 4, yard_id: null, owner_id: 4, category: 'pickup_truck', sub_type: null,
    make: 'Toyota', model: 'Hilux Double Cab', year: 2020,
    fuel_type: 'Diesel', transmission: 'Manual', seats: 5,
    status: 'active', listing_mode: 'rent_and_sale',
    is_self_drive_enabled: true, is_chauffeur_enabled: false,
    hourly_rate: null, daily_rate: '6000', weekly_rate: '35000', monthly_rate: null,
    security_deposit: '20000', pickup_county: 'Mombasa', pickup_area: 'Nyali',
    features: null, description: '4WD workhorse for tough terrain.',
    average_rating: '4.6', total_reviews: 31, total_completed_rentals: 50,
    is_published: true, media: [],
  },
  {
    id: 5, yard_id: null, owner_id: 5, category: 'generator', sub_type: null,
    make: 'Perkins', model: '100kVA', year: 2020,
    fuel_type: 'Diesel', transmission: 'Automatic', seats: null,
    status: 'active', listing_mode: 'rent_only',
    is_self_drive_enabled: false, is_chauffeur_enabled: false,
    hourly_rate: '2500', daily_rate: '18000', weekly_rate: null, monthly_rate: null,
    security_deposit: '50000', pickup_county: 'Nairobi', pickup_area: 'Industrial Area',
    features: null, description: '100kVA silent generator. Delivery available.',
    average_rating: '4.5', total_reviews: 18, total_completed_rentals: 29,
    is_published: true, media: [],
  },
  {
    id: 6, yard_id: null, owner_id: 6, category: 'van_minibus', sub_type: null,
    make: 'Toyota', model: 'Hiace Commuter', year: 2021,
    fuel_type: 'Diesel', transmission: 'Manual', seats: 14,
    status: 'active', listing_mode: 'rent_only',
    is_self_drive_enabled: false, is_chauffeur_enabled: true,
    hourly_rate: null, daily_rate: '8000', weekly_rate: '45000', monthly_rate: null,
    security_deposit: '25000', pickup_county: 'Nairobi', pickup_area: 'CBD',
    features: null, description: '14-seater for team transport or safaris.',
    average_rating: '4.7', total_reviews: 56, total_completed_rentals: 78,
    is_published: true, media: [],
  },
]
