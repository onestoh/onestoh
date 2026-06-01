# Phase 2 — Fleet ERP & Equipment Module

**Timeline:** Months 7–18  
**Goal:** Kenya-wide expansion, heavy equipment category, mobile app launch.  
**Target:** 3,000 rentals/month, 50+ equipment yards, KES 5M+ monthly GMV.

---

## Milestone 2.1 — Fleet ERP Module

### Deliverables

#### Fleet Registry
- Centralised asset register with statuses: `active`, `under_maintenance`, `off_road`, `for_sale`, `retired`
- Digital logbook per asset: booking history, maintenance log, incident records
- Bulk CSV import for large fleets
- Asset grouping by type, size, or custom tags
- Retirement workflow: removes from marketplace, archives history

#### Maintenance Scheduling
- Owner sets maintenance intervals (km-based or time-based)
- System sends maintenance due alerts based on logged mileage/time
- "Schedule Maintenance" auto-blocks dates on availability calendar
- Service record: type, cost, service centre, next due date
- Insurance renewal tracking with 30-day advance alert

#### Utilisation Analytics
- Per-asset utilisation rate: booked days / available days
- Revenue per asset per month/quarter/year
- Idle assets report (zero bookings in last 30 days)
- Fleet performance leaderboard (best/worst performer)
- Period-over-period comparison dashboard

#### Yard Financial Dashboard
- Total revenue breakdown by asset, driver, booking type
- Pending escrow vs. released vs. payout-ready balance
- Expense tracking: maintenance, insurance, fuel
- Net margin per asset
- Exportable PDF + Excel reports

---

## Milestone 2.2 — Heavy Equipment & Machinery Module

### Deliverables

#### Specialised Listing Fields
- Machine class and sub-type (e.g. Excavator → Mini Excavator)
- Operating weight, bucket/blade capacity
- Fuel consumption rate (litres/hour)
- Transport method: self-propelled / flatbed / low-loader required
- Mobilisation cost (flat fee: owner yard → client site)
- Minimum hire period enforcement
- Site access requirements
- Operator certification required: Level 1, Level 2, OSHA-certified

#### Project-Based Booking
- Booking form includes: site location (GPS), site type, job description
- Mobilisation cost auto-calculated from GPS distance
- **Owner review step** (manual — only for heavy equipment):
  - Owner can accept or decline based on site suitability
  - 24-hour window to respond; auto-accept if no response
- Separate mobilisation date vs. operational start date
- Daily operational hours agreed upfront (e.g. max 8hrs/day)
- Fuel provisioning option: client-provided or owner-provided

#### Safety & Compliance
- All operators must have valid NITA/OSHA certificates uploaded
- Machine requires current third-party liability insurance
- Pre-delivery inspection checklist before mobilisation
- On-site incident reporting module (accidents, breakdowns)
- Machine certificate of roadworthiness required annually

---

## Milestone 2.3 — Car & Machine Sales Module

### Deliverables

#### Listing Modes
- `rent_only`, `sale_only`, `rent_and_sale` — owner toggles from dashboard
- Sale listings auto-remove rental availability on confirmed sale reservation
- "For Sale" badge on marketplace cards
- Dedicated sale-only filter in search

#### Sales Enquiry Flow
1. Client clicks "Make Enquiry" → in-platform message thread opened
2. Owner counter-offer through platform messaging
3. Agreed price locked in a `SaleOffer` record
4. Client pays 10% non-refundable reservation fee (escrow)
5. Asset removed from rental availability
6. Balance paid within 3–30 days (configurable)
7. Non-payment → reservation fee retained, asset re-listed

#### Test Drive Scheduling
- Owner configures test drive availability slots on a calendar
- Client selects slot; refundable test drive deposit held in escrow
- Pre-sale inspection record generated per test drive

#### Transfer & Documentation
- Digital Sale Agreement generated on price lock-in
- Both parties sign in-app (click-wrap + OTP signature)
- NTSA logbook transfer guidance built into completion flow
- Valuation request button links to third-party valuation service

---

## Milestone 2.4 — Driver Pool Management

### Deliverables
- Owner registers drivers linked to specific vehicles or fleet-wide
- Driver availability calendar (leave, off days)
- Auto-assignment: nearest available verified driver on chauffeur booking
- Driver performance scores: punctuality, ratings, incident-free rentals
- Driver earnings tracker for payroll support
- Freelance driver mode: shared across multiple yards
- Driver rating profile visible to yard owners when assigning

---

## Milestone 2.5 — Insurance Integration

### Deliverables
- Optional insurance add-on at checkout: "Add Rental Protection — KES X/day"
- **Products:**
  - Collision Damage Waiver (self-drive)
  - Third-Party Liability (client + owner)
- Partner insurers: APA Insurance, Jubilee Insurance (Kenya)
- Certificate issued instantly, attached to booking record
- Claim filed through platform, linked to booking record
- Revenue share tracked per policy sold

---

## Milestone 2.6 — Card Payments (DPO / Flutterwave)

### Deliverables
- Visa / Mastercard via DPO Group — 3DS authentication
- Flutterwave: pan-African cards, bank transfers, mobile money (20+ markets)
- Card tokenisation — raw card numbers never stored on platform
- Webhook signature verification on all callbacks
- Card refund via originating gateway

---

## Milestone 2.7 — Corporate Accounts

### Deliverables
- Company account registration (multi-user under one billing entity)
- Team member invitation and role assignment
- Centralised billing: single monthly invoice for all team bookings
- Corporate booking approval workflow (manager must approve bookings above threshold)
- Purchase order (PO) reference field on bookings
- Annual spend report exportable for accounting

---

## Milestone 2.8 — Mobile App (React Native)

### Deliverables
- iOS + Android app from single React Native codebase
- All Phase 1 booking flows supported:
  - Browse marketplace, view availability calendar
  - Initiate booking, M-Pesa STK Push payment
  - Track booking status, submit pre/post rental photos
  - Chat with owner/driver
  - View wallet, request payout
- Push notifications via Firebase FCM
- Offline-tolerant: cached listing data, queue payments when reconnected
- GPS location sharing for chauffeur pickup
- App Store + Google Play submission

---

## Milestone 2.9 — Advanced Analytics

### Deliverables

#### Admin Analytics
- Booking conversion funnel: listing viewed → calendar → booking → payment
- Revenue by category: cars / trucks / machinery / sales
- Geographic heatmap: bookings by county/region
- Platform growth: signups, verification rates, churn
- Dispute rate and avg resolution time

#### Owner Analytics
- Per-asset and fleet-total revenue dashboards
- Utilisation rate per asset
- Broker referral source breakdown
- Repeat renter rate
- Avg booking duration trends

#### Broker Analytics
- Total clicks and conversion rate per referral link
- Earnings: pending, cleared, paid out
- Best-performing listings by commission generated
- Monthly earnings trend

---

## Milestone 2.10 — Dispute Resolution System

### Deliverables
- "Raise Dispute" button available within 2-hour post-return window
- Escrow freezes relevant portion immediately on dispute raise
- Evidence submission: photos, timestamps, chat history, written statement
- Admin review SLA: 48–72 business hours
- Admin ruling documentation with reasoning
- Automated escrow split per ruling
- One appeal allowed per dispute
- Rental fee always released regardless of deposit disputes

---

## Milestone 2.11 — Listing Promotion Engine

### Deliverables
- Featured Listing: KES 500–2,000/week → top of search results
- Sponsored Category Placement: fixed monthly fee per category slot
- Homepage Banner Placement for maximum visibility
- Promotion payments via platform wallet or M-Pesa
- Promotion analytics: impressions, clicks, booking conversions per spend
