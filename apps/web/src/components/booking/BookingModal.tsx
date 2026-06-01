'use client'
import { useState } from 'react'
import { Modal } from '@/components/ui/Modal'
import { Button } from '@/components/ui/Button'
import type { Asset, DurationType } from '@/lib/types'
import { format } from 'date-fns'
import { CheckCircle2, Clock, AlertCircle, Phone } from 'lucide-react'
import api from '@/lib/api'
import toast from 'react-hot-toast'

interface BookingModalProps {
  open: boolean
  onClose: () => void
  asset: Asset
  startDate: Date
  endDate: Date
  durationType: DurationType
  totalAmount: number
  rentalType: 'self_drive' | 'chauffeur'
}

type Step = 'confirm' | 'phone' | 'stk_pending' | 'success' | 'error'

export function BookingModal({
  open,
  onClose,
  asset,
  startDate,
  endDate,
  durationType,
  totalAmount,
  rentalType,
}: BookingModalProps) {
  const [step, setStep] = useState<Step>('confirm')
  const [phone, setPhone] = useState('')
  const [bookingId, setBookingId] = useState<string | null>(null)
  const [loading, setLoading] = useState(false)

  const handleInitiateBooking = async () => {
    if (!phone.match(/^(07|01|\+254)[0-9]{8,9}$/)) {
      toast.error('Enter a valid Safaricom number')
      return
    }
    setLoading(true)
    try {
      const res = await api.post('/api/bookings', {
        asset_id: asset.id,
        rental_type: rentalType,
        duration_type: durationType,
        start_at: startDate.toISOString(),
        end_at: endDate.toISOString(),
        mpesa_phone: phone,
      })
      setBookingId(res.data.data?.id)
      setStep('stk_pending')
      // Poll for payment confirmation
      const interval = setInterval(async () => {
        try {
          const statusRes = await api.get(`/api/bookings/${res.data.data.id}`)
          const status = statusRes.data.data?.status
          if (status === 'confirmed' || status === 'owner_notified') {
            clearInterval(interval)
            setStep('success')
          } else if (
            status === 'cancelled_by_system' ||
            status === 'cancelled_by_admin'
          ) {
            clearInterval(interval)
            setStep('error')
          }
        } catch {
          clearInterval(interval)
          setStep('error')
        }
      }, 3000)
      setTimeout(() => clearInterval(interval), 120_000)
    } catch (err: unknown) {
      const msg =
        err && typeof err === 'object' && 'response' in err
          ? (err as { response?: { data?: { message?: string } } }).response?.data?.message
          : undefined
      toast.error(msg || 'Failed to initiate booking')
      setStep('error')
    } finally {
      setLoading(false)
    }
  }

  return (
    <Modal open={open} onClose={onClose} title="Confirm Booking" size="md">
      {step === 'confirm' && (
        <div className="space-y-5">
          {/* Asset summary */}
          <div className="bg-[#0B1018] rounded-xl p-4">
            <p className="font-semibold text-[#DCE5F2]">
              {asset.year} {asset.make} {asset.model}
            </p>
            <p className="text-sm text-[#7088A8] capitalize mt-0.5">{asset.category.replace(/_/g, ' ')}</p>
            <div className="mt-3 grid grid-cols-2 gap-2 text-xs">
              <div>
                <span className="text-[#7088A8]">Pickup</span>
                <p className="text-[#DCE5F2] font-mono">{asset.pickup_area}, {asset.pickup_county}</p>
              </div>
              <div>
                <span className="text-[#7088A8]">Duration</span>
                <p className="text-[#DCE5F2] font-mono capitalize">{durationType}</p>
              </div>
              <div>
                <span className="text-[#7088A8]">Start</span>
                <p className="text-[#DCE5F2] font-mono">{format(startDate, 'dd MMM yyyy')}</p>
              </div>
              <div>
                <span className="text-[#7088A8]">End</span>
                <p className="text-[#DCE5F2] font-mono">{format(endDate, 'dd MMM yyyy')}</p>
              </div>
            </div>
          </div>

          <div className="flex items-center justify-between py-3 border-t border-b border-white/5">
            <span className="text-[#7088A8]">Total via M-Pesa</span>
            <span className="text-xl font-bold text-[#E8922A] font-mono">
              KES {totalAmount.toLocaleString()}
            </span>
          </div>

          <Button onClick={() => setStep('phone')} className="w-full">
            Proceed to Payment
          </Button>
        </div>
      )}

      {step === 'phone' && (
        <div className="space-y-5">
          <div className="flex items-center gap-3 p-4 bg-[#E8922A]/10 border border-[#E8922A]/20 rounded-xl">
            <Phone className="w-5 h-5 text-[#E8922A] shrink-0" />
            <p className="text-sm text-[#DCE5F2]">
              Enter your M-Pesa number. An STK Push prompt will be sent to your phone.
            </p>
          </div>
          <div>
            <label className="block text-xs font-mono tracking-widest uppercase text-[#E8922A] mb-2">
              M-Pesa Phone Number
            </label>
            <input
              type="tel"
              placeholder="e.g. 0712 345 678"
              value={phone}
              onChange={(e) => setPhone(e.target.value)}
              className="w-full bg-[#0B1018] border border-white/10 rounded-lg px-4 py-2.5 text-[#DCE5F2] placeholder:text-[#7088A8] outline-none focus:border-[#E8922A]/50"
            />
          </div>
          <div className="flex gap-3">
            <Button variant="outline" onClick={() => setStep('confirm')} className="flex-1">
              Back
            </Button>
            <Button onClick={handleInitiateBooking} loading={loading} className="flex-1">
              Send STK Push — KES {totalAmount.toLocaleString()}
            </Button>
          </div>
        </div>
      )}

      {step === 'stk_pending' && (
        <div className="text-center py-6 space-y-4">
          <div className="w-16 h-16 rounded-full bg-[#E8922A]/10 flex items-center justify-center mx-auto">
            <Clock className="w-8 h-8 text-[#E8922A] animate-pulse" />
          </div>
          <h3 className="font-semibold text-[#DCE5F2]">Check your phone</h3>
          <p className="text-sm text-[#7088A8]">
            An M-Pesa STK Push has been sent to <span className="text-[#DCE5F2] font-mono">{phone}</span>.
            Enter your PIN to complete payment.
          </p>
          <p className="text-xs text-[#7088A8] font-mono">Waiting for confirmation...</p>
          {bookingId && (
            <p className="text-xs text-[#7088A8]">Booking ref: <span className="font-mono text-[#E8922A]">{bookingId}</span></p>
          )}
        </div>
      )}

      {step === 'success' && (
        <div className="text-center py-6 space-y-4">
          <div className="w-16 h-16 rounded-full bg-[#2ECC8A]/10 flex items-center justify-center mx-auto">
            <CheckCircle2 className="w-8 h-8 text-[#2ECC8A]" />
          </div>
          <h3 className="font-semibold text-[#DCE5F2]">Booking Confirmed!</h3>
          <p className="text-sm text-[#7088A8]">
            Payment received. The owner has been notified and will prepare the vehicle.
          </p>
          {bookingId && (
            <p className="text-xs text-[#7088A8]">Booking ref: <span className="font-mono text-[#E8922A]">{bookingId}</span></p>
          )}
          <Button onClick={onClose} className="w-full">View My Bookings</Button>
        </div>
      )}

      {step === 'error' && (
        <div className="text-center py-6 space-y-4">
          <div className="w-16 h-16 rounded-full bg-[#E05252]/10 flex items-center justify-center mx-auto">
            <AlertCircle className="w-8 h-8 text-[#E05252]" />
          </div>
          <h3 className="font-semibold text-[#DCE5F2]">Payment Failed</h3>
          <p className="text-sm text-[#7088A8]">
            The payment was not completed. Please try again or contact support.
          </p>
          <div className="flex gap-3">
            <Button variant="outline" onClick={onClose} className="flex-1">Cancel</Button>
            <Button onClick={() => setStep('phone')} className="flex-1">Try Again</Button>
          </div>
        </div>
      )}
    </Modal>
  )
}
