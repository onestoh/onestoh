@extends('layouts.dashboard')

@section('title', 'Booking ' . $booking->booking_ref)
@section('page-title', 'Booking Details')

@push('styles')
<style>
.info-block { background: var(--black); border: 1px solid var(--border); border-radius: 10px; padding: 1.25rem; margin-bottom: 1rem; }
.info-row { display: flex; justify-content: space-between; padding: .45rem 0; border-bottom: 1px solid var(--border); font-size: .875rem; }
.info-row:last-child { border: none; }
.info-label { color: var(--muted); }
.info-value { color: var(--text); font-weight: 500; text-align: right; }
.s-badge { padding: 4px 12px; border-radius: 20px; font-size: .8rem; font-weight: 600; }
.s-pending_payment { background: rgba(232,146,42,.15); color: var(--amber); border: 1px solid rgba(232,146,42,.3); }
.s-confirmed { background: rgba(99,179,237,.15); color: #63b3ed; border: 1px solid rgba(99,179,237,.3); }
.s-active { background: rgba(46,204,138,.15); color: var(--green); border: 1px solid rgba(46,204,138,.3); }
.s-completed { background: rgba(112,136,168,.15); color: var(--muted); border: 1px solid var(--border); }
.s-cancelled, .s-disputed { background: rgba(232,64,64,.15); color: var(--danger); border: 1px solid rgba(232,64,64,.3); }
</style>
@endpush

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        {{-- Status header --}}
        <div class="info-block">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <div style="color:var(--muted);font-size:.78rem;margin-bottom:.25rem">Booking Reference</div>
                    <div style="color:var(--amber);font-size:1.1rem;font-weight:700;font-family:monospace">{{ $booking->booking_ref }}</div>
                </div>
                <span class="s-badge s-{{ $booking->status }}">{{ str_replace('_', ' ', ucfirst($booking->status)) }}</span>
            </div>

            @if($booking->status === 'pending_payment' && $booking->slot_hold_expires_at)
            <div class="mt-3 p-2 rounded-2" style="background:rgba(232,146,42,.08);border:1px solid rgba(232,146,42,.2);font-size:.82rem;color:var(--amber)">
                <i class="fas fa-exclamation-circle me-1"></i>
                Slot held until {{ \Carbon\Carbon::parse($booking->slot_hold_expires_at)->format('H:i, d M Y') }}.
                Complete payment before this time.
            </div>
            @endif
        </div>

        {{-- Listing info --}}
        <div class="info-block">
            <div class="d-flex gap-3 align-items-start">
                @if($booking->listing?->photos->count())
                <img src="{{ Storage::url($booking->listing->photos->first()->file_path) }}"
                    style="width:100px;height:76px;object-fit:cover;border-radius:8px;flex-shrink:0" alt="">
                @endif
                <div>
                    <div class="fw-semibold mb-1" style="color:var(--text)">{{ $booking->listing?->title }}</div>
                    <div style="color:var(--muted);font-size:.82rem">
                        <i class="fas fa-th me-1"></i>{{ $booking->listing?->category?->name }}
                        &nbsp;·&nbsp;
                        <i class="fas fa-map-marker-alt me-1"></i>{{ $booking->listing?->county }}
                    </div>
                    <div style="color:var(--muted);font-size:.82rem;margin-top:.25rem">
                        Owner: {{ $booking->listing?->user?->name }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Booking details --}}
        <div class="info-block">
            <h6 style="color:var(--amber);font-size:.75rem;text-transform:uppercase;letter-spacing:1px;margin-bottom:.75rem">Booking Details</h6>
            <div class="info-row"><span class="info-label">Duration Type</span><span class="info-value">{{ ucfirst($booking->duration_type) }}</span></div>
            <div class="info-row"><span class="info-label">Start</span><span class="info-value">{{ \Carbon\Carbon::parse($booking->start_datetime)->format('D, d M Y · H:i') }}</span></div>
            <div class="info-row"><span class="info-label">End</span><span class="info-value">{{ \Carbon\Carbon::parse($booking->end_datetime)->format('D, d M Y · H:i') }}</span></div>
            @if($booking->notes)
            <div class="info-row"><span class="info-label">Notes</span><span class="info-value" style="max-width:60%">{{ $booking->notes }}</span></div>
            @endif
        </div>

        {{-- Cost breakdown --}}
        <div class="info-block">
            <h6 style="color:var(--amber);font-size:.75rem;text-transform:uppercase;letter-spacing:1px;margin-bottom:.75rem">Payment Breakdown</h6>
            <div class="info-row"><span class="info-label">Base Amount</span><span class="info-value">KES {{ number_format($booking->base_amount) }}</span></div>
            <div class="info-row"><span class="info-label">Platform Fee</span><span class="info-value">KES {{ number_format($booking->platform_fee) }}</span></div>
            @if($booking->security_deposit)
            <div class="info-row"><span class="info-label">Security Deposit</span><span class="info-value">KES {{ number_format($booking->security_deposit) }}</span></div>
            @endif
            <div class="info-row" style="font-size:1rem">
                <span style="color:var(--text);font-weight:700">Total</span>
                <span style="color:var(--amber);font-weight:800">KES {{ number_format($booking->total_amount) }}</span>
            </div>
        </div>
    </div>

    {{-- Actions sidebar --}}
    <div class="col-lg-4">
        <div class="info-block">
            <h6 style="color:var(--amber);font-size:.75rem;text-transform:uppercase;letter-spacing:1px;margin-bottom:1rem">Actions</h6>

            @if($booking->status === 'pending_payment' && $booking->client_id === auth()->id())
            <a href="{{ route('bookings.pay', $booking) }}" class="btn btn-amber w-100 mb-2">
                <i class="fas fa-mobile-alt me-2"></i>Pay via M-Pesa
            </a>
            @endif

            @if(in_array($booking->status, ['pending_payment', 'confirmed']) && $booking->client_id === auth()->id())
            <form method="POST" action="{{ route('bookings.cancel', $booking) }}" onsubmit="return confirm('Cancel this booking?')">
                @csrf
                <button type="submit" class="btn w-100 mb-2" style="border:1px solid rgba(232,64,64,.3);color:var(--danger)">
                    <i class="fas fa-times me-2"></i>Cancel Booking
                </button>
            </form>
            @endif

            @if($booking->status === 'completed' && $booking->client_id === auth()->id() && !Review::where('booking_id',$booking->id)->where('reviewer_id',auth()->id())->exists())
            <a href="#reviewForm" class="btn w-100 mb-2" style="border:1px solid rgba(232,146,42,.3);color:var(--amber)">
                <i class="fas fa-star me-2"></i>Leave Review
            </a>
            @endif

            <a href="{{ route('bookings.index') }}" class="btn w-100" style="border:1px solid var(--border);color:var(--muted);font-size:.85rem">
                <i class="fas fa-arrow-left me-2"></i>All Bookings
            </a>
        </div>

        @if($booking->listing?->user_id === auth()->id())
        <div class="info-block">
            <h6 style="color:var(--amber);font-size:.75rem;text-transform:uppercase;letter-spacing:1px;margin-bottom:1rem">Owner Actions</h6>
            @if($booking->status === 'confirmed')
            <form method="POST" action="{{ route('bookings.start', $booking) }}" class="mb-2">
                @csrf
                <button type="submit" class="btn btn-amber w-100"><i class="fas fa-play me-2"></i>Mark as Started</button>
            </form>
            @endif
            @if($booking->status === 'active')
            <form method="POST" action="{{ route('bookings.complete', $booking) }}">
                @csrf
                <button type="submit" class="btn w-100" style="background:var(--green);color:#000;font-weight:600"><i class="fas fa-check me-2"></i>Mark Complete</button>
            </form>
            @endif
        </div>
        @endif
    </div>
