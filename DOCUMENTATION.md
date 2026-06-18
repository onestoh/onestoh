# EstateYard — Project Documentation

**Version:** 1.0  
**Stack:** Laravel 12 · PHP 8.2+ · MySQL 8.0 · Blade Templates  
**URL (local):** http://localhost/kan/onestoh/public

---

## Table of Contents

1. [Project Overview](#1-project-overview)
2. [System Requirements](#2-system-requirements)
3. [Installation](#3-installation)
4. [User Roles](#4-user-roles)
5. [Feature Modules](#5-feature-modules)
6. [Role-by-Role Flow](#6-role-by-role-flow)
7. [Database Tables](#7-database-tables)
8. [API Reference](#8-api-reference)
9. [Security](#9-security)
10. [File Structure](#10-file-structure)
11. [Demo Accounts](#11-demo-accounts)

---

## 1. Project Overview

EstateYard is a full-stack Real Estate ERP platform built for the Kenyan property market. It supports buying, selling, renting, short-stay bookings, hotel rooms, live auctions, escrow payments, M-Pesa rent collection, KYC verification, property inspections, surveys, valuations, referral programs, and multi-role dashboards for every stakeholder in the real estate ecosystem.

**Key numbers:**
- 13 user roles
- 34 database tables
- 118+ web routes + REST API v1
- 30+ controllers
- 25+ Eloquent models
- PDF generation (leases, receipts, owner statements)
- Real-time auction bidding (3-second polling)
- M-Pesa payment callback integration

---

## 2. System Requirements

| Requirement | Version |
|-------------|---------|
| PHP | 8.2 or higher |
| MySQL | 8.0 or higher |
| Composer | 2.x |
| Node.js (optional, for Vite) | 18+ |
| WAMP / XAMPP / Laravel Herd | Any recent version |

---

## 3. Installation

### Windows (WAMP)

```cmd
REM 1. Place project in C:\wamp64\www\kan\onestoh\
REM 2. Open CMD as Administrator in that folder
setup.bat

REM 3. Edit .env
APP_URL=http://localhost/kan/onestoh/public
DB_HOST=127.0.0.1
DB_DATABASE=estateyard
DB_USERNAME=root
DB_PASSWORD=your_password

REM 4. Create database and import
mysql -u root -p -e "CREATE DATABASE estateyard CHARACTER SET utf8mb4;"
mysql -u root -p estateyard < setup\estateyard_mysql.sql

REM 5. Open browser
http://localhost/kan/onestoh/public
```

### Linux / Mac

```bash
bash setup.sh
# Edit .env with DB credentials
mysql -u root -p -e "CREATE DATABASE estateyard CHARACTER SET utf8mb4;"
mysql -u root -p estateyard < setup/estateyard_mysql.sql
php artisan serve
# Open http://localhost:8000
```

---

## 4. User Roles

EstateYard supports **13 roles**, each with a dedicated dashboard and permissions:

| Role | Description |
|------|-------------|
| `admin` | Full platform control — users, KYC, property moderation |
| `landlord` | Lists properties, manages leases and rent collection |
| `tenant` | Views lease, pays rent, submits maintenance requests |
| `broker_licensed` | Licensed property broker with referral tracking |
| `broker_unlicensed` | Informal broker/agent with basic tools |
| `developer` | Manages development projects and property listings |
| `valuer` | Conducts and records property valuations |
| `surveyor` | Conducts and records property surveys |
| `auctioneer` | Creates and runs live property auctions |
| `investor` | Browses, saves, and monitors investment properties |
| `corporate` | Corporate entity managing multiple properties |
| `property_manager` | Manages properties on behalf of landlords |
| `finance` | Views rent collection reports and financial data |

---

## 5. Feature Modules

### 5.1 Authentication

- **Registration** — Users select a role on sign-up. Referral codes are tracked at registration and linked to the referring user.
- **Login** — Session-based authentication with brute-force protection (10 attempts per 15 minutes → 429 error).
- **Logout** — Clears session data.
- **Middleware** — `AuthMiddleware` checks `session('user_id')` on protected routes. `RoleMiddleware` enforces per-role access.

**Flow:**
```
Register → Select Role → Login → Role Dashboard
```

---

### 5.2 Property Management

Properties are the core entity. Each property has:
- **Type:** house, apartment, land, commercial, airbnb, hotel, villa, office
- **Listing type:** sale, rent, airbnb, hotel, auction
- **Status:** active, pending, sold, rented, draft, suspended

**Lifecycle:**
```
Owner creates property (draft)
  → Submits for approval (pending)
  → Admin approves (active) or suspends
  → Listed in Marketplace
  → Admin can feature it (is_featured = true)
```

**Who can list:**
Landlord, Developer, Corporate, Property Manager, Broker (Licensed)

**Features:**
- Image uploads (stored in storage/app/public)
- JSON amenities and images fields
- Soft delete (deleted_at)
- View count tracking
- Save/wishlist toggle for investors/tenants
- Property comparison (up to 3 side-by-side)
- Property enquiry form (sends notification to owner)

---

### 5.3 Marketplace

Public listing page accessible to all visitors.

**Filters available:**
- Keyword search (title, location, description)
- Property type (house, apartment, land, etc.)
- Listing type (sale, rent, airbnb, hotel, auction)
- Bedrooms count
- Price range (min/max)
- County
- Verified only toggle
- Featured only toggle

**Sort options:**
- Newest (default)
- Price: Low to High
- Price: High to Low
- Most Views

**Compare tool:**
- Visitors can add up to 3 properties to a comparison list
- Comparison stored in browser localStorage
- Side-by-side table: type, bedrooms, bathrooms, size, county, verified, featured, amenities

---

### 5.4 Booking System (Airbnb / Hotel)

For properties listed as `airbnb` or `hotel`.

**Guest Flow:**
```
View Property → Check Availability → Select Dates → Review Pricing
  → Create Booking (pending) → Payment Page → Process Payment
  → Booking Confirmed → Check-In → Check-Out → Leave Review
```

**Host Flow:**
```
Dashboard → Incoming Bookings → Confirm/Reject → Manage Check-In/Out
```

**Key features:**
- Availability calendar (blocked dates shown visually)
- Dynamic pricing rules (day-of-week rates, date-range overrides)
- Hotel room inventory management (room numbers, types, capacity)
- Guest review system (ratings: overall, cleanliness, communication, location, value)
- Booking status: pending → confirmed → completed / cancelled
- Special requests field
- Automatic availability blocking when booking is confirmed

---

### 5.5 Rent & Lease Management

For properties listed as `rent`.

**Landlord creates lease:**
```
Landlord → New Lease → Assign Tenant → Set Monthly Rent + Deposit
  → Start/End Dates → Active Lease
```

**Tenant pays rent:**
```
Tenant Dashboard → Current Lease → Pay Rent
  → Select Method (M-Pesa / Bank Transfer)
  → M-Pesa: Enter phone → STK Push → Callback updates status
  → Bank: Enter transaction ref → Manual confirmation
```

**Rent Payment Status:** pending → paid / overdue

**PDF Documents:**
- Lease Agreement PDF (landlord/tenant can download)
- Rent Receipt PDF (per payment)
- Owner Statement PDF (monthly summary for landlord)

---

### 5.6 Auctions

Live property auctions with real-time bid updates.

**Auctioneer creates auction:**
```
Auctioneer Dashboard → Create Auction → Link Property
  → Set Starting Bid, Reserve Price, Start/End Time
  → Status: scheduled → live → ended
```

**Live bidding flow:**
```
Buyer views auction page
  → Page polls /auctions/{id}/live-data every 3 seconds
  → Buyer places bid (must exceed current bid)
  → All previous bidders receive "outbid" notification
  → Auctioneer can end auction manually
  → Winner receives notification
  → Auction status → ended, winner_id set
```

**Auction statuses:** scheduled, live, ended, cancelled

---

### 5.7 Escrow Transactions

For property sales to protect both buyer and seller.

```
Buyer initiates escrow → Funds held in escrow
  → On completion: Seller requests release
  → Admin/Finance approves → Funds released to seller
  → OR: Buyer disputes → Admin reviews → Refund or release
```

**Escrow statuses:** pending → funded → released / disputed / refunded

---

### 5.8 KYC Verification

All users can submit KYC documents to get verified status.

**User flow:**
```
Dashboard → Verification → Upload Documents (ID, certificates)
  → Status: pending → Admin Reviews → approved / rejected
  → Approved: is_verified = true, tier updated (basic/professional/elite)
```

**Admin flow:**
```
Admin Dashboard → KYC Queue → View Documents → Approve / Reject with notes
```

**Verification tiers:** none, basic, professional, elite

---

### 5.9 Referral Program

```
User generates referral link (/ref/{code})
  → Shares with others
  → New user registers via link → referred_by tracked
  → Referral conversion recorded
  → Referrer earns commission credit
```

Brokers and promoters have dedicated referral dashboards showing click counts, conversion counts, and total earned.

---

### 5.10 Messaging

Internal platform messaging between users.

```
Any user → Send Message → Select Recipient → Subject + Body
  → Stored in messages table
  → Recipient sees unread count badge in dashboard nav
  → Inbox / Conversation thread view
```

Messages can be linked to a specific property (e.g., enquiry follow-up).

---

### 5.11 Inspections

Book a property inspection with a registered surveyor/inspector.

```
Tenant/Buyer → Book Inspection → Select Property + Date
  → Inspector receives notification
  → Inspection scheduled → Inspector submits report
  → Status: scheduled → completed
```

---

### 5.12 Maintenance Requests

For tenants in active leases.

```
Tenant Dashboard → Report Issue → Title + Description + Priority
  → Submitted to Landlord/Property Manager
  → Assigned to maintenance person
  → Status: open → in_progress → resolved → closed
```

**Priority levels:** low, medium, high, urgent

---

### 5.13 Valuations & Surveys

**Valuation (for Valuers):**
```
Client requests valuation → Valuer assigned → Schedules visit
  → Valuer records estimated value + report
  → Status: pending → completed
```

**Survey (for Surveyors):**
```
Client requests survey → Surveyor assigned → Schedules visit
  → Surveyor submits report
  → Status: pending → completed
```

Both generate fees tracked in the system.

---

### 5.14 Notifications

All key actions trigger notifications stored in `notifications_log`:

| Event | Notified |
|-------|---------|
| New bid placed | All previous bidders (outbid) + Auctioneer |
| Auction ended | Winner |
| Lease created | Tenant |
| Rent payment received | Landlord |
| Maintenance request submitted | Landlord / Property Manager |
| Inspection booked | Inspector |
| KYC approved/rejected | User |
| Property enquiry received | Property owner |
| Booking confirmed | Guest |
| Escrow released | Seller |

Dashboard nav shows a live unread count badge (polls every 30 seconds).

---

### 5.15 Analytics Dashboard

Available to: Landlord, Developer, Corporate, Property Manager, Finance

```
Dashboard → Analytics → Last 6 months
```

**Metrics shown:**
- Total property views
- Total saves/wishlists
- Confirmed bookings count
- Total revenue (rent payments + booking payments)
- Stacked bar chart: rent revenue (gold) vs booking revenue (blue) per month
- Property performance table with occupancy % progress bar

---

### 5.16 Admin Panel

Accessible only to `admin` role.

**User Management:**
- List all users with role filter
- View user details (properties, leases, verification status)
- Activate / Deactivate user accounts

**KYC Moderation:**
- Queue of pending verifications
- View uploaded documents
- Approve (sets is_verified=true, tier=verified) or Reject with notes

**Property Moderation:**
- List pending/active properties
- Approve listing → status = active
- Suspend listing → status = suspended
- Toggle featured status

---

## 6. Role-by-Role Flow

### Admin
```
Login → Admin Dashboard
  → View platform stats (users, properties, transactions)
  → KYC queue → approve/reject verifications
  → Property moderation → approve/suspend/feature
  → User management → activate/deactivate accounts
```

### Landlord
```
Login → Landlord Dashboard
  → List a Property → upload images, set price, type, amenities
  → Create Lease → assign tenant, set monthly rent
  → View rent payment history
  → Generate PDF statements and receipts
  → View maintenance requests on their properties
  → Analytics → revenue charts
```

### Tenant
```
Login → Tenant Dashboard
  → View active lease (rent, dates, landlord contact)
  → Pay Rent → M-Pesa STK push or bank transfer
  → Download rent receipt PDF
  → Submit maintenance request
  → Search marketplace for properties
  → Save properties to wishlist
```

### Broker (Licensed / Unlicensed)
```
Login → Broker Dashboard
  → Generate referral link
  → Share with clients
  → Track clicks, conversions, commissions earned
  → List properties on behalf of clients
  → Send messages to property owners
```

### Developer
```
Login → Developer Dashboard
  → Create developer projects (buildings under construction)
  → List properties for sale/rent
  → Track sold vs available units
  → View analytics on property performance
```

### Valuer
```
Login → Valuer Dashboard
  → View assigned valuation requests
  → Update valuation status
  → Submit estimated value and written report
  → Track completed valuations and fees
```

### Surveyor
```
Login → Surveyor Dashboard
  → View assigned survey requests
  → Update survey status
  → Submit survey report
  → Track completed surveys and fees
```

### Auctioneer
```
Login → Auctioneer Dashboard
  → Create auction → link property, set bids, set schedule
  → Monitor live auctions (real-time bid feed)
  → End auction manually → winner notified
  → View bid history and auction results
```

### Investor
```
Login → Investor Dashboard
  → Browse marketplace
  → Save/wishlist properties
  → Compare up to 3 properties
  → View saved properties list
  → Send enquiry to property owners
```

### Corporate
```
Login → Corporate Dashboard
  → List multiple properties
  → Manage leases across portfolio
  → View rent collection summary
  → Analytics per property
```

### Property Manager
```
Login → Property Manager Dashboard
  → Manage properties on behalf of landlords
  → Track maintenance requests
  → Handle tenant communications
  → View rent payment status
```

### Finance
```
Login → Finance Dashboard
  → View rent collection reports
  → Total collected vs pending vs overdue
  → Monthly revenue breakdown
  → Export/view payment history
```

---

## 7. Database Tables

| Table | Purpose |
|-------|---------|
| `users` | All platform users (all roles) |
| `properties` | Property listings with soft delete |
| `leases` | Rental agreements between landlord and tenant |
| `rent_payments` | Individual rent payment records |
| `auctions` | Property auctions |
| `bids` | Bids placed on auctions |
| `bookings` | Airbnb/hotel bookings |
| `hotel_rooms` | Room inventory for hotel properties |
| `property_availability` | Blocked dates for bookings |
| `property_pricing_rules` | Dynamic pricing rules |
| `booking_reviews` | Guest reviews and star ratings |
| `escrow_transactions` | Property sale escrow records |
| `messages` | User-to-user messages |
| `notifications_log` | System notification records |
| `verifications` | KYC verification records |
| `verification_documents` | KYC document file paths |
| `referrals` | Referral codes and tracking |
| `referral_conversions` | Converted referrals |
| `property_documents` | Property file uploads |
| `property_saves` | User wishlist/saved properties |
| `inspections` | Property inspection bookings |
| `maintenance_requests` | Tenant maintenance tickets |
| `developer_projects` | Developer building projects |
| `valuations` | Property valuation records |
| `surveys` | Property survey records |
| `personal_access_tokens` | Sanctum API tokens |
| `sessions` | Laravel file sessions |
| `cache` | Laravel cache |
| `jobs` | Queue jobs |
| `failed_jobs` | Failed queue jobs |
| `migrations` | Laravel migration history |

---

## 8. API Reference

Base URL: `/api/v1`  
Authentication: Bearer token (Sanctum) — obtain via `/api/v1/auth/login`

### Public Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/v1/auth/login` | Login and receive API token |
| POST | `/v1/auth/register` | Register a new user |
| GET | `/v1/properties` | List active properties |
| GET | `/v1/properties/{id}` | Get single property |
| GET | `/v1/auctions` | List auctions by status |
| GET | `/v1/auctions/{id}` | Get auction with bids |
| GET | `/v1/search/suggestions` | Autocomplete search |

### Protected Endpoints (Bearer token required)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/v1/user` | Current user profile |
| GET | `/v1/user/properties` | User's listed properties |
| GET | `/v1/user/leases` | User's leases |
| GET | `/v1/user/payments` | User's rent payments |
| GET | `/v1/user/messages` | User's messages |
| POST | `/v1/messages` | Send a message |
| GET | `/v1/user/notifications` | User's notifications |
| POST | `/v1/properties` | Create a new property |
| POST | `/v1/auctions/{id}/bid` | Place a bid on an auction |
| POST | `/v1/rent/pay` | Initiate a rent payment |

---

## 9. Security

| Feature | Implementation |
|---------|----------------|
| Authentication | Session-based (`session('user_id')`) for web; Sanctum tokens for API |
| Password hashing | bcrypt with 12 rounds |
| Brute force protection | 10 login attempts per 15 minutes (rate limiting) |
| CSRF protection | Laravel default CSRF middleware on all POST routes |
| Role enforcement | `RoleMiddleware` checks `session('role')` on all protected routes |
| Security headers | `SecurityHeaders` middleware sets CSP, X-Frame-Options, XSS Protection, Referrer-Policy, Permissions-Policy |
| SQL injection | Eloquent ORM with prepared statements |
| XSS | Blade auto-escaping `{{ }}` on all output |

---

## 10. File Structure

```
onestoh/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/           # KYC, property moderation, users
│   │   │   ├── Api/V1/          # REST API controllers
│   │   │   └── *.php            # Web controllers (30+)
│   │   └── Middleware/
│   │       ├── AuthMiddleware.php
│   │       ├── RoleMiddleware.php
│   │       └── SecurityHeaders.php
│   ├── Models/                  # 25+ Eloquent models
│   ├── Providers/
│   │   └── AppServiceProvider.php  # View composers, onboarding
│   └── Services/
│       ├── BookingService.php
│       ├── NotificationService.php
│       └── PropertySaleService.php
├── database/
│   ├── migrations/              # 30 migration files
│   └── seeders/                 # DatabaseSeeder (66 users, 50 properties)
├── resources/
│   └── views/
│       ├── admin/               # Admin panel views
│       ├── auctions/            # Auction listing + live bidding
│       ├── auth/                # Login + register
│       ├── bookings/            # Booking workflow views
│       ├── dashboard/           # Per-role dashboard views (13 roles)
│       ├── layouts/             # app.blade.php + dashboard.blade.php
│       ├── marketplace/         # Property listing, show, compare
│       ├── pages/               # About, financing, verification
│       ├── pdf/                 # PDF templates (lease, receipt, statement)
│       ├── properties/          # Create/edit property forms
│       └── search/              # Search results with filters
├── routes/
│   ├── web.php                  # 118+ web routes
│   ├── api.php                  # REST API routes
│   └── console.php              # Scheduled tasks (overdue rent, auto-end auctions)
├── setup/
│   └── estateyard_mysql.sql     # Complete MySQL dump (34 tables, seeded data)
├── setup.bat                    # Windows/WAMP one-click setup
├── setup.sh                     # Linux/Mac one-click setup
└── .env.example                 # Environment template
```

---

## 11. Demo Accounts

All demo accounts use the password: **`password`**

| Role | Email |
|------|-------|
| Admin | admin1@estateyard.co.ke |
| Admin (super) | admin@estateyard.co.ke |
| Landlord | landlord1@estateyard.co.ke |
| Tenant | tenant1@estateyard.co.ke |
| Broker (Licensed) | broker_licensed1@estateyard.co.ke |
| Broker (Unlicensed) | broker_unlicensed1@estateyard.co.ke |
| Developer | developer1@estateyard.co.ke |
| Valuer | valuer1@estateyard.co.ke |
| Surveyor | surveyor1@estateyard.co.ke |
| Auctioneer | auctioneer1@estateyard.co.ke |
| Investor | investor1@estateyard.co.ke |
| Corporate | corporate1@estateyard.co.ke |
| Property Manager | property_manager1@estateyard.co.ke |
| Finance | finance1@estateyard.co.ke |

> Each role has 5 demo users (e.g. `landlord1` through `landlord5`).  
> Users 1–3 of each role have `is_verified = true`. Users 4–5 are unverified.
