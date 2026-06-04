export type Role = 'client' | 'owner' | 'broker' | 'admin'
export type KycStatus = 'not_submitted' | 'pending' | 'approved' | 'rejected'
export type Country = 'KE' | 'UG' | 'TZ' | 'NG' | 'GH' | 'ZA'

export interface User {
  id: number
  name: string
  email: string
  phone: string
  role: Role
  kyc_status: KycStatus
  country: Country
  preferred_currency: string
  trust_score: number
  referral_code: string
  wallet_balance: number
  is_active: boolean
  email_verified_at: string | null
  created_at: string
}

export interface Asset {
  id: number
  title: string
  description: string
  asset_type: string
  make: string
  model: string
  year: number
  daily_rate: number
  currency: string
  status: string
  is_listed: boolean
  yard?: Yard
  media?: AssetMedia[]
  rating?: number
}

export interface Yard {
  id: number
  name: string
  slug: string
  city: string
  country: Country
  rating: number
  total_assets: number
}

export interface AssetMedia {
  id: number
  url: string
  is_primary: boolean
  media_type: string
}

export type BookingStatus =
  | 'pending_payment' | 'confirmed' | 'owner_notified'
  | 'client_prepared' | 'active' | 'completed' | 'closed' | 'cancelled'

export interface Booking {
  id: string
  asset: Asset
  start_date: string
  end_date: string
  status: BookingStatus
  total_amount_kes: number
  currency: string
  with_driver: boolean
  rental_days: number
  created_at: string
}

export interface Payment {
  id: string
  amount: number
  currency: string
  payment_method: string
  status: string
  paid_at: string | null
  created_at: string
}

export interface Notification {
  id: string
  title: string
  body: string
  type: string
  read_at: string | null
  created_at: string
}

export interface ApiError {
  message: string
  errors?: Record<string, string[]>
}
