@extends('layouts.dashboard')
@section('title', 'My Bookings — EstateYard')
@section('page-title', 'My Bookings')
@section('page-subtitle', 'All your short-stay reservations')

@section('content')

@if(session('success'))
<div class="alert alert-green" style="margin-bottom:20px;">{{ session('success') }}</div>
@endif

<!-- Tabs -->
<div style="display:flex; gap:0; border:1px solid var(--border); border-radius:10px; overflow:hidden; margin-bottom:28px;">
  @foreach(['upcoming' => 'Upcoming', 'past' => 'Past Stays', 'cancelled' => 'Cancelled'] as $key => $label)
  <div onclick="showTab('{{ $key }}')" id="tab-{{ $key }}" style="flex:1; padding:13px 16px; text-align:center; font-size:13px; font-weight:600; cursor:pointer; transition:background .2s; {{ $loop->first ? 'background:var(--gold-dim); color:var(--gold);' : 'background:var(--navy3); color:var(--muted);' }} {{ !$loop->last ? 'border-right:1px solid var(--border);' : '' }}">
    {{ $label }}
    @if($$key->count() > 0)
    <span style="display:inline-block; background:{{ $key==='upcoming' ? 'var(--gold)' : 'var(--surface)' }}; color:{{ $key==='upcoming' ? 'var(--navy)' : 'var(--muted)' }}; border-radius:10px; padding:1px 7px; font-size:11px; margin-left:6px;">{{ $$key->count() }}</span>
    @endif
  </div>
  @endforeach
</div>

