# Phase 3 — AI, Mobile App & Pan-African Expansion

**Timeline:** Months 19–36  
**Goal:** East Africa expansion (Uganda, Tanzania), AI-powered features, platform API launch.  
**Target:** 3 countries, 10,000+ rentals/month, Series A funding.

---

## Milestone 3.1 — AI Pricing Suggestions

### Deliverables
- Comparable listing analysis: same location + category → suggested optimal rate
- Seasonal demand prediction: rate increase recommendations before peak periods
- Under-pricing alerts: warn owner if rate is below market average
- "Set Suggested Rate" one-click to apply AI recommendation
- Owner can accept, dismiss, or customise the suggestion

---

## Milestone 3.2 — Smart Search & Recommendations

### Deliverables
- Natural language search: *"4x4 SUV for the weekend under 5,000"*
- Personalised listing recommendations based on browse and booking history
- "Clients who rented this also looked at..." cross-recommendation
- Voice search support (mobile app)
- NLP query parser: extracts asset type, price range, dates, location from free text

---

## Milestone 3.3 — Fraud Detection Engine

### Deliverables
- ML-based anomaly detection on payments and booking patterns
- Flags: multiple bookings from same IP in short window
- Flags: rapid KYC submissions with similar documents (duplicate accounts)
- Flags: unusual payout patterns (velocity checks)
- Auto-escalation to admin on fraud score threshold breach
- Manual override: admin can clear or confirm flags

---

## Milestone 3.4 — Demand Forecasting

### Deliverables
- Historical booking data model → demand prediction per asset category per region
- High-demand period alerts to yard owners (public holidays, major events, tourism peaks)
- Market-wide supply vs. demand gap reports for admin
- Fleet investment signals: *"Add 2 more SUVs for the December period in Nairobi"*

---

## Milestone 3.5 — AI Chat Assistant

### Deliverables
- In-app AI assistant:
  - **Clients:** FAQ answers, listing finder, process explainer
  - **Owners:** Analytics interpretation, listing improvement suggestions
  - **Admin:** KYC queue summary, urgent dispute flags
- Trained on platform T&Cs, listing data, FAQ content
- Escalation path: AI → human support agent on unresolved queries
- Claude API integration (Anthropic) — Sonnet model for balanced cost/performance

---

## Milestone 3.6 — AI Photo Verification

### Deliverables
- Auto-reject blurry or low-quality listing photos (quality score threshold)
- Asset type validation: confirm photo matches declared category (car vs. truck vs. machinery)
- Pre/post rental photo comparison for damage assessment assistance in disputes
- Suspicious profile photo detection (stock image fingerprinting)
- Integration point: Cloudinary AI or custom model on AWS Rekognition

---

## Milestone 3.7 — Uganda Launch (MTN MoMo)

### Deliverables
- MTN Mobile Money API integration (Uganda)
- UGX currency support in pricing engine
- Ugandan ID document types in KYC system
- Local legal framework: Ugandan rental agreement template
- Localised SMS content in Ugandan English
- Uganda-specific asset categories (boda bodas prominent)

---

## Milestone 3.8 — Tanzania Launch (Tigo Pesa / Airtel)

### Deliverables
- Tigo Pesa API integration (Tanzania)
- Airtel Money Africa API (Tanzania + Rwanda)
- TZS currency support
- Tanzanian ID document types in KYC
- Tanzanian rental agreement template
- Swahili language option (optional for MVP)

---

## Milestone 3.9 — Multi-Currency Engine

### Deliverables
- Supported currencies: KES, UGX, TZS, USD, GBP, EUR
- Daily exchange rate fetch (Open Exchange Rates API)
- Price display in user's preferred currency
- Payouts in local currency regardless of booking currency
- Admin can lock exchange rate per region or use live rates

---

## Milestone 3.10 — PayPal / Stripe Integration

### Deliverables
- Stripe: USD / EUR / GBP card processing for international clients
- PayPal: diaspora bookings and USD payments
- Automatic currency conversion at checkout
- International receipt format (invoices in booking currency)

---

## Milestone 3.11 — Public API & Webhooks

### Deliverables
- RESTful Public API with OAuth 2.0 client credentials
- Endpoints: listing availability, booking creation, booking status, payout status
- Webhook subscriptions: booking events, payment events, escrow events
- API developer portal with documentation (Swagger / Redoc)
- Rate limiting: 1,000 req/min per API client
- Use cases: fleet management software integration, corporate travel tools

---

## Milestone 3.12 — White-Label Mode (YardOS)

### Deliverables
- Multi-tenant architecture: each white-label instance has its own domain, branding, fee config
- Operator admin panel to manage their tenant's configuration
- Shared platform infrastructure (no separate deployment per tenant)
- Licensing fee model: setup fee + monthly SaaS fee + per-transaction fee
- Target markets: franchise operators in Nigeria, Ghana, South Africa

---

## Phase 3 — Expansion Readiness Checklist

- [ ] Daraja API rate structures reviewed for volume discounts
- [ ] MTN MoMo Uganda sandbox tested end-to-end
- [ ] Tigo Pesa / Airtel Tanzania sandbox tested
- [ ] Legal counsel engaged in Uganda and Tanzania for rental agreement localisation
- [ ] Series A pitch deck updated with Phase 2 GMV and growth metrics
- [ ] White-label architecture design reviewed by CTO
- [ ] AI model training dataset requirements defined (min 10,000 completed bookings)
