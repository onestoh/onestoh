'use client'
import Link from 'next/link'
import type { AssetCategory } from '@/lib/types'

const CATEGORIES: { value: AssetCategory; label: string; icon: string; count?: number }[] = [
  { value: 'passenger_car', label: 'Passenger Cars', icon: '🚗', count: 234 },
  { value: 'suv_4x4', label: 'SUVs & 4x4', icon: '🚙', count: 187 },
  { value: 'van_minibus', label: 'Vans & Minibuses', icon: '🚐', count: 93 },
  { value: 'pickup_truck', label: 'Pickup Trucks', icon: '🛻', count: 145 },
  { value: 'heavy_truck', label: 'Heavy Trucks', icon: '🚛', count: 78 },
  { value: 'excavator', label: 'Excavators', icon: '🏗️', count: 42 },
  { value: 'tractor_farm', label: 'Farm Tractors', icon: '🚜', count: 61 },
  { value: 'crane_lift', label: 'Cranes & Lifts', icon: '🏗️', count: 29 },
  { value: 'generator', label: 'Generators', icon: '⚡', count: 115 },
  { value: 'compactor_roller', label: 'Compactors', icon: '🛞', count: 33 },
  { value: 'motorcycle_tuktuk', label: 'Motorcycles & Tuktuks', icon: '🛵', count: 201 },
  { value: 'special_equipment', label: 'Special Equipment', icon: '🔧', count: 47 },
]

export function CategoryGrid() {
  return (
    <section className="py-10">
      <div className="flex items-center justify-between mb-6">
        <h2 className="text-xl font-semibold text-[#DCE5F2]">Browse by Category</h2>
        <Link href="/listings" className="text-sm text-[#E8922A] hover:text-[#F5B050]">View all</Link>
      </div>
      <div className="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3">
        {CATEGORIES.map((cat) => (
          <Link
            key={cat.value}
            href={`/listings?category=${cat.value}`}
            className="group flex flex-col items-center gap-2 p-4 bg-[#141D2B] border border-white/5 rounded-xl hover:border-[#E8922A]/30 hover:bg-[#141D2B] transition-all"
          >
            <span className="text-2xl">{cat.icon}</span>
            <span className="text-xs text-center text-[#7088A8] group-hover:text-[#DCE5F2] transition-colors leading-tight">
              {cat.label}
            </span>
            {cat.count && (
              <span className="text-xs font-mono text-[#E8922A]/60">{cat.count}</span>
            )}
          </Link>
        ))}
      </div>
    </section>
  )
}
