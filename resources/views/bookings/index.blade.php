@extends('layouts.dashboard')

@section('title', 'My Bookings')
@section('page-title', 'My Bookings')

@push('styles')
<style>
.booking-card { background: var(--black); border: 1px solid var(--border); border-radius: 10px; padding: 1rem 1.25rem; margin-bottom: .75rem; }
.s-badge { padding: 3px 10px; border-radius: 20px; font-size: .72rem; font-weight: 600; }
.s-pending_payment { background: rgba(232,146,42,.15); color: var(--amber); border: 1px solid rgba(232,146,42,.3); }
.s-confirmed { background: rgba(99,179,237,.15); color: #63b3ed; border: 1px solid rgba(99,179,237,.3); }
.s-active { background: rgba(46,204,138,.15); color: var(--green); border: 1px solid rgba(46,204,138,.3); }
.s-completed { background: rgba(112,136,168,.15); color: var(--muted); border: 1px solid var(--border); }
.s-cancelled, .s-disputed { background: rgba(232,64,64,.15); color: var(--danger); border: 1px solid rgba(232,64,64,.3); }
</style>
@endpush

@section('content')
<h4 class="mb-4" style="color:var(--text);font-weight:700">My Bookings</h4>

@if($bookings->isEmpty())
<div class="text-center py-5" style="color:var(--muted)">
    <i class="fas fa-calendar-times fa-3x mb-3 d-block" style="opacity:.3"></i>
    <h5 style="color:var(--text)">No bookings yet</h5>
    <a href="{{ route('marketplace') }}" class="btn btn-amber mt-2">Browse Marketplace</a>
</div>
@else
@foreach($bookings as $booking)
<div class="booking-card">
    <div class="d-flex align-items-start gap-3">
        @if($booking->listing?->primaryPhoto)
            <img src="{{ Storage::url($booking->listing->primaryPhoto->file_path) }}"
                style="width:72px;height:56px;object-fit:cover;border-radius:7px;flex-shrink:0" alt="">
        @else
            <div style="width:72px;height:56px;background:var(--surface);border-radius:7px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="fas fa-car text-amber"></i>
            </div>
        @endif

        <div class="flex-grow-1 min-w-0">
            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                <span class="fw-semibold" style="color:var(--text);font-size:.9rem">{{ Str::limit($booking->listing?->title ?? 'Listing Unavailable', 45) }}</span>
                <span class="s-badge s-{{ $booking->status }}">{{ str_replace('_', ' ', ucfirst($booking->status)) }}</span>
            </div>
            <div style="color:var(--muted);font-size:.8rem">
                <i class="fas fa-hashtag me-1"></i>{{ $booking->booking_ref }}
                &nbsp;·&nbsp;
                <i class="fas fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($booking->start_datetime)->format('d M Y') }}
                → {{ \Carbon\Carbon::parse($booking->end_datetime)->format('d M Y') }}
                &nbsp;·&nbsp;
                <i class="fas fa-clock me-1"></i>{{ ucfirst($booking->duration_type) }}
            </div>
        </div>

        <div class="text-end flex-shrink-0">
            <div style="color:var(--amber);font-weight:700;font-size:1rem">KES {{ number_format($booking->total_amount) }}</div>
            <div style="color:var(--muted);font-size:.75rem">Total</div>
            <div class="mt-2">
                <a href="{{ route('bookings.show', $booking) }}" class="btn btn-sm" style="border:1px solid var(--border);color:var(--muted);font-size:.78rem">View Details</a>
            </div>
        </div>
    </div>
</div>
@endforeach

<div class="mt-3">{{ $bookings->links('pagination::bootstrap-5') }}</div>
@endif
@endsection
