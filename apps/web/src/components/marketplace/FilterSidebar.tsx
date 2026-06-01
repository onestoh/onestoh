'use client'
import { useState } from 'react'
import type { AssetCategory } from '@/lib/types'
import { SlidersHorizontal, X } from 'lucide-react'

interface Filters {
  categories: AssetCategory[]
  minRate: string
  maxRate: string
  county: string
  selfDrive: boolean
  chauffeur: boolean
  minYear: string
  fuelType: string
  transmission: string
}

interface FilterSidebarProps {
  filters: Filters
  onChange: (filters: Filters) => void
}

const CATEGORIES: { value: AssetCategory; label: string }[] = [
  { value: 'passenger_car', label: 'Passenger Cars' },
  { value: 'suv_4x4', label: 'SUVs & 4x4' },
  { value: 'van_minibus', label: 'Vans & Minibuses' },
  { value: 'pickup_truck', label: 'Pickup Trucks' },
  { value: 'heavy_truck', label: 'Heavy Trucks' },
  { value: 'excavator', label: 'Excavators' },
  { value: 'tractor_farm', label: 'Farm Tractors' },
  { value: 'crane_lift', label: 'Cranes & Lifts' },
  { value: 'generator', label: 'Generators' },
  { value: 'compactor_roller', label: 'Compactors' },
  { value: 'motorcycle_tuktuk', label: 'Motorcycles & Tuktuks' },
  { value: 'special_equipment', label: 'Special Equipment' },
]

const COUNTIES = [
  'Nairobi', 'Mombasa', 'Kisumu', 'Nakuru', 'Eldoret',
  'Thika', 'Machakos', 'Meru', 'Nyeri', 'Garissa',
]

function Section({ title, children }: { title: string; children: React.ReactNode }) {
  return (
    <div className="py-4 border-b border-white/5 last:border-0">
      <h4 className="text-xs font-mono tracking-widest uppercase text-[#E8922A] mb-3">{title}</h4>
      {children}
    </div>
  )
}

