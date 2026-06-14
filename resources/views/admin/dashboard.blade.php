@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@push('styles')
<style>
.stat-tile { background: var(--black); border: 1px solid var(--border); border-radius: 12px; padding: 1.25rem; }
.stat-tile .value { font-size: 1.8rem; font-weight: 800; line-height: 1; margin: .4rem 0 .2rem; }
.stat-tile .label { color: var(--muted); font-size: .72rem; text-transform: uppercase; letter-spacing: .08em; }
.stat-tile .sub { color: var(--muted); font-size: .78rem; }
.table-dark-custom { color: var(--text); }
.table-dark-custom td, .table-dark-custom th { border-color: var(--border); vertical-align: middle; }
.s-badge { padding: 2px 8px; border-radius: 12px; font-size: .72rem; font-weight: 600; }
.s-pending_payment { background: rgba(232,146,42,.15); color: var(--amber); }
.s-confirmed { background: rgba(99,179,237,.15); color: #63b3ed; }
.s-active { background: rgba(46,204,138,.15); color: var(--green); }
.s-completed { background: rgba(112,136,168,.2); color: var(--muted); }
.s-cancelled, .s-disputed { background: rgba(232,64,64,.15); color: var(--danger); }
</style>
@endpush

@push('scripts')
<script>
// Populate topbar stats
document.getElementById('statUsers')?.textContent = '{{ $stats["total_users"] }}';
document.getElementById('statListings')?.textContent = '{{ $stats["total_listings"] }}';
document.getElementById('statBookings')?.textContent = '{{ $stats["total_bookings"] }}';
document.getElementById('statRevenue')?.textContent = 'KES {{ number_format($stats["revenue_total"], 0) }}';
document.getElementById('statDisputes')?.textContent = '{{ $stats["open_disputes"] }}';
</script>
@endpush

@section('content')

{{-- Stat tiles --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-tile">
            <div class="label"><i class="fas fa-users me-1"></i>Total Users</div>
            <div class="value" style="color:var(--amber)">{{ number_format($stats['total_users']) }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-tile">
            <div class="label"><i class="fas fa-car me-1"></i>Listings</div>
            <div class="value" style="color:var(--text)">{{ number_format($stats['total_listings']) }}</div>
            <div class="sub">{{ $stats['active_listings'] }} active</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-tile">
            <div class="label"><i class="fas fa-calendar-check me-1"></i>Bookings</div>
            <div class="value" style="color:var(--text)">{{ number_format($stats['total_bookings']) }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-tile">
            <div class="label"><i class="fas fa-coins me-1"></i>Revenue</div>
            <div class="value" style="color:var(--green)">{{ number_format($stats['revenue_total'] / 1000, 1) }}K</div>
            <div class="sub">KES total</div>
        </div>
    </div>
</div>

{{-- Alert tiles --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <a href="{{ route('admin.kyc.index') }}" class="stat-tile d-block text-decoration-none">
            <div class="label"><i class="fas fa-id-card me-1"></i>Pending KYC</div>
            <div class="value" style="color:var(--amber)">{{ $stats['pending_kyc'] }}</div>
            <div class="sub">Users awaiting review</div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('admin.disputes.index') }}" class="stat-tile d-block text-decoration-none">
            <div class="label"><i class="fas fa-exclamation-triangle me-1"></i>Open Disputes</div>
            <div class="value" style="color:var(--danger)">{{ $stats['open_disputes'] }}</div>
            <div class="sub">Require resolution</div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('admin.payouts.index') }}" class="stat-tile d-block text-decoration-none">
            <div class="label"><i class="fas fa-money-bill-wave me-1"></i>Pending Payouts</div>
            <div class="value" style="color:var(--amber)">{{ $stats['pending_payouts'] }}</div>
            <div class="sub">Awaiting approval</div>
        </a>
    </div>
</div>

<div class="row g-4">
    {{-- Recent Bookings --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-semibold" style="font-size:.9rem"><i class="fas fa-calendar-check me-2 text-amber"></i>Recent Bookings</span>
                <a href="{{ route('admin.bookings.index') }}" style="font-size:.8rem;color:var(--amber)">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-dark-custom mb-0" style="--bs-table-bg:transparent">
                        <thead style="font-size:.72rem;color:var(--muted);text-transform:uppercase">
                            <tr><th class="px-3 py-2">Ref</th><th>Client</th><th>Listing</th><th>Amount</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            @foreach($recentBookings as $b)
                            <tr>
                                <td class="px-3 py-2" style="font-family:monospace;color:var(--amber);font-size:.8rem">
                                    <a href="{{ route('admin.bookings.show', $b) }}" style="color:var(--amber)">{{ $b->booking_ref }}</a>
                                </td>
                                <td style="font-size:.85rem">{{ $b->client?->name }}</td>
                                <td style="font-size:.85rem">{{ Str::limit($b->listing?->title ?? '—', 28) }}</td>
                                <td style="font-size:.85rem">KES {{ number_format($b->total_amount) }}</td>
                                <td><span class="s-badge s-{{ $b->status }}">{{ str_replace('_',' ',ucfirst($b->status)) }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Users --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-semibold" style="font-size:.9rem"><i class="fas fa-users me-2 text-amber"></i>New Users</span>
                <a href="{{ route('admin.users.index') }}" style="font-size:.8rem;color:var(--amber)">View All</a>
            </div>
            <div class="card-body p-0">
                @foreach($recentUsers as $u)
                <div class="d-flex align-items-center gap-3 px-3 py-2" style="border-bottom:1px solid var(--border)">
                    <div style="width:32px;height:32px;border-radius:50%;background:rgba(232,146,42,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <i class="fas fa-user text-amber" style="font-size:.75rem"></i>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div style="color:var(--text);font-size:.85rem;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $u->name }}</div>
                        <div style="color:var(--muted);font-size:.72rem">{{ ucwords(str_replace('_',' ',$u->role)) }}</div>
                    </div>
                    @if($u->status === 'verified')
                        <i class="fas fa-check-circle text-success" style="font-size:.8rem"></i>
                    @elseif($u->status === 'pending')
                        <i class="fas fa-clock text-amber" style="font-size:.8rem"></i>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
