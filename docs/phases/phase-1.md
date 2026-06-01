# Phase 1 — Foundation & Core Marketplace

**Timeline:** Months 1–6  
**Goal:** Launch TheOnlineYard in Nairobi with 50+ verified yard owners, 200+ active listings.  
**Target:** 500 completed rentals by Month 6.

---

## Milestone 1.1 — Project Setup & DevOps

### Deliverables
- Monorepo structure (`apps/api`, `apps/web`, `apps/mobile`, `packages/`)
- Docker Compose for local development (PHP-FPM, Nginx, MySQL, Redis)
- GitHub Actions CI/CD pipeline:
  - PHP tests on push (PHPUnit)
  - JS linting + type-check on push
  - Auto-deploy to staging on merge to `develop`
  - Manual deploy to production from `main`
- Staging and production environments provisioned (DigitalOcean)
- Cloudflare DNS + SSL configured
- `.env.example` files with all required variables documented

### Acceptance Criteria
- `docker-compose up` brings a working local stack in under 5 minutes
- CI passes on an empty Laravel + Next.js scaffold
- Staging URL accessible via HTTPS

---

## Milestone 1.2 — Auth & KYC System

### Deliverables

#### Registration Flow
1. Sign up: email + phone + role selection (Client / Owner / Broker / Driver)
2. OTP verification: phone (Africa's Talking SMS) + email (SendGrid)
3. Profile builder: name, address, role-specific fields
4. KYC document upload:
   - National ID (both sides)
   - Selfie with ID visible
   - Role-specific docs (business cert, driving licence, etc.)
5. Admin review queue — account locked until approved
6. Approval/rejection email + SMS notification with reason codes

#### Document Requirements by Role
- **Individual:** National ID, selfie, phone (M-Pesa line)
- **Yard Owner:** Cert of Incorporation, KRA PIN, Director ID, optional NTSA cert
- **Driver/Operator:** National ID, valid driving licence, police clearance
- **Broker:** National ID, selfie, optional physical address

#### Verification Tiers
| Tier | Cost | Capabilities |
|---|---|---|
| Unverified | Free | Browse only |
| ID Verified | Free | Book, list (up to 5), earn commissions |
| Business Verified | KES 500/yr | Unlimited listings, gold badge, priority placement |
| Premium Verified | KES 2,000/yr | All above + advanced analytics + support |

### API Endpoints
```
POST /api/auth/register
POST /api/auth/verify-otp
POST /api/auth/login
POST /api/auth/logout
POST /api/auth/refresh
POST /api/kyc/upload-documents
GET  /api/kyc/status
POST /api/admin/kyc/approve/{user}
POST /api/admin/kyc/reject/{user}
```

### Acceptance Criteria
- Full registration-to-approval flow end-to-end
- OTP expires after 10 minutes
- Documents stored encrypted in S3
- Admin KYC queue shows all pending submissions with filter/sort
- Re-submission workflow functions after rejection

---

## Milestone 1.3 — User Roles & Dashboards

### Deliverables
- Role-based dashboard routing (Next.js middleware)
- 6 dashboard shells with navigation:
  - **Super Admin:** Full platform control panel
  - **Yard Owner:** Fleet overview, bookings, wallet
  - **Individual Owner:** Simplified asset + booking view
  - **Client:** Booking history, upcoming trips, wallet
  - **Broker:** Referral dashboard, earnings, link generator
  - **Operator/Driver:** Assignment queue, trip status
- Role-based API middleware (`RoleMiddleware` in Laravel)
- Impersonation mode for admin support

---

## Milestone 1.4 — Vehicle & Machine Listing System

### Deliverables

#### Listing Creation (Multi-Step Form)
1. Select mode: Rental / Sale / Both
2. Asset details: type, category, make, model, year, engine, transmission, fuel, seats
3. Media upload: minimum 5 photos (validated), optional video
4. Rate setting: configure hourly / daily / weekly / monthly rates independently
5. Availability: set default available hours, block specific dates
6. Location & rules: pickup county + area, delivery option, usage rules
7. Submit for admin review

#### Validation Rules
- Minimum 5 photos enforced before submission
- Registration plate encrypted, cross-checked for duplicates
- Photo hash check for duplicate image detection
- VIN/chassis cross-reference for Premium Verified listings

#### Asset Categories
Passenger Cars, SUVs & 4×4s, Vans & Minibuses, Pickup Trucks, Heavy Trucks, Excavators, Tractors & Farm Equipment, Cranes & Lifts, Generators, Compactors & Rollers, Motorcycles & Tuk-Tuks, Special Equipment

### API Endpoints
```
GET    /api/listings
GET    /api/listings/{id}
POST   /api/listings
PUT    /api/listings/{id}
DELETE /api/listings/{id}
POST   /api/listings/{id}/media
DELETE /api/listings/{id}/media/{mediaId}
POST   /api/admin/listings/{id}/approve
POST   /api/admin/listings/{id}/reject
```

---

## Milestone 1.5 — Availability Calendar & Booking Engine Core

### Deliverables

#### Calendar System
- Per-listing availability stored in `availability_slots` table
- 4 slot states: `available`, `pending` (15-min hold), `confirmed`, `owner_blocked`
- Hourly view for assets with hourly rates (6AM–10PM time grid)
- Daily view as default for all other assets
- Inline availability widget on marketplace listing cards
- Full calendar on listing detail page
- Real-time updates via Pusher WebSocket

#### Booking Engine — 8-Stage Lifecycle
| Stage | Status | Trigger |
|---|---|---|
| 1 | `pending_payment` | Client confirms booking details |
| 2 | `payment_processing` | M-Pesa STK Push triggered |
| 3 | `confirmed` | Gateway payment callback received |
| 4 | `owner_notified` | Owner + driver notification dispatched |
| 5 | `client_prepared` | Pickup instructions sent to client |
| 6 | `active` | Pre-rental photos submitted, trip started |
| 7 | `completed` | Post-rental photos submitted, no dispute in 2hrs |
| 8 | `closed` | Escrow released, reviews requested |

### API Endpoints
```
GET  /api/listings/{id}/availability
POST /api/listings/{id}/hold-slots
POST /api/bookings
GET  /api/bookings/{id}
POST /api/bookings/{id}/start
POST /api/bookings/{id}/complete
POST /api/bookings/{id}/cancel
GET  /api/users/me/bookings
```

### Acceptance Criteria
- Concurrent booking conflict test: 100 simultaneous slot requests for same dates → only 1 succeeds
- 15-minute hold expires and slot returns to `available` automatically
- Slot permanently locked within 3 seconds of payment callback

---

## Milestone 1.6 — M-Pesa Payment Integration

### Deliverables
- Safaricom Daraja API integration (OAuth 2.0 token management)
- **STK Push (C2B):** Auto-triggers payment prompt to client's M-Pesa phone
- **Paybill fallback:** Client pays manually; booking reference auto-matched on confirmation
- **B2C (payouts):** Owner wallet-to-M-Pesa payout disbursement
- Webhook callback handler: verifies signature, updates booking status
- M-Pesa timeout reconciliation (polls for status on timeout)
- Auto-generated PDF receipt on payment confirmation
- Refund flow via Daraja B2C API

### Security
- OAuth tokens never stored in DB — fetched and cached in Redis with TTL
- All webhook callbacks verified against Safaricom IP whitelist
- Idempotency keys on all payment requests

### API Endpoints
```
POST /api/payments/mpesa/stk-push
POST /api/payments/mpesa/callback      # Daraja webhook
POST /api/payments/mpesa/b2c           # Payout
POST /api/payments/wallet/pay
GET  /api/payments/transactions
```

---

## Milestone 1.7 — Escrow Engine

### Deliverables
- `EscrowAccount` model — per-booking escrow ledger
- 5 escrow stages: Collection → Hold → Release Trigger → Distribution → Dispute Hold
- **Distribution split on completion:**
  1. Rental fee − platform fee → Owner Yard Wallet
  2. Security deposit → Client Wallet
  3. Platform fee → Platform Revenue Account
  4. Broker commission (if referral) → Broker Wallet
- Auto-release triggers:
  - Both parties submit post-rental photos + 2hr no-dispute window
  - 24hr auto-release after scheduled return time
  - Admin manual override
- Dispute freeze: security deposit portion frozen on dispute raise
- Admin force-release tool

---

## Milestone 1.8 — Notification System

### Deliverables
- 12 notification trigger events (see master plan Section 10)
- **Channels per event:**
  - Push: Firebase FCM (mobile + web PWA)
  - SMS: Africa's Talking (critical events always sent)
  - Email: SendGrid with HTML templates
  - WhatsApp: optional opt-in via WhatsApp Business API
- Notification preferences per user (toggle per category per channel)
- Do Not Disturb hours — critical alerts always bypass
- Notification centre inbox in dashboard (mark read, filter, search)
- Laravel notification queues (never block request cycle)

---

## Milestone 1.9 — Broker Referral Engine

### Deliverables
- Unique referral link + QR code generated on KYC approval for all users
- Referral tracking: link clicks, conversions, source attribution
- Commission structure:

| Transaction Type | Referral Agent | Licensed Broker |
|---|---|---|
| Daily rental | 3% | 5% |
| Weekly rental | 3.5% | 5.5% |
| Monthly rental | 4% | 6% |
| Vehicle sale lead | 1.5% | 2.5% |
| New yard owner referral | KES 500 flat | KES 1,000 flat |
| Heavy equipment rental | 3% | 5% |

- Commissions auto-credited to broker wallet on escrow release
- Minimum KES 500 payout threshold
- Broker dashboard: clicks, conversion rate, earnings, payout history

---

## Milestone 1.10 — Admin Control Panel

### Deliverables
- **KYC Queue:** Bulk approve / request docs / reject with reason codes
- **User Management:** Search, view full profile, suspend, reactivate, merge duplicates
- **Listings Management:** Approve/reject queue, override, boost, bulk de-list
- **Payments & Escrow:** Full transaction ledger, manual release/hold, force refund
- **Platform Config:** Fee percentages, commission rates, notification templates, cancellation policy
- Impersonation mode (view platform as any user)

---

## Milestone 1.11 — Reviews & Trust System

### Deliverables
- Bidirectional reviews: client rates owner/asset; owner rates client
- Review only unlocked after `completed` booking status
- **Client rates:** Overall, condition, pickup timeliness, driver professionalism, value
- **Owner rates:** Behaviour, asset care, punctuality, communication; positive/bad tags
- Trust Score (0–100) per user: auto-calculated from ratings, disputes, account age
- Trust Score thresholds: <60 yellow warning, <40 admin review + potential suspension
- Owner public response to client reviews
- 7-day review submission window per booking

---

## Phase 1 — Launch Readiness Checklist

- [ ] M-Pesa Daraja API tested: STK Push, B2C, C2B callbacks
- [ ] KYC flow end-to-end tested with Smile Identity sandbox
- [ ] Availability calendar concurrency tested (simultaneous bookings)
- [ ] Escrow release automation tested under all 3 trigger conditions
- [ ] All 12 notification triggers tested across SMS, push, and email
- [ ] Load test: 1,000 concurrent marketplace users
- [ ] Security audit: OWASP Top 10 review completed
- [ ] Platform T&Cs and rental agreement template reviewed by Kenyan legal counsel
- [ ] M-Pesa Paybill / Till number registered with Safaricom
- [ ] Bank escrow account opened in company name
- [ ] 50+ yard owners pre-registered and verified before go-live
- [ ] Support team trained on dispute resolution process
