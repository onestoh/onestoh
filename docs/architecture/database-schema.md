# Database Schema — Key Tables

## Core Entities

```sql
-- Users (all roles)
users
  id, name, email, phone, password_hash
  role: enum(super_admin, yard_owner, individual_owner, client, broker, driver)
  kyc_status: enum(pending, approved, rejected, suspended)
  trust_score: tinyint(0-100)
  referral_code: varchar(12) UNIQUE
  referred_by: fk(users.id)
  created_at, updated_at

-- KYC Documents
kyc_documents
  id, user_id, document_type, file_path (S3), status
  reviewed_by: fk(users.id) -- admin
  rejection_reason, created_at

-- Yards (fleet operator profiles)
yards
  id, owner_id: fk(users.id)
  name, description, logo_url
  county, location_area, coordinates
  verification_tier: enum(basic, business, premium)
  created_at

-- Assets (vehicles and machines)
assets
  id, yard_id: fk(yards.id), owner_id: fk(users.id)
  category, sub_type, make, model, year
  registration_plate (encrypted), vin (encrypted)
  status: enum(active, under_maintenance, off_road, for_sale, retired)
  listing_mode: enum(rent_only, sale_only, rent_and_sale)
  fuel_type, transmission, seats_or_capacity
  is_self_drive_enabled, is_chauffeur_enabled
  hourly_rate, daily_rate, weekly_rate, monthly_rate
  security_deposit, mileage_cap_per_day
  pickup_county, pickup_area, pickup_coordinates
  is_published, admin_approved_at
  created_at, updated_at

-- Availability Slots
availability_slots
  id, asset_id: fk(assets.id)
  date, hour (null for full-day)
  status: enum(available, pending, confirmed, owner_blocked)
  booking_id: fk(bookings.id) nullable
  hold_expires_at: timestamp nullable
  UNIQUE(asset_id, date, hour)

-- Bookings
bookings
  id (UUID), asset_id, client_id, driver_id nullable
  rental_type: enum(self_drive, chauffeur)
  duration_type: enum(hourly, daily, weekly, monthly)
  start_at, end_at, actual_end_at
  status: enum(pending_payment, payment_processing, confirmed,
               owner_notified, client_prepared, active,
               completed, closed, cancelled_by_client,
               cancelled_by_admin, cancelled_by_system, disputed)
  base_amount, security_deposit_amount, driver_surcharge
  delivery_fee, insurance_fee, platform_fee, total_amount
  referral_code, broker_id nullable
  cancellation_reason, cancelled_at
  pre_rental_photos: json, post_rental_photos: json
  created_at, updated_at

-- Escrow Accounts
escrow_accounts
  id, booking_id: fk(bookings.id) UNIQUE
  total_collected, rental_fee, security_deposit
  platform_fee, broker_commission
  status: enum(collecting, held, releasing, released, dispute_hold, refunded)
  released_at, created_at

-- Wallets
wallets
  id, user_id: fk(users.id) UNIQUE
  balance: decimal(15,2)
  currency: varchar(3) DEFAULT 'KES'
  created_at, updated_at

wallet_transactions
  id, wallet_id, type: enum(credit, debit)
  amount, balance_after
  reference_type (booking, payout, refund, commission)
  reference_id, description
  created_at

-- Reviews
reviews
  id, booking_id: fk(bookings.id)
  reviewer_id, reviewee_id: fk(users.id)
  reviewer_type: enum(client, owner)
  overall_rating: tinyint(1-5)
  sub_ratings: json
  comment: text
  tags: json
  is_published, admin_flagged
  created_at

-- Notifications
notifications
  id (UUID), user_id: fk(users.id)
  type: varchar(50)  -- e.g. booking_confirmed, earnings_released
  title, body, data: json
  channels_sent: json  -- {sms: true, email: true, push: false}
  read_at: timestamp nullable
  created_at

-- Referral Tracking
referral_clicks
  id, referral_code, ip_address, user_agent
  converted_to_booking_id nullable
  created_at

-- Disputes
disputes
  id, booking_id: fk(bookings.id)
  raised_by: fk(users.id)
  type: enum(damage, not_as_described, no_show, mileage, deposit)
  description: text, evidence: json
  status: enum(open, under_review, ruled, appealed, closed)
  admin_id nullable, ruling: text, escrow_split: json
  created_at, resolved_at
```

## Indexes

```sql
-- Performance-critical indexes
CREATE INDEX idx_assets_status_published ON assets(status, is_published);
CREATE INDEX idx_assets_category_county ON assets(category, pickup_county);
CREATE INDEX idx_availability_asset_date ON availability_slots(asset_id, date, status);
CREATE INDEX idx_bookings_client ON bookings(client_id, status);
CREATE INDEX idx_bookings_asset ON bookings(asset_id, status);
CREATE INDEX idx_notifications_user_read ON notifications(user_id, read_at);
CREATE INDEX idx_wallet_tx_wallet ON wallet_transactions(wallet_id, created_at);
```
