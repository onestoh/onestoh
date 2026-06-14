@extends('layouts.dashboard')

@section('title', 'Pay for Booking')
@section('page-title', 'Complete Payment')

@push('styles')
<style>
.pay-card { background: var(--black); border: 1px solid var(--border); border-radius: 12px; padding: 1.5rem; }
.form-control { background: var(--surface); border: 1px solid var(--border); color: var(--text); }
.form-control:focus { background: var(--surface); border-color: var(--amber); color: var(--text); box-shadow: 0 0 0 2px rgba(232,146,42,.15); }
.cost-row { display: flex; justify-content: space-between; padding: .4rem 0; font-size: .875rem; border-bottom: 1px solid var(--border); }
.cost-row:last-child { border: none; font-weight: 700; font-size: 1rem; }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="{{ route('bookings.show', $booking) }}" style="color:var(--muted)"><i class="fas fa-arrow-left"></i></a>
            <h4 style="color:var(--text);font-weight:700;margin:0">Complete Payment</h4>
        </div>

        {{-- Booking summary --}}
        <div class="pay-card mb-4">
            <h6 style="color:var(--amber);font-size:.75rem;text-transform:uppercase;letter-spacing:1px;margin-bottom:.75rem">Booking Summary</h6>
            <div style="color:var(--text);font-weight:600;margin-bottom:.25rem">{{ $booking->listing?->title }}</div>
            <div style="color:var(--muted);font-size:.82rem;margin-bottom:.75rem">
                <i class="fas fa-hashtag me-1"></i>{{ $booking->booking_ref }} &nbsp;·&nbsp;
                {{ \Carbon\Carbon::parse($booking->start_datetime)->format('d M Y') }} → {{ \Carbon\Carbon::parse($booking->end_datetime)->format('d M Y') }}
            </div>
            <div class="cost-row"><span style="color:var(--muted)">Base Amount</span><span style="color:var(--text)">KES {{ number_format($booking->base_amount) }}</span></div>
            <div class="cost-row"><span style="color:var(--muted)">Platform Fee</span><span style="color:var(--text)">KES {{ number_format($booking->platform_fee) }}</span></div>
            @if($booking->security_deposit)
            <div class="cost-row"><span style="color:var(--muted)">Security Deposit</span><span style="color:var(--text)">KES {{ number_format($booking->security_deposit) }}</span></div>
            @endif
            <div class="cost-row mt-2 pt-1" style="border-top:1px solid var(--border)">
                <span style="color:var(--text)">Total Due</span>
                <span style="color:var(--amber)">KES {{ number_format($booking->total_amount) }}</span>
            </div>

            @if($booking->slot_hold_expires_at && !$booking->isHoldExpired())
            <div class="mt-3 p-2 rounded-2" style="background:rgba(232,146,42,.08);border:1px solid rgba(232,146,42,.2);font-size:.8rem;color:var(--amber)">
                <i class="fas fa-clock me-1"></i>Slot held until {{ \Carbon\Carbon::parse($booking->slot_hold_expires_at)->format('H:i, d M') }}
            </div>
            @endif
        </div>

        {{-- M-Pesa Payment --}}
        <div class="pay-card">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div style="width:48px;height:48px;border-radius:10px;background:rgba(46,204,138,.1);display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-mobile-alt" style="color:var(--green);font-size:1.3rem"></i>
                </div>
                <div>
                    <div style="color:var(--text);font-weight:700">M-Pesa STK Push</div>
                    <div style="color:var(--muted);font-size:.82rem">Enter your M-Pesa number to receive a payment prompt</div>
                </div>
            </div>

            <form method="POST" action="{{ route('bookings.mpesa', $booking) }}">
                @csrf
                <div class="mb-4">
                    <label style="color:var(--muted);font-size:.82rem;display:block;margin-bottom:.3rem">M-Pesa Phone Number</label>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                        value="{{ old('phone', auth()->user()->phone) }}"
                        placeholder="07XXXXXXXX or 254XXXXXXXXX" required>
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div style="color:var(--muted);font-size:.75rem;margin-top:.25rem">You will receive a prompt on this number to enter your M-Pesa PIN</div>
                </div>

                <button type="submit" class="btn w-100 py-2 fw-semibold" style="background:var(--green);color:#000;font-size:1rem">
                    <i class="fas fa-mobile-alt me-2"></i>Send M-Pesa Prompt — KES {{ number_format($booking->total_amount) }}
                </button>
            </form>

            <div class="mt-3 text-center" style="color:var(--muted);font-size:.78rem">
                <i class="fas fa-lock me-1"></i>Secured by Safaricom Daraja API · Payment processed by M-Pesa
            </div>
        </div>
    </div>
</div>
@endsection
