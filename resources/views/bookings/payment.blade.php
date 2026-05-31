@extends('layouts.app')
@section('title', 'Payment — Booking #' . $booking->id . ' — EstateYard')

@section('content')
<div style="padding-top:90px; background:var(--navy); min-height:100vh;">
<div class="container" style="padding:32px; max-width:640px; margin:0 auto;">

  <div style="text-align:center; margin-bottom:32px;">
    <div style="font-size:48px; margin-bottom:12px;">💳</div>
    <h1 style="font-family:var(--font-serif); font-size:32px; color:var(--white); margin-bottom:8px;">Complete Your Payment</h1>
    <p style="color:var(--muted); font-size:14px;">Booking reference: <span style="color:var(--gold); font-family:var(--font-mono);">BOOK-{{ $booking->id }}-{{ \Carbon\Carbon::parse($booking->check_in)->format('Ymd') }}</span></p>
  </div>

  <!-- Booking summary card -->
  <div style="background:var(--navy3); border:1px solid var(--border); border-radius:var(--radius); padding:24px; margin-bottom:24px;">
    <div style="font-size:13px; font-weight:600; color:var(--muted); text-transform:uppercase; letter-spacing:1px; margin-bottom:16px;">Booking Summary</div>
    <div style="display:flex; align-items:center; gap:16px; margin-bottom:16px; padding-bottom:16px; border-bottom:1px solid var(--border-dim);">
      <div style="width:64px; height:64px; background:var(--surface); border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:32px;">🏠</div>
      <div>
        <div style="font-size:16px; font-weight:600; color:var(--white);">{{ $booking->property->title ?? 'Property' }}</div>
        <div style="font-size:13px; color:var(--muted);">📍 {{ $booking->property->location ?? '' }}, {{ $booking->property->county ?? '' }}</div>
        @if($booking->room)
        <div style="font-size:12px; color:var(--gold);">{{ $booking->room->name }}</div>
        @endif
      </div>
    </div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px; padding-bottom:16px; border-bottom:1px solid var(--border-dim);">
      <div>
        <div style="font-size:11px; color:var(--muted); text-transform:uppercase; letter-spacing:1px;">Check-in</div>
        <div style="font-size:14px; color:var(--white); font-weight:600;">{{ \Carbon\Carbon::parse($booking->check_in)->format('D, d M Y') }}</div>
        <div style="font-size:12px; color:var(--muted);">After 2:00 PM</div>
      </div>
      <div>
        <div style="font-size:11px; color:var(--muted); text-transform:uppercase; letter-spacing:1px;">Check-out</div>
        <div style="font-size:14px; color:var(--white); font-weight:600;">{{ \Carbon\Carbon::parse($booking->check_out)->format('D, d M Y') }}</div>
        <div style="font-size:12px; color:var(--muted);">Before 11:00 AM</div>
      </div>
    </div>
    <div style="display:flex; justify-content:space-between; margin-bottom:8px; font-size:13px; color:var(--muted);">
      <span>KES {{ number_format($booking->base_price_per_night) }} × {{ $booking->nights }} nights</span>
      <span>KES {{ number_format($booking->base_price_per_night * $booking->nights) }}</span>
    </div>
    <div style="display:flex; justify-content:space-between; margin-bottom:8px; font-size:13px; color:var(--muted);">
      <span>Cleaning fee</span>
      <span>KES {{ number_format($booking->cleaning_fee) }}</span>
    </div>
    <div style="display:flex; justify-content:space-between; margin-bottom:8px; font-size:13px; color:var(--muted);">
      <span>Service fee (5%)</span>
      <span>KES {{ number_format($booking->service_fee) }}</span>
    </div>
    <div style="display:flex; justify-content:space-between; padding-top:12px; border-top:1px solid var(--border); font-size:18px; font-weight:700; color:var(--white);">
      <span>Total</span>
      <span style="color:var(--gold);">KES {{ number_format($booking->total_price) }}</span>
    </div>
  </div>

  <!-- Payment form -->
  <form method="POST" action="{{ route('booking.pay', $booking->id) }}" id="paymentForm">
    @csrf

    <!-- M-Pesa -->
    <div id="mpesaSection" style="background:var(--navy3); border:1px solid var(--border); border-radius:var(--radius); padding:24px; margin-bottom:16px;">
      <div style="display:flex; align-items:center; gap:12px; margin-bottom:16px;">
        <div style="width:44px; height:44px; border-radius:10px; background:#00b300; display:flex; align-items:center; justify-content:center; font-size:20px; font-weight:700; color:white;">M</div>
        <div>
          <div style="font-size:15px; font-weight:600; color:var(--white);">Lipa Na M-Pesa</div>
          <div style="font-size:12px; color:var(--muted);">STK Push to your phone</div>
        </div>
        <label style="display:flex; align-items:center; gap:6px; margin-left:auto; cursor:pointer;">
          <input type="radio" name="payment_method" value="mpesa" checked style="accent-color:var(--gold);">
          <span style="font-size:13px; color:var(--muted);">Select</span>
        </label>
      </div>
      <div class="form-group" style="margin-bottom:0;">
        <label class="form-label">M-Pesa Phone Number</label>
        <input type="tel" name="phone" class="form-control" placeholder="0700 000 000" id="mpesaPhone">
        <div style="font-size:12px; color:var(--muted); margin-top:6px;">You'll receive an STK push prompt on this number</div>
      </div>
    </div>

    <!-- Bank Transfer -->
    <div id="bankSection" style="background:var(--navy3); border:1px solid var(--border-dim); border-radius:var(--radius); padding:20px; margin-bottom:24px; opacity:0.6;">
      <div style="display:flex; align-items:center; gap:12px; margin-bottom:0;">
        <div style="width:44px; height:44px; border-radius:10px; background:var(--blue); display:flex; align-items:center; justify-content:center; font-size:20px;">🏦</div>
        <div>
          <div style="font-size:15px; font-weight:600; color:var(--white);">Bank Transfer</div>
          <div style="font-size:12px; color:var(--muted);">KCB Bank · A/C: 1234567890</div>
        </div>
        <label style="display:flex; align-items:center; gap:6px; margin-left:auto; cursor:pointer;">
          <input type="radio" name="payment_method" value="bank" style="accent-color:var(--gold);">
          <span style="font-size:13px; color:var(--muted);">Select</span>
        </label>
      </div>
    </div>

    <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center; padding:16px; font-size:16px;" id="payBtn">
      <span id="payBtnText">💳 Pay KES {{ number_format($booking->total_price) }}</span>
      <span id="payBtnSpinner" style="display:none;">⏳ Processing...</span>
    </button>
    <p style="text-align:center; font-size:12px; color:var(--muted); margin-top:12px;">🔒 Secured by EstateYard · Your payment is protected</p>
  </form>

  <div style="text-align:center; margin-top:16px;">
    <a href="{{ route('booking.cancel', $booking->id) }}" onclick="return confirm('Cancel this booking?')" style="font-size:13px; color:var(--muted);">Cancel booking</a>
  </div>
</div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('input[name="payment_method"]').forEach(r => {
  r.addEventListener('change', function() {
    if (this.value === 'mpesa') {
      document.getElementById('mpesaSection').style.opacity = '1';
      document.getElementById('bankSection').style.opacity = '0.6';
    } else {
      document.getElementById('mpesaSection').style.opacity = '0.6';
      document.getElementById('bankSection').style.opacity = '1';
    }
  });
});

document.getElementById('paymentForm').addEventListener('submit', function() {
  document.getElementById('payBtnText').style.display = 'none';
  document.getElementById('payBtnSpinner').style.display = 'inline';
  document.getElementById('payBtn').disabled = true;
});
</script>
@endpush
