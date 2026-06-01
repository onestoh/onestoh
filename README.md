# TheOnlineYard — ERP Platform

> A full-stack, automated marketplace and ERP platform for renting and selling vehicles, heavy machinery, and motor equipment across Africa. M-Pesa-first. Escrow-protected. Zero manual intervention.

**Part of the YardGroup Ecosystem** · Powered by Belasoft Solutions Ltd  
Kenya · East Africa · Pan-African Vision

---

## Table of Contents

- [Project Overview](#project-overview)
- [Tech Stack](#tech-stack)
- [Repository Structure](#repository-structure)
- [Development Phases](#development-phases)
  - [Phase 1 — Foundation & Core Marketplace](#phase-1--foundation--core-marketplace-months-16)
  - [Phase 2 — Fleet ERP & Equipment Module](#phase-2--fleet-erp--equipment-module-months-718)
  - [Phase 3 — AI, Mobile App & Pan-African Expansion](#phase-3--ai-mobile-app--pan-african-expansion-months-1936)
- [Getting Started](#getting-started)
- [Environment Variables](#environment-variables)
- [Contributing](#contributing)
- [License](#license)

---

## Project Overview

TheOnlineYard connects yard owners, fleet operators, individual car owners, and heavy-equipment companies directly with clients — eliminating manual coordination and enabling fully digital, payment-automated rental experiences from booking to key handover.

### What Can Be Listed?

| Category | Rental | Sale |
|---|---|---|
| Passenger Cars (saloons, SUVs, vans) | ✓ | ✓ |
| Pickup Trucks & Commercial Vehicles | ✓ | ✓ |
| Heavy Trucks (lorries, tankers, tippers) | ✓ | ✓ |
| Excavators & Construction Equipment | ✓ | — |
| Agricultural Machinery (tractors, harvesters) | ✓ | ✓ |
| Cranes, Forklifts & Lifts | ✓ | — |
| Generators & Special Equipment | ✓ | — |
| Motorcycles & Tuk-Tuks | ✓ | ✓ |

### Stakeholder Roles

| Role | Description |
|---|---|
| **Super Admin** | Platform operator — full control, KYC review, dispute arbitration |
| **Yard Owner** | Business or individual managing a fleet of vehicles/machines |
| **Individual Owner** | Private owner listing 1–5 assets |
| **Client / Renter** | Individual or corporate booking assets |
| **Broker / Agent** | Commission earner via referral links and QR codes |
| **Operator / Driver** | Licensed driver or machine operator attached to a yard |

---

## Tech Stack

| Layer | Technology | Purpose |
|---|---|---|
| Frontend | Next.js / React | SSR marketplace + SPA dashboards |
| Mobile | React Native | iOS + Android cross-platform app |
| Backend API | Laravel (PHP) | RESTful API, queues, escrow engine |
| Database | MySQL + Redis | Relational data + caching & queues |
| Real-time | Laravel Echo + Pusher | Live calendar, chat, notifications |
| Storage | AWS S3 / Cloudflare R2 | Photos, documents, agreements |
| CDN | Cloudflare | DDoS protection, global caching |
| Payments (Primary) | Safaricom Daraja API | M-Pesa STK Push, B2C, C2B |
| Payments (Secondary) | DPO Group / Flutterwave | Cards, multi-currency, pan-African |
| SMS | Africa's Talking | SMS gateway, East African coverage |
| Email | SendGrid | Transactional + marketing emails |
| Push Notifications | Firebase FCM | Mobile + web push |
| Maps | Google Maps API | Location search, distance, pins |
| KYC | Smile Identity | African ID verification + liveness |
| Search | Algolia / Meilisearch | Full-text listing search |
| PDF | Laravel DomPDF | Agreements, receipts, statements |
| Analytics | Mixpanel + GA4 | Event tracking, funnel analysis |
| Hosting | DigitalOcean / AWS | VPS clusters, auto-scaling |
| DevOps | Docker + GitHub Actions | CI/CD, staging + production |

---

## Repository Structure

```
onestoh/
├── apps/
│   ├── api/              # Laravel backend API
│   ├── web/              # Next.js frontend (marketplace + dashboards)
│   └── mobile/           # React Native app
├── packages/
│   ├── ui/               # Shared UI component library
│   └── types/            # Shared TypeScript types
├── docs/
│   ├── phases/           # Detailed phase implementation plans
│   ├── api/              # API documentation
│   └── architecture/     # Architecture diagrams and decisions
├── infrastructure/
│   ├── docker/           # Docker configs
│   └── ci/               # GitHub Actions workflows
└── README.md
```

---

## Development Phases

### Phase 1 — Foundation & Core Marketplace (Months 1–6)

**Goal:** Launch with 50+ verified yard owners, 200+ active listings in Nairobi. Target: 500 completed rentals by Month 6.

See [`docs/phases/phase-1.md`](docs/phases/phase-1.md) for full details.

#### Phase 1 Milestones

| # | Milestone | Deliverable |
|---|---|---|
| 1.1 | Project Setup & DevOps | Monorepo, Docker, CI/CD, environments |
| 1.2 | Auth & KYC System | Registration, OTP, document upload, admin review |
| 1.3 | User Roles & Dashboards | 6 role dashboards scaffolded |
| 1.4 | Listing System | Multi-step listing creation, photo upload, admin approval |
| 1.5 | Availability Calendar | Real-time calendar, slot holds, conflict prevention |
| 1.6 | Booking Engine | Full automated booking lifecycle (8 stages) |
| 1.7 | M-Pesa Integration | STK Push, Paybill fallback, callback handling |
| 1.8 | Escrow Engine | Hold, release, split, dispute freeze |
| 1.9 | Notification System | SMS + email + push for all 12 trigger events |
| 1.10 | Broker Referral Engine | Unique links, QR codes, commission tracking |
| 1.11 | Admin Control Panel | KYC queue, listings mgmt, payment control |
| 1.12 | Reviews & Trust System | Bidirectional ratings, trust score, flagging |

---

### Phase 2 — Fleet ERP & Equipment Module (Months 7–18)

**Goal:** Kenya-wide expansion, heavy equipment launch, mobile app. Target: 3,000 rentals/month, KES 5M+ monthly GMV.

See [`docs/phases/phase-2.md`](docs/phases/phase-2.md) for full details.

#### Phase 2 Milestones

| # | Milestone | Deliverable |
|---|---|---|
| 2.1 | Fleet ERP Module | Asset registry, maintenance scheduling, utilisation analytics |
| 2.2 | Heavy Equipment Module | Specialised listing fields, project-based booking, mobilisation |
| 2.3 | Car & Machine Sales Module | Sale listings, test drive scheduling, digital agreements |
| 2.4 | Driver Pool Management | Driver registration, auto-assignment, performance scores |
| 2.5 | Insurance Integration | APA / Jubilee add-on at checkout, claim linking |
| 2.6 | Card Payments | DPO Group / Flutterwave card processing, 3DS |
| 2.7 | Airtel Money Integration | Secondary telecom coverage |
| 2.8 | Corporate Accounts | Multi-user company accounts, bulk booking, invoicing |
| 2.9 | Mobile App (React Native) | iOS + Android, all core booking flows |
| 2.10 | Advanced Analytics | Per-asset P&L, geographic heatmaps, funnel analysis |
| 2.11 | Dispute Resolution | Evidence submission, admin arbitration, auto-disbursement |
| 2.12 | Listing Promotion Engine | Featured listings, sponsored placement, banner ads |

---

### Phase 3 — AI, Mobile App & Pan-African Expansion (Months 19–36)

**Goal:** Uganda + Tanzania launch, AI features, platform API. Target: 3 countries, 10,000+ rentals/month, Series A.

See [`docs/phases/phase-3.md`](docs/phases/phase-3.md) for full details.

#### Phase 3 Milestones

| # | Milestone | Deliverable |
|---|---|---|
| 3.1 | AI Pricing Suggestions | Market rate analysis, seasonal demand prediction |
| 3.2 | Smart Search & Recommendations | NLP search, personalised recommendations |
| 3.3 | Fraud Detection Engine | ML anomaly detection on payments and bookings |
| 3.4 | Demand Forecasting | Historical booking analysis, fleet investment signals |
| 3.5 | AI Chat Assistant | In-app assistant for clients, owners, admin |
| 3.6 | AI Photo Verification | Quality check, asset type detection, damage comparison |
| 3.7 | MTN MoMo Integration | Uganda/Rwanda mobile money |
| 3.8 | Tigo Pesa Integration | Tanzania mobile money |
| 3.9 | Multi-currency Engine | KES, UGX, TZS, USD, GBP, EUR |
| 3.10 | PayPal / Stripe | International and diaspora payments |
| 3.11 | Public API & Webhooks | Third-party fleet software integration |
| 3.12 | White-label Mode | YardOS for international operators |

---

## Getting Started

### Prerequisites

- PHP 8.2+
- Node.js 20+
- Docker & Docker Compose
- MySQL 8.0
- Redis 7

### Installation

```bash
# Clone the repository
git clone https://github.com/onestoh/onestoh.git
cd onestoh

# Copy environment files
cp apps/api/.env.example apps/api/.env
cp apps/web/.env.example apps/web/.env.local

# Start all services with Docker
docker-compose up -d

# Install API dependencies
cd apps/api
composer install
php artisan key:generate
php artisan migrate --seed

# Install web dependencies
cd ../web
npm install
npm run dev
```

### Running Tests

```bash
# Backend tests
cd apps/api && php artisan test

# Frontend tests
cd apps/web && npm run test
```

---

## Environment Variables

See [`docs/environment.md`](docs/environment.md) for the full list of required environment variables across all services.

Key integrations requiring credentials:

- `MPESA_CONSUMER_KEY` / `MPESA_CONSUMER_SECRET` — Safaricom Daraja API
- `MPESA_SHORTCODE` / `MPESA_PASSKEY` — STK Push credentials
- `AFRICAS_TALKING_API_KEY` — SMS gateway
- `SENDGRID_API_KEY` — Email
- `FIREBASE_SERVER_KEY` — Push notifications
- `GOOGLE_MAPS_API_KEY` — Maps and location
- `SMILE_IDENTITY_API_KEY` — KYC verification
- `FLUTTERWAVE_SECRET_KEY` — Card and pan-African payments
- `PUSHER_APP_ID` / `PUSHER_APP_KEY` — Real-time WebSocket
- `AWS_ACCESS_KEY_ID` / `AWS_SECRET_ACCESS_KEY` — File storage

---

## Contributing

This is a private platform project. All development is coordinated through the internal Belasoft Solutions team.

---

## License

Proprietary — © 2025 Belasoft Solutions Ltd. All Rights Reserved.
