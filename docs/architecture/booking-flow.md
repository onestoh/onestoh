# Booking Flow Architecture

## Overview

The booking engine is the core of TheOnlineYard. All state transitions are handled server-side via queued jobs — no booking state can be changed by direct client API calls after payment confirmation.

## State Machine

```
pending_payment
    ↓  (M-Pesa STK Push triggered)
payment_processing
    ↓  (Daraja callback: payment confirmed)
confirmed
    ↓  (Notification jobs dispatched)
owner_notified
    ↓  (Pickup instructions sent to client)
client_prepared
    ↓  (Pre-rental photos submitted)
active
    ↓  (Post-rental photos + 2hr no-dispute OR 24hr auto-release)
completed
    ↓  (Escrow released, reviews requested)
closed
```

## Cancellation Paths

```
pending_payment  → cancelled_by_client   (no charge)
payment_processing → cancelled_by_system (payment failed)
confirmed        → cancelled_by_client   (per cancellation policy refund)
confirmed        → cancelled_by_admin    (full refund)
active           → disputed              (escrow frozen)
disputed         → resolved              (admin ruling, escrow split)
```

## Availability Slot State Machine

```
available
    ↓  (client selects dates, 15-min hold placed)
pending
    ↓  (payment confirmed)
confirmed
    ↓  (rental completed)
available  (slots released back)

available
    ↓  (owner manually blocks)
owner_blocked
    ↓  (owner unblocks)
available
```

## Key Technical Notes

1. **Slot holds** use Redis `SETEX` with 900s TTL — no DB write needed for temporary holds. On expiry, a Redis keyspace notification triggers the slot release job.

2. **Concurrency control:** Slot confirmation uses a database-level lock (`SELECT FOR UPDATE`) to prevent race conditions when multiple clients attempt the same slot simultaneously.

3. **Payment callbacks** are idempotent — duplicate Daraja callbacks (Safaricom sends retries) are detected via `transaction_id` uniqueness constraint and silently ignored.

4. **Escrow release** is a queued job dispatched after the release trigger fires. It runs in a separate queue (`escrow`) with a single worker to prevent concurrent release of the same booking.

5. **Driver auto-assignment** uses a priority queue: first available driver with the correct licence class, sorted by last assignment time (round-robin fairness).
