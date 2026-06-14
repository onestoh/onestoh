@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
<div class="mb-4">
    <h4 style="color:var(--text);font-weight:700">Welcome back, {{ $user->name }}</h4>
    <p style="color:var(--muted)">{{ now()->format('l, d F Y') }}</p>
</div>

{{-- Stats Row --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="p-3 rounded-3 h-100" style="background:var(--surface);border:1px solid var(--border)">
            <div style="color:var(--muted);font-size:.78rem;text-transform:uppercase;letter-spacing:.06em">Wallet Balance</div>
            <div style="color:var(--amber);font-size:1.4rem;font-weight:700" class="mt-1">
                KES {{ number_format($user->wallet?->balance ?? 0, 2) }}
            </div>
            <a href="{{ route('wallet.index') }}" style="color:var(--muted);font-size:.8rem">View wallet &rarr;</a>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="p-3 rounded-3 h-100" style="background:var(--surface);border:1px solid var(--border)">
            <div style="color:var(--muted);font-size:.78rem;text-transform:uppercase;letter-spacing:.06em">Active Bookings</div>
            <div style="color:var(--text);font-size:1.4rem;font-weight:700" class="mt-1">{{ $stats['active_bookings'] }}</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="p-3 rounded-3 h-100" style="background:var(--surface);border:1px solid var(--border)">
            <div style="color:var(--muted);font-size:.78rem;text-transform:uppercase;letter-spacing:.06em">KYC Status</div>
            <div class="mt-1">
                @php $kycStatus = $stats['kyc_status'] @endphp
                @if($kycStatus === 'verified')
                <span class="badge" style="background:rgba(46,204,138,.15);color:var(--green)"><i class="fas fa-check-circle me-1"></i>Verified</span>
                @elseif($kycStatus === 'pending')
                <span class="badge" style="background:rgba(232,146,42,.15);color:var(--amber)"><i class="fas fa-clock me-1"></i>Pending</span>
                @elseif($kycStatus === 'rejected')
                <span class="badge" style="background:rgba(220,53,69,.15);color:#f87171"><i class="fas fa-times-circle me-1"></i>Rejected</span>
                @else
                <span class="badge" style="background:rgba(112,136,168,.15);color:var(--muted)">Not Submitted</span>
                @endif
            </div>
            <a href="{{ route('kyc.index') }}" style="color:var(--muted);font-size:.8rem">Manage KYC &rarr;</a>
        </div>
    </div>

    @if(in_array($user->role, ['yard_owner', 'individual_owner']))
    <div class="col-6 col-lg-3">
        <div class="p-3 rounded-3 h-100" style="background:var(--surface);border:1px solid var(--border)">
            <div style="color:var(--muted);font-size:.78rem;text-transform:uppercase;letter-spacing:.06em">Total Listings</div>
            <div style="color:var(--text);font-size:1.4rem;font-weight:700" class="mt-1">{{ $stats['total_listings'] }}</div>
        </div>
    </div>
    @endif

    @if($user->role === 'broker')
    <div class="col-6 col-lg-3">
        <div class="p-3 rounded-3 h-100" style="background:var(--surface);border:1px solid var(--border)">
            <div style="color:var(--muted);font-size:.78rem;text-transform:uppercase;letter-spacing:.06em">Commissions Earned</div>
            <div style="color:var(--green);font-size:1.4rem;font-weight:700" class="mt-1">KES {{ number_format($stats['total_commissions'] ?? 0, 2) }}</div>
        </div>
    </div>
    @endif

    @if($user->role === 'client')
    <div class="col-6 col-lg-3">
        <div class="p-3 rounded-3 h-100" style="background:var(--surface);border:1px solid var(--border)">
            <div style="color:var(--muted);font-size:.78rem;text-transform:uppercase;letter-spacing:.06em">Total Bookings</div>
            <div style="color:var(--text);font-size:1.4rem;font-weight:700" class="mt-1">{{ $stats['total_bookings'] }}</div>
        </div>
    </div>
    @endif
</div>

@if(in_array($user->role, ['yard_owner', 'individual_owner']))
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="p-3 rounded-3" style="background:var(--surface);border:1px solid var(--border)">
            <div style="color:var(--muted);font-size:.78rem;text-transform:uppercase;letter-spacing:.06em">Revenue This Month</div>
            <div style="color:var(--green);font-size:1.4rem;font-weight:700" class="mt-1">KES {{ number_format($stats['revenue_this_month'] ?? 0, 2) }}</div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="p-3 rounded-3" style="background:var(--surface);border:1px solid var(--border)">
            <div style="color:var(--muted);font-size:.78rem;text-transform:uppercase;letter-spacing:.06em">Pending Requests</div>
            <div style="color:var(--amber);font-size:1.4rem;font-weight:700" class="mt-1">{{ $stats['pending_requests'] ?? 0 }}</div>
        </div>
    </div>
</div>
@endif

@if($user->role === 'broker')
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="p-3 rounded-3" style="background:var(--surface);border:1px solid var(--border)">
            <div style="color:var(--muted);font-size:.78rem;text-transform:uppercase;letter-spacing:.06em">Active Deals</div>
            <div style="color:var(--amber);font-size:1.4rem;font-weight:700" class="mt-1">{{ $stats['active_deals'] ?? 0 }}</div>
        </div>
    </div>
</div>
@endif

@if($user->role === 'client' && isset($stats['next_booking']) && $stats['next_booking'])
<div class="mb-4 p-3 rounded-3" style="background:rgba(232,146,42,.08);border:1px solid rgba(232,146,42,.3)">
    <div style="color:var(--amber);font-size:.8rem;font-weight:600;text-transform:uppercase">Upcoming Booking</div>
    <div class="d-flex justify-content-between align-items-center mt-1">
        <div>
            <div style="color:var(--text);font-weight:600">{{ $stats['next_booking']->listing->title ?? 'N/A' }}</div>
            <div style="color:var(--muted);font-size:.875rem">{{ \Carbon\Carbon::parse($stats['next_booking']->start_datetime)->format('D, d M Y • H:i') }}</div>
        </div>
        <span class="badge" style="background:rgba(46,204,138,.15);color:var(--green)">{{ ucfirst($stats['next_booking']->status) }}</span>
    </div>
</div>
@endif

{{-- Recent Bookings --}}
<div class="p-3 rounded-3" style="background:var(--surface);border:1px solid var(--border)">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 style="color:var(--text);font-weight:600" class="mb-0">Recent Bookings</h6>
    </div>
    @if($recentBookings->count())
    <div class="table-responsive">
        <table class="table table-sm" style="color:var(--text)">
            <thead>
                <tr style="border-color:var(--border);color:var(--muted);font-size:.8rem">
                    <th>Ref</th>
                    <th>Listing</th>
                    <th>Dates</th>
                    <th>Status</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentBookings as $booking)
                <tr style="border-color:var(--border)">
                    <td style="font-size:.85rem;color:var(--muted)">{{ $booking->booking_ref }}</td>
                    <td style="font-size:.875rem">{{ Str::limit($booking->listing->title ?? '—', 30) }}</td>
                    <td style="font-size:.8rem;color:var(--muted)">
                        {{ \Carbon\Carbon::parse($booking->start_datetime)->format('d M') }}
                        &rarr; {{ \Carbon\Carbon::parse($booking->end_datetime)->format('d M Y') }}
                    </td>
                    <td>
                        @php
                        $colors = ['pending'=>'var(--amber)','confirmed'=>'var(--green)','completed'=>'var(--green)','cancelled'=>'#f87171','ongoing'=>'#60a5fa'];
                        $c = $colors[$booking->status] ?? 'var(--muted)';
                        @endphp
                        <span class="badge" style="background:rgba(255,255,255,.05);color:{{ $c }};border:1px solid {{ $c }}">{{ ucfirst($booking->status) }}</span>
                    </td>
                    <td class="text-end" style="font-size:.875rem;font-weight:600">KES {{ number_format($booking->total_amount) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <p style="color:var(--muted);font-size:.9rem" class="mb-0">No bookings yet. <a href="{{ route('marketplace') }}" style="color:var(--amber)">Browse listings &rarr;</a></p>
    @endif
</div>
@endsection
