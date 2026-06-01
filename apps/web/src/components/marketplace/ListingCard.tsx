import Link from 'next/link'
import Image from 'next/image'
import { Star, MapPin, Fuel, Settings2, Users } from 'lucide-react'
import type { Asset } from '@/lib/types'
import { Badge } from '@/components/ui/Badge'
import clsx from 'clsx'

interface ListingCardProps {
  asset: Asset
}

function formatRate(asset: Asset) {
  if (asset.daily_rate) return { amount: asset.daily_rate, unit: '/day' }
  if (asset.hourly_rate) return { amount: asset.hourly_rate, unit: '/hr' }
  if (asset.weekly_rate) return { amount: asset.weekly_rate, unit: '/week' }
  if (asset.monthly_rate) return { amount: asset.monthly_rate, unit: '/mo' }
  return null
}

export function ListingCard({ asset }: ListingCardProps) {
  const primaryMedia = asset.media?.find((m) => m.is_primary) ?? asset.media?.[0]
  const rate = formatRate(asset)
  const isAvailable = asset.status === 'active'

  return (
    <Link href={`/listings/${asset.id}`} className="group block">
      <div className="bg-[#141D2B] border border-white/5 rounded-xl overflow-hidden hover:border-[#E8922A]/30 transition-all duration-200 hover:shadow-lg hover:shadow-[#E8922A]/5">
        {/* Image */}
        <div className="relative h-48 bg-[#0B1018] overflow-hidden">
          {primaryMedia ? (
            <Image
              src={primaryMedia.file_path}
              alt={`${asset.make} ${asset.model}`}
              fill
              className="object-cover group-hover:scale-105 transition-transform duration-300"
            />
          ) : (
            <div className="w-full h-full flex items-center justify-center">
              <span className="text-4xl opacity-20">🚗</span>
            </div>
          )}
          {/* Availability dot */}
          <div className="absolute top-3 left-3 flex items-center gap-1.5 bg-[#0B1018]/80 backdrop-blur-sm rounded-full px-2.5 py-1">
            <span
              className={clsx(
                'w-2 h-2 rounded-full',
                isAvailable ? 'bg-[#2ECC8A] animate-pulse' : 'bg-[#E05252]'
              )}
            />
            <span className="text-xs font-mono text-[#DCE5F2]">
              {isAvailable ? 'Available' : 'Unavailable'}
            </span>
          </div>
          {/* Listing mode */}
          {asset.listing_mode === 'rent_and_sale' && (
            <div className="absolute top-3 right-3">
              <Badge variant="amber">Rent &amp; Sale</Badge>
            </div>
          )}
          {asset.listing_mode === 'sale_only' && (
            <div className="absolute top-3 right-3">
              <Badge variant="blue">For Sale</Badge>
            </div>
          )}
        </div>

        {/* Content */}
        <div className="p-4">
          <div className="flex items-start justify-between gap-2 mb-2">
            <div>
              <h3 className="font-semibold text-[#DCE5F2] group-hover:text-[#E8922A] transition-colors">
                {asset.year} {asset.make} {asset.model}
              </h3>
              <p className="text-xs text-[#7088A8] capitalize">
                {asset.category.replace(/_/g, ' ')}
              </p>
            </div>
            {rate && (
              <div className="text-right shrink-0">
                <span className="text-lg font-bold text-[#E8922A]">
                  KES {Number(rate.amount).toLocaleString()}
                </span>
                <span className="text-xs text-[#7088A8]">{rate.unit}</span>
              </div>
            )}
          </div>

          {/* Meta */}
          <div className="flex items-center gap-3 text-xs text-[#7088A8] mb-3">
            <span className="flex items-center gap-1">
              <Fuel className="w-3 h-3" />
              {asset.fuel_type}
            </span>
            <span className="flex items-center gap-1">
              <Settings2 className="w-3 h-3" />
              {asset.transmission}
            </span>
            {asset.seats && (
              <span className="flex items-center gap-1">
                <Users className="w-3 h-3" />
                {asset.seats} seats
              </span>
            )}
          </div>

          <div className="flex items-center justify-between">
            <span className="flex items-center gap-1 text-xs text-[#7088A8]">
              <MapPin className="w-3 h-3" />
              {asset.pickup_area}, {asset.pickup_county}
            </span>
            <div className="flex items-center gap-1">
              <Star className="w-3.5 h-3.5 fill-[#E8922A] text-[#E8922A]" />
              <span className="text-xs font-mono text-[#DCE5F2]">
                {Number(asset.average_rating).toFixed(1)}
              </span>
              <span className="text-xs text-[#7088A8]">({asset.total_reviews})</span>
            </div>
          </div>

          {/* Drive options */}
          <div className="flex gap-2 mt-3">
            {asset.is_self_drive_enabled && (
              <span className="text-xs bg-white/5 text-[#7088A8] px-2 py-0.5 rounded">Self Drive</span>
            )}
            {asset.is_chauffeur_enabled && (
              <span className="text-xs bg-white/5 text-[#7088A8] px-2 py-0.5 rounded">Chauffeur</span>
            )}
          </div>
        </div>
      </div>
    </Link>
  )
}