</div>
@if($booking->status === 'completed' && $booking->client_id === auth()->id())
@php $alreadyReviewed = \App\Models\Review::where('booking_id',$booking->id)->where('reviewer_id',auth()->id())->exists(); @endphp
@if(!$alreadyReviewed)
<div class="info-block mt-4" id="reviewForm">
    <h6 style="color:var(--amber);font-size:.75rem;text-transform:uppercase;letter-spacing:1px;margin-bottom:1rem"><i class="fas fa-star me-2"></i>Leave a Review</h6>
    <form method="POST" action="{{ route('reviews.store', $booking) }}" x-data="{ rating: 0, hover: 0 }">
        @csrf
        <div class="mb-3">
            <label style="color:var(--muted);font-size:.82rem;display:block;margin-bottom:.4rem">Your Rating</label>
            <div class="d-flex gap-1" style="font-size:1.6rem">
                @for($i = 1; $i <= 5; $i++)
                <span style="cursor:pointer;transition:color .15s"
                    :style="(hover >= {{ $i }} || rating >= {{ $i }}) ? 'color:var(--amber)' : 'color:var(--border)'"
                    @mouseenter="hover = {{ $i }}" @mouseleave="hover = 0"
                    @click="rating = {{ $i }}">
                    <i class="fas fa-star"></i>
                </span>
                @endfor
            </div>
            <input type="hidden" name="rating" :value="rating" required>
            <div x-show="rating === 0" style="color:var(--danger);font-size:.75rem;margin-top:.25rem">Please select a rating.</div>
        </div>
        <div class="mb-3">
            <label style="color:var(--muted);font-size:.82rem;display:block;margin-bottom:.4rem">Comment</label>
            <textarea name="comment" rows="3" class="form-control" style="background:var(--black);border:1px solid var(--border);color:var(--text);resize:none"
                placeholder="Share your experience with this equipment/service..." required maxlength="1000"></textarea>
        </div>
        <button type="submit" class="btn btn-amber btn-sm" :disabled="rating === 0">
            <i class="fas fa-paper-plane me-1"></i>Submit Review
        </button>
    </form>
</div>
@else
<div class="info-block mt-4" style="border-color:rgba(46,204,138,.3)">
    <p style="color:var(--green);margin:0;font-size:.9rem"><i class="fas fa-check-circle me-2"></i>You have already reviewed this booking.</p>
</div>
@endif
@endif
@endsection
