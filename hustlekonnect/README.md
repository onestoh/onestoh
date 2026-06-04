# 🚗 HustleKonnect

> Pan-African vehicle & heavy-machinery rental/sales marketplace — M-Pesa-first, escrow-protected, zero-human-involvement booking automation.

[![Laravel](https://img.shields.io/badge/Laravel-11-red)](https://laravel.com)
[![Next.js](https://img.shields.io/badge/Next.js-14-black)](https://nextjs.org)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-blue)](https://mysql.com)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)

## 📋 Table of Contents
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Quick Start (Docker)](#quick-start-docker)
- [Manual Installation](#manual-installation)
- [Database Setup](#database-setup)
- [Environment Variables](#environment-variables)
- [Running the App](#running-the-app)
- [Security Features](#security-features)
- [API Documentation](#api-documentation)
- [Project Structure](#project-structure)

---

## ✨ Features

- **🔒 Zero-trust security** — brute-force lockout, SQL injection detection, XSS sanitization, CSP headers
- **🤖 Full booking automation** — booking confirms, activates, and completes without human intervention
- **💳 Multi-gateway payments** — M-Pesa, MTN MoMo, Flutterwave, Stripe, Paystack, Wallet
- **🏦 Escrow engine** — funds held securely, auto-released 24hr after completion
- **📍 IoT GPS telematics** — live fleet tracking, geofences, trip scoring
- **🏛️ Government procurement** — tender portal with weighted bid scoring
- **📊 ERP integrations** — QuickBooks & Xero OAuth sync
- **🌍 6 African markets** — KE, UG, TZ, NG, GH, ZA with local payment methods
- **🤝 Fleet financing** — loan calculator, lease-to-own, partner bank integration
- **🔔 Owner notifications** — SMS + email + push at every booking stage
- **🧠 AI pricing** — Claude-powered dynamic pricing suggestions
- **🏢 White-label** — YardOS multi-tenancy via subdomain

---

## 🛠 Tech Stack

| Layer | Technology |
|---|---|
| Backend API | Laravel 11, PHP 8.2+, Sanctum 4 |
| Frontend | Next.js 14, TypeScript, Tailwind CSS |
| Mobile | React Native 0.74 |
| Database | **MySQL 8.0** (primary), Redis 7 (cache/queue) |
| Queue | Laravel Horizon + Redis |
| Storage | AWS S3 / local |
| Auth | Laravel Sanctum + OTP (Africa's Talking) |
| Payments | M-Pesa Daraja, Flutterwave, Paystack, Stripe, MTN MoMo |
| KYC | Smile Identity |
| AI | Anthropic Claude API |
| Notifications | Africa's Talking SMS, SendGrid, Firebase FCM |

---

## 🚀 Quick Start (Docker)

**Prerequisites:** Docker 24+, Docker Compose v2

```bash
# 1. Clone the repository
git clone https://github.com/onestoh/hustlekonnect.git
cd hustlekonnect

# 2. Copy environment file
cp apps/api/.env.example apps/api/.env

# 3. Start all services (MySQL auto-imports hustlekonnect.sql)
docker compose -f infrastructure/docker/docker-compose.yml up -d

# 4. Wait 30s for MySQL to initialize, then:
docker compose -f infrastructure/docker/docker-compose.yml exec api php artisan key:generate
docker compose -f infrastructure/docker/docker-compose.yml exec api php artisan migrate --force
docker compose -f infrastructure/docker/docker-compose.yml exec api php artisan db:seed

# 5. Open the app
#   Web:  http://localhost:3000
#   API:  http://localhost:8000/api/v1
```

---

## 🔧 Manual Installation

### Requirements

| Tool | Minimum Version |
|---|---|
| PHP | 8.2+ |
| Composer | 2.6+ |
| Node.js | 20 LTS |
| MySQL | 8.0+ |
| Redis | 7+ |

### Step 1 — Clone

```bash
git clone https://github.com/onestoh/hustlekonnect.git
cd hustlekonnect
```

### Step 2 — Database

```bash
# Create database
mysql -u root -p -e "
  CREATE DATABASE hustlekonnect CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
  CREATE USER 'hk_user'@'localhost' IDENTIFIED BY 'your_strong_password';
  GRANT ALL PRIVILEGES ON hustlekonnect.* TO 'hk_user'@'localhost';
  FLUSH PRIVILEGES;
"

# Import full schema + seed data
mysql -u hk_user -p hustlekonnect < database/hustlekonnect.sql
```

### Step 3 — Backend API

```bash
cd apps/api
composer install
cp .env.example .env
# Edit .env with your credentials
php artisan key:generate
php artisan storage:link
```

### Step 4 — Frontend

```bash
cd apps/web
npm install
cp .env.local.example .env.local
# Set NEXT_PUBLIC_API_URL=http://localhost:8000
```

### Step 5 — Start

```bash
# API (Terminal 1)
cd apps/api && php artisan serve

# Queue worker (Terminal 2 — required for payments + notifications)
cd apps/api && php artisan queue:work redis --queue=payments,notifications,bookings,default

# Frontend (Terminal 3)
cd apps/web && npm run dev
```

### Step 6 — Health Check

```bash
cd apps/api && php artisan system:health-check
```

---

## 🗄️ Database Setup

### Import SQL (recommended)

```bash
mysql -u root -p hustlekonnect < database/hustlekonnect.sql
```

The SQL file includes:
- All 40+ table definitions
- Indexes and foreign keys
- Seed data: 6 market configs (KE/UG/TZ/NG/GH/ZA), 5 financing partners, 4 data products
- Default admin user (`admin@hustlekonnect.com` / password: `password` — **change immediately**)

### Via Migrations

```bash
cd apps/api
php artisan migrate --force
php artisan db:seed
```

---

## ⚙️ Key Environment Variables

| Variable | Description |
|---|---|
| `DB_HOST` | MySQL host (default: 127.0.0.1) |
| `DB_DATABASE` | Database name (hustlekonnect) |
| `DB_USERNAME` | MySQL username |
| `DB_PASSWORD` | MySQL password |
| `MPESA_CONSUMER_KEY` | Safaricom Daraja API key |
| `MPESA_CONSUMER_SECRET` | Safaricom Daraja secret |
| `PAYSTACK_SECRET_KEY` | Paystack (Nigeria/Ghana) |
| `STRIPE_SECRET` | Stripe (international) |
| `AFRICASTALKING_API_KEY` | SMS notifications |
| `ANTHROPIC_API_KEY` | AI pricing + chat |
| `SMILE_IDENTITY_PARTNER_ID` | KYC verification |

Full list: see `apps/api/.env.example`

---

## 🔐 Security Features

| Feature | Details |
|---|---|
| Brute-force protection | 5 attempts → 15 min lockout · 10 → 1 hr · 20 → 24 hr |
| Rate limiting | Auth: 20/min · Payments: 30/min · API: 120/min |
| SQL injection detection | 10 regex patterns, request blocked + security log entry |
| XSS sanitization | All string inputs strip_tags + htmlspecialchars (passwords exempt) |
| Security headers | HSTS, X-Frame-Options: DENY, CSP, X-Content-Type-Options on every response |
| File upload security | Extension blocklist (.php/.exe/.sh), MIME-type vs extension verification |
| KYC gating | Bookings/payments require verified identity |
| Password policy | Min 8 chars, must contain letters + numbers + symbols, HaveIBeenPwned check |
| CORS | Strict origin allowlist |
| Input sanitization | Global middleware strips dangerous content from all requests |

---

## 📡 API Endpoints (Summary)

Base: `http://localhost:8000/api/v1`  
Auth: `Authorization: Bearer {token}`

| Group | Endpoints |
|---|---|
| Auth | POST /auth/register, /auth/login, /auth/verify-otp, /auth/logout |
| Listings | GET/POST /listings, GET/PUT/DELETE /listings/{id} |
| Bookings | POST /bookings, GET /bookings, GET/POST /bookings/{id} |
| Payments | POST /payments/initiate, GET /payments/{id}/status |
| Wallet | GET /wallet/balance, POST /wallet/withdraw |
| KYC | POST /kyc/submit, GET /kyc/status |
| Telematics | GET /owner/telematics/devices, GET /owner/geofences |
| Financing | GET /financing/partners, POST /financing/apply |
| Procurement | GET /procurement/tenders, POST /procurement/tenders/{id}/bid |
| ERP | GET/POST /erp/integrations, POST /erp/integrations/{id}/sync |
| Admin | /admin/users, /admin/kyc, /admin/listings, /admin/disputes, /admin/analytics |

---

## 📁 Project Structure

```
hustlekonnect/
├── apps/
│   ├── api/                    # Laravel 11 REST API
│   │   ├── app/Http/Controllers/Api/    # 30+ controllers
│   │   ├── app/Http/Middleware/         # Security middleware
│   │   ├── app/Http/Requests/           # Form validation
│   │   ├── app/Models/                  # 40+ Eloquent models
│   │   ├── app/Services/                # 30+ business services
│   │   ├── app/Jobs/                    # Queue jobs
│   │   ├── app/Observers/               # Model observers (automation)
│   │   ├── database/migrations/         # 40+ migrations
│   │   └── routes/api*.php              # All API routes
│   ├── web/                    # Next.js 14 frontend
│   └── mobile/                 # React Native 0.74
├── database/
│   └── hustlekonnect.sql       # ⬅ Complete MySQL schema + seed data
├── infrastructure/
│   ├── docker/docker-compose.yml
│   ├── nginx/hustlekonnect.conf
│   └── supervisor/hustlekonnect.conf
├── INSTALL.md                  # Detailed installation guide
├── .env.example                # Environment template
└── README.md
```

---

## 🔄 Moving to Your Own Repo

This project lives in `onestoh/onestoh` under the `hustlekonnect/` folder. To make it standalone:

```bash
git clone https://github.com/onestoh/onestoh.git
cd onestoh
git subtree split --prefix=hustlekonnect -b hustlekonnect-only
git checkout hustlekonnect-only

# Push to your new repo
git remote add hustlekonnect https://github.com/onestoh/hustlekonnect.git
git push hustlekonnect hustlekonnect-only:main
```

---

## 📄 License

MIT — see [LICENSE](LICENSE)