@foreach(['upcoming' => $upcoming, 'past' => $past, 'cancelled' => $cancelled] as $tabKey => $tabBookings)
<div id="tabcontent-{{ $tabKey }}" style="{{ $tabKey !== 'upcoming' ? 'display:none;' : '' }}">
  @if($tabBookings->isEmpty())
  <div style="text-align:center; padding:64px 32px; color:var(--muted);">
    <div style="font-size:56px; margin-bottom:16px;">{{ $tabKey==='upcoming' ? '🗓️' : ($tabKey==='past' ? '🏨' : '❌') }}</div>
    <div style="font-size:16px; font-weight:600; color:var(--white); margin-bottom:8px;">No {{ strtolower($tabKey === 'upcoming' ? 'upcoming bookings' : ($tabKey === 'past' ? 'past stays' : 'cancelled bookings')) }}</div>
    @if($tabKey === 'upcoming')
    <p style="font-size:14px; margin-bottom:20px;">Start exploring short-stay properties on EstateYard</p>
    <a href="{{ url('/marketplace?type=airbnb') }}" class="btn btn-gold">🔍 Browse Short Stays</a>
    @endif
  </div>
  @else
  <div style="display:grid; gap:16px;">
    @foreach($tabBookings as $booking)
    <div style="background:var(--navy3); border:1px solid var(--border-dim); border-radius:var(--radius); padding:20px; display:flex; gap:16px; align-items:flex-start;">
      <!-- Property icon -->
      <div style="width:72px; height:72px; border-radius:10px; background:var(--surface); display:flex; align-items:center; justify-content:center; font-size:36px; flex-shrink:0;">🏠</div>
      <!-- Details -->
      <div style="flex:1; min-width:0;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:8px; margin-bottom:8px;">
          <div>
            <div style="font-size:15px; font-weight:600; color:var(--white);">{{ $booking->property->title ?? 'Property' }}</div>
            <div style="font-size:13px; color:var(--muted);">📍 {{ $booking->property->location ?? '' }}, {{ $booking->property->county ?? '' }}</div>
            @if($booking->room)
            <div style="font-size:12px; color:var(--gold); margin-top:2px;">{{ $booking->room->name }}</div>
            @endif
          </div>
          @php
            $statusColors = ['pending'=>'gold','confirmed'=>'green','paid'=>'green','checked_in'=>'blue','checked_out'=>'muted','cancelled'=>'red','refunded'=>'orange'];
            $sc = $statusColors[$booking->status] ?? 'muted';
          @endphp
          <span style="background:rgba(var(--{{ $sc }}-rgb,100,100,100),0.1); color:var(--{{ $sc }}); border:1px solid rgba(100,100,100,0.2); padding:4px 12px; border-radius:20px; font-size:11px; font-weight:600; white-space:nowrap;">{{ strtoupper($booking->status) }}</span>
        </div>
        <div style="display:flex; flex-wrap:wrap; gap:16px; margin-bottom:12px; font-size:13px; color:var(--muted);">
          <span>📅 {{ \Carbon\Carbon::parse($booking->check_in)->format('d M') }} → {{ \Carbon\Carbon::parse($booking->check_out)->format('d M Y') }}</span>
          <span>🌙 {{ $booking->nights }} nights</span>
          <span>👥 {{ $booking->guests_count }} guests</span>
          <span style="color:var(--gold); font-weight:600;">KES {{ number_format($booking->total_price) }}</span>
        </div>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
          <a href="{{ route('booking.confirmation', $booking->id) }}" class="btn btn-sm btn-outline">📄 View Details</a>
          @if($booking->isActive())
          <form method="POST" action="{{ route('booking.cancel', $booking->id) }}" onsubmit="return confirm('Cancel this booking?')">
            @csrf
            <button type="submit" class="btn btn-sm" style="background:rgba(224,82,82,0.1); color:var(--red); border:1px solid rgba(224,82,82,0.3);">✕ Cancel</button>
          </form>
          @endif
          @if($booking->status === 'checked_out' && !$booking->review)
          <button onclick="document.getElementById('review-{{ $booking->id }}').style.display='block'" class="btn btn-sm btn-gold">⭐ Leave Review</button>
          @endif
        </div>

        <!-- Review form (hidden) -->
        @if($booking->status === 'checked_out' && !$booking->review)
        <div id="review-{{ $booking->id }}" style="display:none; margin-top:16px; padding:16px; background:var(--surface); border-radius:10px;">
          <form method="POST" action="{{ route('booking.review', $booking->id) }}">
            @csrf
            <div style="font-size:14px; font-weight:600; color:var(--white); margin-bottom:12px;">Rate your stay</div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px;">
              @foreach(['rating' => 'Overall', 'cleanliness' => 'Cleanliness', 'communication' => 'Communication', 'value' => 'Value'] as $field => $label)
              <div>
                <label class="form-label" style="font-size:12px;">{{ $label }}</label>
                <select name="{{ $field }}" class="form-control" style="padding:8px;" {{ $field==='rating' ? 'required' : '' }}>
                  <option value="">—</option>
                  @for($i=5;$i>=1;$i--)<option value="{{ $i }}">{{ $i }} ★</option>@endfor
                </select>
              </div>
              @endforeach
            </div>
            <div class="form-group">
              <textarea name="comment" class="form-control" rows="2" placeholder="Share your experience..."></textarea>
            </div>
            <button type="submit" class="btn btn-gold btn-sm">Submit Review</button>
            <button type="button" onclick="document.getElementById('review-{{ $booking->id }}').style.display='none'" class="btn btn-outline btn-sm" style="margin-left:8px;">Cancel</button>
          </form>
        </div>
        @endif
      </div>
    </div>
    @endforeach
  </div>
  @endif
</div>
@endforeach

@endsection

@push('scripts')
<script>
function showTab(name) {
  ['upcoming','past','cancelled'].forEach(t => {
    document.getElementById('tabcontent-' + t).style.display = t === name ? 'block' : 'none';
    const tab = document.getElementById('tab-' + t);
    if (tab) {
      tab.style.background = t === name ? 'var(--gold-dim)' : 'var(--navy3)';
      tab.style.color = t === name ? 'var(--gold)' : 'var(--muted)';
    }
  });
}
</script>
@endpush