export function FilterSidebar({ filters, onChange }: FilterSidebarProps) {
  const [open, setOpen] = useState(false)

  const toggleCategory = (cat: AssetCategory) => {
    const next = filters.categories.includes(cat)
      ? filters.categories.filter((c) => c !== cat)
      : [...filters.categories, cat]
    onChange({ ...filters, categories: next })
  }

  const resetFilters = () =>
    onChange({
      categories: [], minRate: '', maxRate: '', county: '',
      selfDrive: false, chauffeur: false, minYear: '', fuelType: '', transmission: '',
    })

  const inner = (
    <div className="bg-[#141D2B] border border-white/5 rounded-xl p-4">
      <div className="flex items-center justify-between mb-2">
        <h3 className="font-semibold text-[#DCE5F2] flex items-center gap-2">
          <SlidersHorizontal className="w-4 h-4 text-[#E8922A]" /> Filters
        </h3>
        <button
          onClick={resetFilters}
          className="text-xs text-[#7088A8] hover:text-[#E8922A] font-mono"
        >
          Reset all
        </button>
      </div>

      <Section title="Category">
        <div className="space-y-2">
          {CATEGORIES.map((cat) => (
            <label key={cat.value} className="flex items-center gap-2.5 cursor-pointer group">
              <input
                type="checkbox"
                checked={filters.categories.includes(cat.value)}
                onChange={() => toggleCategory(cat.value)}
                className="w-4 h-4 rounded accent-[#E8922A]"
              />
              <span className="text-sm text-[#7088A8] group-hover:text-[#DCE5F2] transition-colors">
                {cat.label}
              </span>
            </label>
          ))}
        </div>
      </Section>

      <Section title="Daily Rate (KES)">
        <div className="flex gap-2">
          <input
            type="number"
            placeholder="Min"
            value={filters.minRate}
            onChange={(e) => onChange({ ...filters, minRate: e.target.value })}
            className="w-full bg-[#0B1018] border border-white/10 rounded-lg px-3 py-2 text-sm text-[#DCE5F2] placeholder:text-[#7088A8] outline-none focus:border-[#E8922A]/50"
          />
          <input
            type="number"
            placeholder="Max"
            value={filters.maxRate}
            onChange={(e) => onChange({ ...filters, maxRate: e.target.value })}
            className="w-full bg-[#0B1018] border border-white/10 rounded-lg px-3 py-2 text-sm text-[#DCE5F2] placeholder:text-[#7088A8] outline-none focus:border-[#E8922A]/50"
          />
        </div>
      </Section>

      <Section title="Location">
        <select
          value={filters.county}
          onChange={(e) => onChange({ ...filters, county: e.target.value })}
          className="w-full bg-[#0B1018] border border-white/10 rounded-lg px-3 py-2 text-sm text-[#DCE5F2] outline-none focus:border-[#E8922A]/50"
        >
          <option value="" className="bg-[#141D2B]">All Counties</option>
          {COUNTIES.map((c) => (
            <option key={c} value={c} className="bg-[#141D2B]">{c}</option>
          ))}
        </select>
      </Section>

      <Section title="Rental Type">
        <div className="space-y-2">
          <label className="flex items-center gap-2.5 cursor-pointer">
            <input
              type="checkbox"
              checked={filters.selfDrive}
              onChange={(e) => onChange({ ...filters, selfDrive: e.target.checked })}
              className="w-4 h-4 rounded accent-[#E8922A]"
            />
            <span className="text-sm text-[#7088A8]">Self Drive</span>
          </label>
          <label className="flex items-center gap-2.5 cursor-pointer">
            <input
              type="checkbox"
              checked={filters.chauffeur}
              onChange={(e) => onChange({ ...filters, chauffeur: e.target.checked })}
              className="w-4 h-4 rounded accent-[#E8922A]"
            />
            <span className="text-sm text-[#7088A8]">With Chauffeur</span>
          </label>
        </div>
      </Section>

      <Section title="Fuel Type">
        <select
          value={filters.fuelType}
          onChange={(e) => onChange({ ...filters, fuelType: e.target.value })}
          className="w-full bg-[#0B1018] border border-white/10 rounded-lg px-3 py-2 text-sm text-[#DCE5F2] outline-none focus:border-[#E8922A]/50"
        >
          <option value="" className="bg-[#141D2B]">Any</option>
          {['Petrol', 'Diesel', 'Electric', 'Hybrid', 'LPG'].map((f) => (
            <option key={f} value={f.toLowerCase()} className="bg-[#141D2B]">{f}</option>
          ))}
        </select>
      </Section>

      <Section title="Transmission">
        <div className="flex gap-2">
          {['Any', 'Manual', 'Automatic'].map((t) => (
            <button
              key={t}
              onClick={() => onChange({ ...filters, transmission: t === 'Any' ? '' : t.toLowerCase() })}
              className={`flex-1 py-1.5 rounded-lg text-xs font-mono transition-colors ${
                (t === 'Any' && !filters.transmission) || filters.transmission === t.toLowerCase()
                  ? 'bg-[#E8922A] text-[#080C12]'
                  : 'bg-[#0B1018] text-[#7088A8] hover:text-[#DCE5F2]'
              }`}
            >
              {t}
            </button>
          ))}
        </div>
      </Section>
    </div>
  )

  return (
    <>
      {/* Mobile toggle */}
      <div className="lg:hidden mb-4">
        <button
          onClick={() => setOpen(true)}
          className="flex items-center gap-2 px-4 py-2 bg-[#141D2B] border border-white/10 rounded-lg text-sm text-[#DCE5F2]"
        >
          <SlidersHorizontal className="w-4 h-4 text-[#E8922A]" /> Filters
        </button>
      </div>

      {/* Mobile drawer */}
      {open && (
        <div className="lg:hidden fixed inset-0 z-50 flex">
          <div className="absolute inset-0 bg-black/60" onClick={() => setOpen(false)} />
          <div className="relative ml-auto w-80 h-full bg-[#0B1018] overflow-y-auto p-4">
            <button
              onClick={() => setOpen(false)}
              className="absolute top-4 right-4 p-1 text-[#7088A8] hover:text-[#DCE5F2]"
            >
              <X className="w-5 h-5" />
            </button>
            {inner}
          </div>
        </div>
      )}

      {/* Desktop sidebar */}
      <div className="hidden lg:block">{inner}</div>
    </>
  )
}
