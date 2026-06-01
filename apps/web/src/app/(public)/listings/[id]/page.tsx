'use client'
import { useState, useEffect } from 'react'
import { useParams } from 'next/navigation'
import Image from 'next/image'
import Link from 'next/link'
import api from '@/lib/api'
import type { Asset, DurationType } from '@/lib/types'
import { AvailabilityCalendar } from '@/components/booking/AvailabilityCalendar'
import { PriceCalculator } from '@/components/booking/PriceCalculator'
import { BookingModal } from '@/components/booking/BookingModal'
import { Badge } from '@/components/ui/Badge'
import {
  Star, MapPin, Fuel, Settings2, Users, Shield, ChevronLeft, ChevronRight,
  CheckCircle2, Calendar, Car
} from 'lucide-react'

export default function ListingDetailPage() {
  const { id } = useParams<{ id: string }>()
  const [asset, setAsset] = useState<Asset | null>(null)
  const [loading, setLoading] = useState(true)
  const [photoIdx, setPhotoIdx] = useState(0)
  const [startDate, setStartDate] = useState<Date | null>(null)
  const [endDate, setEndDate] = useState<Date | null>(null)
  const [durationType, setDurationType] = useState<DurationType>('daily')
  const [rentalType, setRentalType] = useState<'self_drive' | 'chauffeur'>('self_drive')
  const [bookingOpen, setBookingOpen] = useState(false)
  const [totalAmount, setTotalAmount] = useState(0)

  useEffect(() => {
    api.get(`/api/assets/${id}`)
      .then((res) => setAsset(res.data.data))
      .catch(() => setAsset(null))
      .finally(() => setLoading(false))
  }, [id])

  if (loading) {
    return (
      <div className="max-w-7xl mx-auto px-4 py-12">
        <div className="animate-pulse space-y-4">
          <div className="h-80 bg-[#141D2B] rounded-2xl" />
          <div className="h-8 bg-[#141D2B] rounded w-1/2" />
          <div className="h-4 bg-[#141D2B] rounded w-1/3" />
        </div>
      </div>
    )
  }

  if (!asset) {
    return (
      <div className="max-w-7xl mx-auto px-4 py-20 text-center">
        <p className="text-5xl mb-4">🔍</p>
        <h1 className="text-xl font-semibold text-[#DCE5F2]">Listing not found</h1>
        <Link href="/" className="inline-block mt-4 text-[#E8922A]">Back to marketplace</Link>
      </div>
    )
  }

  const photos = asset.media.filter((m) => m.type === 'photo')

  const canBook = startDate && endDate && asset.status === 'active'

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 py-8">
      {/* Back */}
      <Link href="/" className="inline-flex items-center gap-1.5 text-sm text-[#7088A8] hover:text-[#DCE5F2] mb-6">
        <ChevronLeft className="w-4 h-4" /> Back to listings
      </Link>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {/* Left: main content */}
        <div className="lg:col-span-2 space-y-8">
          {/* Gallery */}
          <div className="relative">
            <div className="relative h-80 md:h-[420px] rounded-2xl overflow-hidden bg-[#141D2B]">
              {photos.length > 0 ? (
                <Image
                  src={photos[photoIdx].file_path}
                  alt={`${asset.make} ${asset.model}`}
                  fill
                  className="object-cover"
                />
              ) : (
                <div className="w-full h-full flex items-center justify-center text-6xl">
                  <Car className="w-20 h-20 text-[#7088A8]/20" />
                </div>
              )}
              {photos.length > 1 && (
                <>
                  <button
                    onClick={() => setPhotoIdx((i) => (i - 1 + photos.length) % photos.length)}
                    className="absolute left-3 top-1/2 -translate-y-1/2 p-2 rounded-full bg-black/50 text-white hover:bg-black/70"
                  >
                    <ChevronLeft className="w-5 h-5" />
                  </button>
                  <button
                    onClick={() => setPhotoIdx((i) => (i + 1) % photos.length)}
                    className="absolute right-3 top-1/2 -translate-y-1/2 p-2 rounded-full bg-black/50 text-white hover:bg-black/70"
                  >
                    <ChevronRight className="w-5 h-5" />
                  </button>
                  <div className="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5">
                    {photos.map((_, i) => (
                      <button
                        key={i}
                        onClick={() => setPhotoIdx(i)}
                        className={`w-2 h-2 rounded-full transition-colors ${
                          i === photoIdx ? 'bg-[#E8922A]' : 'bg-white/40'
                        }`}
                      />
                    ))}
                  </div>
                </>
              )}
            </div>
            {/* Thumbnail strip */}
            {photos.length > 1 && (
              <div className="flex gap-2 mt-3 overflow-x-auto pb-2">
                {photos.map((photo, i) => (
                  <button
                    key={photo.id}
                    onClick={() => setPhotoIdx(i)}
                    className={`relative w-20 h-16 rounded-lg overflow-hidden shrink-0 border-2 transition-colors ${
                      i === photoIdx ? 'border-[#E8922A]' : 'border-white/5'
                    }`}
                  >
                    <Image src={photo.file_path} alt="" fill className="object-cover" />
                  </button>
                ))}
              </div>
            )}
          </div>

          {/* Title & meta */}
          <div>
            <div className="flex items-start justify-between gap-4 flex-wrap">
              <div>
                <h1 className="text-2xl font-serif font-semibold text-[#DCE5F2]">
                  {asset.year} {asset.make} {asset.model}
                </h1>
                <p className="text-[#7088A8] capitalize mt-1">{asset.category.replace(/_/g, ' ')}</p>
              </div>
              <div className="flex items-center gap-2 flex-wrap">
                {asset.status === 'active' ? (
                  <Badge variant="green"><span className="w-1.5 h-1.5 rounded-full bg-[#2ECC8A]" /> Available</Badge>
                ) : (
                  <Badge variant="red">Unavailable</Badge>
                )}
                {asset.listing_mode === 'rent_and_sale' && <Badge variant="amber">Rent &amp; Sale</Badge>}
              </div>
            </div>

            <div className="flex items-center gap-4 mt-4 flex-wrap">
              <span className="flex items-center gap-1.5 text-sm text-[#7088A8]">
                <MapPin className="w-4 h-4" /> {asset.pickup_area}, {asset.pickup_county}
              </span>
              <span className="flex items-center gap-1.5 text-sm">
                <Star className="w-4 h-4 fill-[#E8922A] text-[#E8922A]" />
                <span className="text-[#DCE5F2] font-medium">{Number(asset.average_rating).toFixed(1)}</span>
                <span className="text-[#7088A8]">({asset.total_reviews} reviews)</span>
              </span>
              <span className="flex items-center gap-1.5 text-sm text-[#7088A8]">
                <CheckCircle2 className="w-4 h-4 text-[#2ECC8A]" /> {asset.total_completed_rentals} rentals
              </span>
            </div>
          </div>

          {/* Vehicle specs */}
          <div className="bg-[#141D2B] border border-white/5 rounded-xl p-5">
            <h2 className="font-semibold text-[#DCE5F2] mb-4">Vehicle Details</h2>
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
              {[
                { label: 'Fuel', value: asset.fuel_type, icon: <Fuel className="w-4 h-4" /> },
                { label: 'Transmission', value: asset.transmission, icon: <Settings2 className="w-4 h-4" /> },
                ...(asset.seats ? [{ label: 'Seats', value: `${asset.seats} seats`, icon: <Users className="w-4 h-4" /> }] : []),
                { label: 'Year', value: String(asset.year), icon: <Calendar className="w-4 h-4" /> },
              ].map((spec) => (
                <div key={spec.label} className="flex items-center gap-2">
                  <div className="w-8 h-8 rounded-lg bg-[#E8922A]/10 flex items-center justify-center text-[#E8922A]">
                    {spec.icon}
                  </div>
                  <div>
                    <p className="text-xs text-[#7088A8]">{spec.label}</p>
                    <p className="text-sm font-medium text-[#DCE5F2] capitalize">{spec.value}</p>
                  </div>
                </div>
              ))}
            </div>
          </div>

          {/* Rental types */}
          <div>
            <h2 className="font-semibold text-[#DCE5F2] mb-4">Rental Options</h2>
            <div className="flex gap-3">
              {asset.is_self_drive_enabled && (
                <button
                  onClick={() => setRentalType('self_drive')}
                  className={`flex-1 p-4 rounded-xl border text-left transition-all ${
                    rentalType === 'self_drive' ? 'border-[#E8922A] bg-[#E8922A]/5' : 'border-white/5 bg-[#141D2B] hover:border-white/20'
                  }`}
                >
                  <p className="font-medium text-[#DCE5F2]">Self Drive</p>
                  <p className="text-xs text-[#7088A8] mt-1">You drive, full control</p>
                </button>
              )}
              {asset.is_chauffeur_enabled && (
                <button
                  onClick={() => setRentalType('chauffeur')}
                  className={`flex-1 p-4 rounded-xl border text-left transition-all ${
                    rentalType === 'chauffeur' ? 'border-[#E8922A] bg-[#E8922A]/5' : 'border-white/5 bg-[#141D2B] hover:border-white/20'
                  }`}
                >
                  <p className="font-medium text-[#DCE5F2]">With Chauffeur</p>
                  <p className="text-xs text-[#7088A8] mt-1">Professional driver included</p>
                </button>
              )}
            </div>
          </div>

          {/* Description */}
          {asset.description && (
            <div>
              <h2 className="font-semibold text-[#DCE5F2] mb-3">About this asset</h2>
              <p className="text-sm text-[#7088A8] leading-relaxed">{asset.description}</p>
            </div>
          )}

          {/* Availability calendar */}
          <div>
            <h2 className="font-semibold text-[#DCE5F2] mb-4">Availability</h2>
            <AvailabilityCalendar
              assetId={asset.id}
              onRangeSelect={(start, end) => { setStartDate(start); setEndDate(end) }}
            />
          </div>

          {/* Owner card */}
          {asset.owner && (
            <div className="bg-[#141D2B] border border-white/5 rounded-xl p-5">
              <h2 className="font-semibold text-[#DCE5F2] mb-4">Owner</h2>
              <div className="flex items-center gap-4">
                <div className="w-12 h-12 rounded-full bg-[#E8922A]/20 flex items-center justify-center text-[#E8922A] font-bold text-lg">
                  {asset.owner.name[0].toUpperCase()}
                </div>
                <div>
                  <p className="font-medium text-[#DCE5F2]">{asset.owner.name}</p>
                  <p className="text-sm text-[#7088A8] capitalize">{asset.owner.role.replace(/_/g, ' ')}</p>
                  {asset.owner.company_name && (
                    <p className="text-xs text-[#7088A8]">{asset.owner.company_name}</p>
                  )}
                </div>
                <div className="ml-auto text-right">
                  <p className="text-xs text-[#7088A8]">Trust score</p>
                  <p className="font-bold text-[#2ECC8A]">{asset.owner.trust_score}/100</p>
                </div>
              </div>
              <div className="flex items-center gap-2 mt-4">
                {asset.owner.kyc_status === 'approved' && (
                  <Badge variant="green"><Shield className="w-3 h-3" /> KYC Verified</Badge>
                )}
              </div>
            </div>
          )}
        </div>

        {/* Right: booking sidebar */}
        <div className="space-y-5">
          <PriceCalculator
            asset={asset}
            startDate={startDate}
            endDate={endDate}
            onDurationTypeChange={setDurationType}
          />

          <button
            onClick={() => canBook && setBookingOpen(true)}
            disabled={!canBook}
            className="w-full bg-[#E8922A] text-[#080C12] font-semibold py-3.5 rounded-xl hover:bg-[#F5B050] transition-colors disabled:opacity-40 disabled:cursor-not-allowed text-lg"
          >
            {startDate && endDate ? 'Book Now via M-Pesa' : 'Select Dates to Book'}
          </button>

          {!startDate && (
            <p className="text-xs text-center text-[#7088A8]">Select dates on the availability calendar below</p>
          )}

          <div className="bg-[#141D2B] border border-white/5 rounded-xl p-4 space-y-3">
            <div className="flex items-center gap-2 text-sm">
              <Shield className="w-4 h-4 text-[#2ECC8A]" />
              <span className="text-[#7088A8]">M-Pesa escrow protection</span>
            </div>
            <div className="flex items-center gap-2 text-sm">
              <CheckCircle2 className="w-4 h-4 text-[#2ECC8A]" />
              <span className="text-[#7088A8]">KYC-verified owner</span>
            </div>
            <div className="flex items-center gap-2 text-sm">
              <Star className="w-4 h-4 text-[#2ECC8A]" />
              <span className="text-[#7088A8]">Deposit returned on completion</span>
            </div>
          </div>
        </div>
      </div>

      {/* Booking modal */}
      {bookingOpen && startDate && endDate && (
        <BookingModal
          open={bookingOpen}
          onClose={() => setBookingOpen(false)}
          asset={asset}
          startDate={startDate}
          endDate={endDate}
          durationType={durationType}
          totalAmount={totalAmount}
          rentalType={rentalType}
        />
      )}
    </div>
  )
}
