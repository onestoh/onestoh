'use client'
import { useState } from 'react'
import { useRouter } from 'next/navigation'
import { Search, MapPin, Calendar, Clock } from 'lucide-react'
import type { DurationType } from '@/lib/types'

const DURATION_OPTIONS: { value: DurationType; label: string }[] = [
  { value: 'hourly', label: 'Hourly' },
  { value: 'daily', label: 'Daily' },
  { value: 'weekly', label: 'Weekly' },
  { value: 'monthly', label: 'Monthly' },
]

const KENYAN_COUNTIES = [
  'Nairobi', 'Mombasa', 'Kisumu', 'Nakuru', 'Eldoret',
  'Thika', 'Machakos', 'Meru', 'Nyeri', 'Garissa',
]

export function SearchBar() {
  const router = useRouter()
  const [query, setQuery] = useState('')
  const [county, setCounty] = useState('')
  const [startDate, setStartDate] = useState('')
  const [duration, setDuration] = useState<DurationType>('daily')

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault()
    const params = new URLSearchParams()
    if (query) params.set('q', query)
    if (county) params.set('county', county)
    if (startDate) params.set('start', startDate)
    params.set('duration', duration)
    router.push(`/?${params.toString()}`)
  }

  return (
    <form
      onSubmit={handleSearch}
      className="bg-[#141D2B] border border-white/10 rounded-2xl p-2 flex flex-col sm:flex-row gap-2 shadow-xl"
    >
      {/* What */}
      <div className="flex items-center gap-2 flex-1 px-3 py-2 bg-[#0B1018] rounded-xl">
        <Search className="w-4 h-4 text-[#7088A8] shrink-0" />
        <input
          type="text"
          placeholder="Vehicle, machinery, equipment..."
          value={query}
          onChange={(e) => setQuery(e.target.value)}
          className="flex-1 bg-transparent text-sm text-[#DCE5F2] placeholder:text-[#7088A8] outline-none min-w-0"
        />
      </div>

      {/* Where */}
      <div className="flex items-center gap-2 px-3 py-2 bg-[#0B1018] rounded-xl min-w-[160px]">
        <MapPin className="w-4 h-4 text-[#7088A8] shrink-0" />
        <select
          value={county}
          onChange={(e) => setCounty(e.target.value)}
          className="flex-1 bg-transparent text-sm text-[#DCE5F2] outline-none cursor-pointer"
        >
          <option value="" className="bg-[#141D2B]">All Counties</option>
          {KENYAN_COUNTIES.map((c) => (
            <option key={c} value={c} className="bg-[#141D2B]">{c}</option>
          ))}
        </select>
      </div>

      {/* When */}
      <div className="flex items-center gap-2 px-3 py-2 bg-[#0B1018] rounded-xl">
        <Calendar className="w-4 h-4 text-[#7088A8] shrink-0" />
        <input
          type="date"
          value={startDate}
          onChange={(e) => setStartDate(e.target.value)}
          min={new Date().toISOString().split('T')[0]}
          className="bg-transparent text-sm text-[#DCE5F2] outline-none cursor-pointer"
        />
      </div>

      {/* Duration */}
      <div className="flex items-center gap-2 px-3 py-2 bg-[#0B1018] rounded-xl">
        <Clock className="w-4 h-4 text-[#7088A8] shrink-0" />
        <select
          value={duration}
          onChange={(e) => setDuration(e.target.value as DurationType)}
          className="bg-transparent text-sm text-[#DCE5F2] outline-none cursor-pointer"
        >
          {DURATION_OPTIONS.map((o) => (
            <option key={o.value} value={o.value} className="bg-[#141D2B]">{o.label}</option>
          ))}
        </select>
      </div>

      <button
        type="submit"
        className="bg-[#E8922A] text-[#080C12] font-semibold text-sm px-6 py-3 rounded-xl hover:bg-[#F5B050] transition-colors shrink-0"
      >
        Search
      </button>
    </form>
  )
}
