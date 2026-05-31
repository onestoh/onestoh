@extends('layouts.app')
@section('title', 'Booking Confirmed — EstateYard')

@section('content')
<div style="padding-top:90px; background:var(--navy); min-height:100vh;">
<div class="container" style="padding:32px; max-width:640px; margin:0 auto;">

  <!-- Success animation -->
  <div style="text-align:center; margin-bottom:40px;">
    <div style="width:90px; height:90px; border-radius:50%; background:rgba(46,204,138,0.1); border:2px solid var(--green); display:flex; align-items:center; justify-content:center; margin:0 auto 20px; animation:checkPop .5s ease-out;">
      <span style="font-size:44px; color:var(--green);">✓</span>
    </div>
    <h1 style="font-family:var(--font-serif); font-size:36px; color:var(--white); margin-bottom:10px;">Booking Confirmed!</h1>
    <p style="color:var(--muted); font-size:15px;">Your booking has been confirmed and the host has been notified.</p>
    <div style="display:inline-block; background:var(--gold-dim); border:1px solid var(--gold); border-radius:8px; padding:8px 20px; margin-top:12px; font-family:var(--font-mono); font-size:14px; color:var(--gold);">
      {{ $booking->getBookingRef() }}
    </div>
  </div>

  <!-- Booking details -->
  <div style="background:var(--navy3); border:1px solid var(--border); border-radius:var(--radius); padding:24px; margin-bottom:24px;">
    <div style="font-size:13px; font-weight:600; color:var(--muted); text-transform:uppercase; letter-spacing:1px; margin-bottom:16px;">Booking Details</div>

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

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px;">
      <div style="background:var(--surface); border-radius:8px; padding:12px;">
        <div style="font-size:11px; color:var(--muted); text-transform:uppercase; letter-spacing:1px; margin-bottom:6px;">Check-in</div>
        <div style="font-size:15px; color:var(--white); font-weight:600;">{{ \Carbon\Carbon::parse($booking->check_in)->format('D, d M Y') }}</div>
        <div style="font-size:12px; color:var(--muted);">After 2:00 PM</div>
      </div>
      <div style="background:var(--surface); border-radius:8px; padding:12px;">
        <div style="font-size:11px; color:var(--muted); text-transform:uppercase; letter-spacing:1px; margin-bottom:6px;">Check-out</div>
        <div style="font-size:15px; color:var(--white); font-weight:600;">{{ \Carbon\Carbon::parse($booking->check_out)->format('D, d M Y') }}</div>
        <div style="font-size:12px; color:var(--muted);">Before 11:00 AM</div>
      </div>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px; margin-bottom:16px; padding-bottom:16px; border-bottom:1px solid var(--border-dim);">
      <div style="text-align:center; background:var(--surface); border-radius:8px; padding:12px;">
        <div style="font-size:11px; color:var(--muted); margin-bottom:4px;">NIGHTS</div>
        <div style="font-size:20px; font-weight:700; color:var(--white); font-family:var(--font-serif);">{{ $booking->nights }}</div>
      </div>
      <div style="text-align:center; background:var(--surface); border-radius:8px; padding:12px;">
        <div style="font-size:11px; color:var(--muted); margin-bottom:4px;">GUESTS</div>
        <div style="font-size:20px; font-weight:700; color:var(--white); font-family:var(--font-serif);">{{ $booking->guests_count }}</div>
      </div>
      <div style="text-align:center; background:var(--surface); border-radius:8px; padding:12px;">
        <div style="font-size:11px; color:var(--muted); margin-bottom:4px;">TOTAL PAID</div>
        <div style="font-size:16px; font-weight:700; color:var(--gold); font-family:var(--font-serif);">KES {{ number_format($booking->total_price) }}</div>
      </div>
    </div>

    <div style="display:flex; justify-content:space-between; align-items:center;">
      <div>
        <div style="font-size:12px; color:var(--muted);">Payment via</div>
        <div style="font-size:14px; color:var(--white); font-weight:600;">{{ strtoupper($booking->payment_method ?? 'MPESA') }}</div>
      </div>
      <span style="background:rgba(46,204,138,0.1); color:var(--green); border:1px solid rgba(46,204,138,0.3); padding:4px 12px; border-radius:20px; font-size:12px; font-weight:600;">CONFIRMED</span>
    </div>
  </div>

  <!-- What happens next -->
  <div style="background:var(--navy3); border:1px solid var(--border-dim); border-radius:var(--radius); padding:24px; margin-bottom:24px;">
    <div style="font-size:15px; font-weight:600; color:var(--white); margin-bottom:16px;">What happens next</div>
    @foreach([
      ['✉️', 'Confirmation email sent', 'A booking confirmation has been sent to your email address.'],
      ['🔔', 'Host notified', 'The property owner has been notified of your upcoming stay.'],
      ['🔑', 'Check-in instructions', 'You\'ll receive detailed check-in instructions 24 hours before arrival.'],
    ] as $step)
    <div style="display:flex; gap:14px; margin-bottom:14px; {{ !$loop->last ? 'padding-bottom:14px; border-bottom:1px solid var(--border-dim);' : '' }}">
      <div style="width:40px; height:40px; border-radius:50%; background:var(--gold-dim); display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0;">{{ $step[0] }}</div>
      <div>
        <div style="font-size:14px; font-weight:600; color:var(--white);">{{ $step[1] }}</div>
        <div style="font-size:13px; color:var(--muted);">{{ $step[2] }}</div>
      </div>
    </div>
    @endforeach
  </div>

  <!-- Actions -->
  <div style="display:flex; gap:12px; margin-bottom:20px;">
    <a href="{{ route('dashboard.bookings') }}" class="btn btn-gold" style="flex:1; justify-content:center; padding:14px;">📋 View My Bookings</a>
    <a href="{{ url('/marketplace') }}" class="btn btn-outline" style="flex:1; justify-content:center; padding:14px;">🏘️ Back to Marketplace</a>
  </div>

  <!-- Add to calendar (ICS link) -->
  <div style="text-align:center; padding:16px;">
    <a href="#" onclick="downloadICS(); return false;" style="font-size:13px; color:var(--muted);">📅 Add to Calendar</a>
  </div>

</div>
</div>
@endsection

@push('styles')
<style>
@keyframes checkPop {
  0% { transform: scale(0.5); opacity: 0; }
  70% { transform: scale(1.1); opacity: 1; }
  100% { transform: scale(1); opacity: 1; }
}
</style>
@endpush

@push('scripts')
<script>
function downloadICS() {
  const checkIn = '{{ \Carbon\Carbon::parse($booking->check_in)->format('Ymd') }}';
  const checkOut = '{{ \Carbon\Carbon::parse($booking->check_out)->format('Ymd') }}';
  const title = encodeURIComponent('EstateYard Stay: {{ addslashes($booking->property->title ?? "Property") }}');
  const ics = `BEGIN:VCALENDAR\r\nVERSION:2.0\r\nBEGIN:VEVENT\r\nDTSTART:${checkIn}\r\nDTEND:${checkOut}\r\nSUMMARY:${decodeURIComponent(title)}\r\nDESCRIPTION:Booking Ref: {{ $booking->getBookingRef() }}\r\nEND:VEVENT\r\nEND:VCALENDAR`;
  const blob = new Blob([ics], {type:'text/calendar'});
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = 'estateyard-booking.ics';
  a.click();
  URL.revokeObjectURL(url);
}
</script>
@endpush
