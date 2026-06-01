# Environment Variables Reference

All sensitive values must be stored in `.env` files — never committed to version control.

---

## Backend (apps/api/.env)

```env
# App
APP_NAME="TheOnlineYard"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://api.theonlineyard.co.ke

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=theonlineyard
DB_USERNAME=
DB_PASSWORD=

# Redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=

# M-Pesa Daraja API (Safaricom)
MPESA_ENVIRONMENT=production          # sandbox | production
MPESA_CONSUMER_KEY=
MPESA_CONSUMER_SECRET=
MPESA_SHORTCODE=                       # Paybill or Till number
MPESA_PASSKEY=                         # STK Push passkey
MPESA_CALLBACK_URL=https://api.theonlineyard.co.ke/api/payments/mpesa/callback
MPESA_B2C_INITIATOR_NAME=
MPESA_B2C_INITIATOR_PASSWORD=
MPESA_B2C_SECURITY_CREDENTIAL=

# Africa's Talking (SMS)
AFRICAS_TALKING_USERNAME=
AFRICAS_TALKING_API_KEY=
AFRICAS_TALKING_FROM=TheOnlineYard

# SendGrid (Email)
SENDGRID_API_KEY=
MAIL_FROM_ADDRESS=noreply@theonlineyard.co.ke
MAIL_FROM_NAME="TheOnlineYard"

# Firebase (Push Notifications)
FIREBASE_SERVER_KEY=
FIREBASE_PROJECT_ID=

# Pusher / Laravel Echo (Real-time)
PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=eu

# AWS S3 / Cloudflare R2 (File Storage)
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=eu-west-1
AWS_BUCKET=theonlineyard-storage
AWS_ENDPOINT=                          # For Cloudflare R2

# Google Maps
GOOGLE_MAPS_API_KEY=

# Smile Identity (KYC)
SMILE_IDENTITY_API_KEY=
SMILE_IDENTITY_PARTNER_ID=
SMILE_IDENTITY_ENVIRONMENT=Production  # Test | Production

# Flutterwave (Pan-African Payments)
FLUTTERWAVE_PUBLIC_KEY=
FLUTTERWAVE_SECRET_KEY=
FLUTTERWAVE_ENCRYPTION_KEY=

# DPO Group (Card Payments - Kenya)
DPO_COMPANY_TOKEN=
DPO_SERVICE_TYPE=

# Algolia / Meilisearch (Search)
SCOUT_DRIVER=meilisearch
MEILISEARCH_HOST=
MEILISEARCH_KEY=

# WhatsApp Business API (Optional)
WHATSAPP_TOKEN=
WHATSAPP_PHONE_NUMBER_ID=

# Platform Config
PLATFORM_SERVICE_FEE_PERCENT=10        # % deducted from each rental on escrow release
PLATFORM_MIN_PAYOUT_KES=500
SLOT_HOLD_MINUTES=15                   # Cart hold duration before slots released
```

---

## Frontend (apps/web/.env.local)

```env
NEXT_PUBLIC_API_URL=https://api.theonlineyard.co.ke
NEXT_PUBLIC_PUSHER_KEY=
NEXT_PUBLIC_PUSHER_CLUSTER=eu
NEXT_PUBLIC_GOOGLE_MAPS_KEY=
NEXT_PUBLIC_FIREBASE_API_KEY=
NEXT_PUBLIC_FIREBASE_AUTH_DOMAIN=
NEXT_PUBLIC_FIREBASE_PROJECT_ID=
NEXT_PUBLIC_MIXPANEL_TOKEN=
NEXT_PUBLIC_GA4_ID=
```

---

## Notes

- All M-Pesa credentials are obtained from the [Safaricom Developer Portal](https://developer.safaricom.co.ke)
- Smile Identity credentials from [Smile Identity Dashboard](https://docs.usesmileid.com)
- Africa's Talking credentials from [Africa's Talking Dashboard](https://africastalking.com)
- Firebase credentials from [Firebase Console](https://console.firebase.google.com)
- Never use production M-Pesa credentials in development — always use the Daraja sandbox
