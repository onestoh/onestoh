export type UserRole = 'super_admin' | 'yard_owner' | 'individual_owner' | 'client' | 'broker' | 'driver'
export type KycStatus = 'pending' | 'approved' | 'rejected' | 'suspended'
export type BookingStatus = 'pending_payment' | 'payment_processing' | 'confirmed' | 'owner_notified' | 'client_prepared' | 'active' | 'completed' | 'closed' | 'cancelled_by_client' | 'cancelled_by_admin' | 'cancelled_by_system' | 'disputed'
export type AssetCategory = 'passenger_car' | 'suv_4x4' | 'van_minibus' | 'pickup_truck' | 'heavy_truck' | 'excavator' | 'tractor_farm' | 'crane_lift' | 'generator' | 'compactor_roller' | 'motorcycle_tuktuk' | 'special_equipment'
export type DurationType = 'hourly' | 'daily' | 'weekly' | 'monthly'

export interface User {
  id: number
  name: string
  email: string
  phone: string
  role: UserRole
  kyc_status: KycStatus
  trust_score: number
  referral_code: string | null
  is_licensed_broker: boolean
  company_name: string | null
  created_at: string
}

export interface Asset {
  id: number
  yard_id: number | null
  owner_id: number
  category: AssetCategory
  sub_type: string | null
  make: string
  model: string
  year: number
  fuel_type: string
  transmission: string
  seats: number | null
  status: 'active' | 'under_maintenance' | 'off_road' | 'for_sale' | 'retired'
  listing_mode: 'rent_only' | 'sale_only' | 'rent_and_sale'
  is_self_drive_enabled: boolean
  is_chauffeur_enabled: boolean
  hourly_rate: string | null
  daily_rate: string | null
  weekly_rate: string | null
  monthly_rate: string | null
  security_deposit: string
  pickup_county: string
  pickup_area: string
  features: Record<string, boolean> | null
  description: string | null
  average_rating: string
  total_reviews: number
  total_completed_rentals: number
  is_published: boolean
  media: AssetMedia[]
  owner?: User
  yard?: Yard
}

export interface AssetMedia {
  id: number
  asset_id: number
  type: 'photo' | 'video'
  file_path: string
  thumbnail_path: string | null
  sort_order: number
  is_primary: boolean
}

export interface Yard {
  id: number
  owner_id: number
  name: string
  description: string | null
  logo_url: string | null
  county: string
  location_area: string
  verification_tier: 'basic' | 'business' | 'premium'
  average_rating: string
  total_reviews: number
  total_completed_rentals: number
}

export interface Booking {
  id: string
  asset_id: number
  client_id: number
  driver_id: number | null
  broker_id: number | null
  rental_type: 'self_drive' | 'chauffeur'
  duration_type: DurationType
  start_at: string
  end_at: string
  status: BookingStatus
  currency: string
  base_amount: string
  security_deposit_amount: string
  platform_fee: string
  total_amount: string
  asset?: Asset
  client?: User
}

export interface Wallet {
  id: number
  user_id: number
  balance: string
  pending_balance: string
  deposit_balance: string
  currency: string
}

export interface PlatformNotification {
  id: string
  user_id: number
  type: string
  title: string
  body: string
  data: Record<string, unknown> | null
  read_at: string | null
  created_at: string
}
