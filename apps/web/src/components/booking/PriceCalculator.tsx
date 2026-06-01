'use client'
import { useState, useMemo } from 'react'
import { differenceInDays, differenceInHours } from 'date-fns'
import type { Asset, DurationType } from '@/lib/types'
import { Info } from 'lucide-react'

interface PriceCalculatorProps {
  asset: Asset
  startDate: Date | null
  endDate: Date | null
  onDurationTypeChange?: (type: DurationType) => void
}

const PLATFORM_FEE_RATE = 0.08 // 8%

function getRateForType(asset: Asset, durationType: DurationType): number | null {
  switch (durationType) {
    case 'hourly': return asset.hourly_rate ? Number(asset.hourly_rate) : null
    case 'daily': return asset.daily_rate ? Number(asset.daily_rate) : null
    case 'weekly': return asset.weekly_rate ? Number(asset.weekly_rate) : null
    case 'monthly': return asset.monthly_rate ? Number(asset.monthly_rate) : null
  }
}

export function PriceCalculator({
  asset,
  startDate,
  endDate,
  onDurationTypeChange,
}: PriceCalculatorProps) {
  const [durationType, setDurationType] = useState<DurationType>('daily')

  const availableTypes: DurationType[] = (['hourly', 'daily', 'weekly', 'monthly'] as DurationType[]).filter(
    (t) => getRateForType(asset, t) !== null
  )

  const handleDurationChange = (t: DurationType) => {
    setDurationType(t)
    onDurationTypeChange?.(t)
  }

  const { units, baseAmount, platformFee, depositAmount, total } = useMemo(() => {
    const rate = getRateForType(asset, durationType) ?? 0
    let units = 0

    if (startDate && endDate) {
      if (durationType === 'hourly') {
        units = Math.max(1, differenceInHours(endDate, startDate))
      } else if (durationType === 'daily') {
        units = Math.max(1, differenceInDays(endDate, startDate))
      } else if (durationType === 'weekly') {
        units = Math.max(1, Math.ceil(differenceInDays(endDate, startDate) / 7))
      } else {
        units = Math.max(1, Math.ceil(differenceInDays(endDate, startDate) / 30))
      }
    } else {
      units = 1
    }

    const baseAmount = rate * units
    const platformFee = Math.round(baseAmount * PLATFORM_FEE_RATE)
    const depositAmount = Number(asset.security_deposit)
    const total = baseAmount + platformFee + depositAmount

    return { units, baseAmount, platformFee, depositAmount, total }
  }, [asset, durationType, startDate, endDate])

  const rate = getRateForType(asset, durationType) ?? 0
  const unitLabel = durationType === 'hourly' ? 'hour' : durationType === 'daily' ? 'day' : durationType === 'weekly' ? 'week' : 'month'

  return (
    <div className="bg-[#141D2B] border border-white/5 rounded-xl p-5">
      <h3 className="font-semibold text-[#DCE5F2] mb-4">Price Breakdown</h3>

      {/* Duration type tabs */}
      <div className="flex gap-1 bg-[#0B1018] rounded-lg p-1 mb-5">
        {availableTypes.map((t) => (
          <button
            key={t}
            onClick={() => handleDurationChange(t)}
            className={`flex-1 py-1.5 rounded-md text-xs font-mono capitalize transition-colors ${
              durationType === t
                ? 'bg-[#E8922A] text-[#080C12] font-semibold'
                : 'text-[#7088A8] hover:text-[#DCE5F2]'
            }`}
          >
            {t}
          </button>
        ))}
      </div>

      {/* Rate */}
      <div className="text-center mb-5">
        <span className="text-3xl font-bold text-[#E8922A]">KES {rate.toLocaleString()}</span>
        <span className="text-[#7088A8] text-sm">/{unitLabel}</span>
      </div>

      {/* Line items */}
      <div className="space-y-2.5 text-sm">
        <div className="flex items-center justify-between">
          <span className="text-[#7088A8]">
            KES {rate.toLocaleString()} &times; {units} {unitLabel}{units !== 1 ? 's' : ''}
          </span>
          <span className="text-[#DCE5F2] font-mono">KES {baseAmount.toLocaleString()}</span>
        </div>
        <div className="flex items-center justify-between">
          <span className="text-[#7088A8] flex items-center gap-1">
            Platform fee (8%)
            <Info className="w-3 h-3" />
          </span>
          <span className="text-[#DCE5F2] font-mono">KES {platformFee.toLocaleString()}</span>
        </div>
        <div className="flex items-center justify-between">
          <span className="text-[#7088A8]">Security deposit</span>
          <span className="text-[#DCE5F2] font-mono">KES {depositAmount.toLocaleString()}</span>
        </div>
        <div className="h-px bg-white/5" />
        <div className="flex items-center justify-between font-semibold">
          <span className="text-[#DCE5F2]">Total (M-Pesa)</span>
          <span className="text-[#E8922A] font-bold text-lg font-mono">KES {total.toLocaleString()}</span>
        </div>
      </div>

      <p className="mt-4 text-xs text-[#7088A8] leading-relaxed">
        Deposit returned after successful rental completion. Platform fee covers escrow &amp; insurance.
      </p>
    </div>
  )
}
