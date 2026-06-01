# EstateYard ERP Platform

A comprehensive real estate ERP platform built on Laravel 13 and PHP 8.4, serving the Kenyan property market. EstateYard connects landlords, tenants, brokers, developers, auctioneers, investors, valuers, surveyors, and property managers on a single unified platform.

---

## Features

### Phase 1 — Core Marketplace
- Property listings (sale, rent, airbnb, hotel, auction)
- Advanced search and filtering by county, type, price range
- Property detail pages with image gallery and maps
- User registration for 13 distinct roles

### Phase 2 — Lease & Rent Management
- Digital lease creation and signing
- Automated rent payment tracking (M-Pesa, card, bank)
- Overdue detection and reminders
- Maintenance request ticketing

### Phase 3 — Auctions & Escrow
- Live auction bidding with real-time bid updates
- Upcoming and ended auction management
- Escrow transaction tracking
- Bid history and winner tracking

### Phase 4 — Airbnb & Hotel Bookings
- Short-stay (Airbnb) and hotel room bookings
- Calendar availability blocking
- M-Pesa payment integration
- Booking confirmation and check-in flow

### Phase 5 — Professional Dashboards
- 13 role-specific dashboards (admin, landlord, tenant, broker, developer, valuer, surveyor, auctioneer, investor, corporate, property manager, finance, promoter)
- Developer project tracking (units sold vs. total)
- Valuation and survey job management
- Finance overview with escrow and rent summaries
- PDF report generation
- Referral program with earnings tracking

---

## Tech Stack

- **Framework**: Laravel 13
- **Language**: PHP 8.4
- **Database**: SQLite (dev) / MySQL (production)
- **Auth**: Custom session-based auth
- **API**: Laravel Sanctum REST API
- **PDF**: DomPDF (barryvdh/laravel-dompdf)
- **Frontend**: Blade templates, Vanilla JS, Tailwind CSS
- **Storage**: Laravel Storage (local/S3)

---

## Installation

```bash
git clone https://github.com/onestoh/onestoh.git
cd onestoh
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
```

Visit `http://localhost:8000`

---

## Demo Credentials

All accounts use password: `password`

| Role | Email |
|------|-------|
| Admin | admin1@estateyard.co.ke |
| Landlord | landlord1@estateyard.co.ke |
| Tenant | tenant1@estateyard.co.ke |
| Broker (Licensed) | broker1@estateyard.co.ke |
| Broker (Unlicensed/Promoter) | promoter1@estateyard.co.ke |
| Developer | developer1@estateyard.co.ke |
| Valuer | valuer1@estateyard.co.ke |
| Surveyor | surveyor1@estateyard.co.ke |
| Auctioneer | auctioneer1@estateyard.co.ke |
| Investor | investor1@estateyard.co.ke |
| Corporate | corporate1@estateyard.co.ke |
| Property Manager | manager1@estateyard.co.ke |
| Finance | finance1@estateyard.co.ke |

---

## API Endpoints

```
GET  /api/v1/properties              List properties (filterable)
GET  /api/v1/properties/{id}         Property detail
GET  /api/v1/auctions                List auctions
GET  /api/v1/auctions/{id}           Auction detail
GET  /api/search/suggestions?q=      Search autocomplete
GET  /bookings/availability/{id}     Property availability calendar
POST /bookings                       Create a booking
POST /bookings/{id}/payment          Process payment
```

---

## Phase Roadmap

| Phase | Status | Description |
|-------|--------|-------------|
| 1 | Done | Marketplace, listings, search, public pages |
| 2 | Done | Lease management, rent payments, maintenance |
| 3 | Done | Auctions, bidding, escrow |
| 4 | Done | Airbnb/hotel bookings, availability calendar |
| 5 | Done | All 13 dashboards, PDF reports, referrals |
| 6 | Planned | Mobile app (React Native), SMS notifications |

---

## License

MIT License — see [LICENSE](LICENSE) for details.
