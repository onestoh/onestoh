@extends('layouts.dashboard')

@section('title', 'Broker Dashboard')
@section('page-title', 'Broker Dashboard')

@section('content')
<h4 class="mb-4" style="color:var(--text);font-weight:700">Broker Dashboard</h4>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="p-3 rounded-3" style="background:var(--surface);border:1px solid var(--border)">
            <div style="color:var(--muted);font-size:.78rem;text-transform:uppercase;letter-spacing:.06em">Wallet</div>
            <div style="color:var(--amber);font-size:1.3rem;font-weight:700" class="mt-1">KES {{ number_format($stats['wallet_balance'], 0) }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="p-3 rounded-3" style="background:var(--surface);border:1px solid var(--border)">
            <div style="color:var(--muted);font-size:.78rem;text-transform:uppercase;letter-spacing:.06em">Total Earned</div>
            <div style="color:var(--green);font-size:1.3rem;font-weight:700" class="mt-1">KES {{ number_format($stats['total_earned'], 0) }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="p-3 rounded-3" style="background:var(--surface);border:1px solid var(--border)">
            <div style="color:var(--muted);font-size:.78rem;text-transform:uppercase;letter-spacing:.06em">Pending</div>
            <div style="color:var(--amber);font-size:1.3rem;font-weight:700" class="mt-1">KES {{ number_format($stats['pending_amount'], 0) }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="p-3 rounded-3" style="background:var(--surface);border:1px solid var(--border)">
            <div style="color:var(--muted);font-size:.78rem;text-transform:uppercase;letter-spacing:.06em">Total Deals</div>
            <div style="color:var(--text);font-size:1.3rem;font-weight:700" class="mt-1">{{ $stats['total_deals'] }}</div>
        </div>
    </div>
</div>

{{-- Referral Code --}}
<div class="p-4 rounded-3 mb-4" style="background:var(--surface);border:1px solid rgba(232,146,42,.3)">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div style="color:var(--amber);font-size:.78rem;text-transform:uppercase;letter-spacing:1px;font-weight:700">Your Referral Code</div>
            <div style="color:var(--text);font-size:1.5rem;font-weight:800;font-family:monospace;letter-spacing:2px">{{ $stats['referral_code'] }}</div>
            <div style="color:var(--muted);font-size:.82rem;margin-top:.25rem">Share this code to earn commissions on referred bookings</div>
        </div>
        <button onclick="navigator.clipboard.writeText('{{ $stats['referral_code'] }}')" class="btn btn-amber btn-sm px-4">
            <i class="fas fa-copy me-1"></i>Copy Code
        </button>
    </div>
</div>

{{-- Recent Commissions --}}
<div class="p-3 rounded-3" style="background:var(--surface);border:1px solid var(--border)">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 style="color:var(--text);font-weight:600;margin:0">Recent Commissions</h6>
        <a href="{{ route('broker.commissions') }}" style="font-size:.8rem;color:var(--amber)">View All</a>
    </div>

    @if($recentCommissions->isEmpty())
    <p style="color:var(--muted);font-size:.9rem">No commissions yet. Start referring clients to earn.</p>
    @else
    <div class="table-responsive">
        <table class="table table-sm" style="color:var(--text)">
            <thead>
                <tr style="border-color:var(--border);color:var(--muted);font-size:.75rem;text-transform:uppercase">
                    <th>Booking</th>
                    <th>Listing</th>
                    <th>Commission</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentCommissions as $c)
                <tr style="border-color:var(--border)">
                    <td style="font-size:.82rem;color:var(--muted);font-family:monospace">{{ $c->booking_ref }}</td>
                    <td style="font-size:.85rem">{{ Str::limit($c->listing_title, 30) }}</td>
                    <td style="color:var(--green);font-weight:600">KES {{ number_format($c->amount) }}</td>
                    <td>
                        @if($c->status === 'paid')
                            <span style="color:var(--green);font-size:.78rem"><i class="fas fa-check-circle me-1"></i>Paid</span>
                        @else
                            <span style="color:var(--amber);font-size:.78rem"><i class="fas fa-clock me-1"></i>Pending</span>
                        @endif
                    </td>
                    <td style="color:var(--muted);font-size:.78rem">{{ \Carbon\Carbon::parse($c->created_at)->format('d M Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
