@extends('layouts.admin')
@section('title', 'Booking — ' . $booking->booking_ref)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.bookings.index') }}" class="text-muted text-decoration-none">
        <i class="fas fa-arrow-left me-1"></i>Back to Bookings
    </a>
</div>

{{-- Header --}}
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-1 fw-bold"><code style="color:var(--amber);font-size:1.2rem;">{{ $booking->booking_ref }}</code></h4>
        @php
            $st = $booking->status;
            $styleMap = [
                'pending_payment' => 'background:rgba(232,146,42,0.15);color:var(--amber);border:1px solid rgba(232,146,42,0.3);',
                'confirmed'       => 'background:rgba(99,179,237,0.15);color:#63b3ed;border:1px solid rgba(99,179,237,0.3);',
                'active'          => 'background:rgba(46,204,138,0.15);color:var(--green);border:1px solid rgba(46,204,138,0.3);',
                'completed'       => 'background:rgba(112,136,168,0.15);color:var(--muted);border:1px solid rgba(112,136,168,0.3);',
                'cancelled'       => 'background:rgba(232,64,64,0.15);color:var(--danger);border:1px solid rgba(232,64,64,0.3);',
                'disputed'        => 'background:rgba(232,64,64,0.15);color:var(--danger);border:1px solid rgba(232,64,64,0.3);',
            ];
            $style = $styleMap[$st] ?? 'background:rgba(112,136,168,0.15);color:var(--muted);border:1px solid rgba(112,136,168,0.3);';
        @endphp
        <span class="badge rounded-pill px-3" style="{{ $style }}">{{ ucfirst(str_replace('_',' ',$st)) }}</span>
    </div>

    @if($booking->status === 'completed' && !$booking->escrow_released_at)
    <form method="POST" action="{{ route('admin.bookings.escrow', $booking) }}"
        onsubmit="return confirm('Release escrow to owner? This cannot be undone.')">
        @csrf
        <button class="btn btn-amber">
            <i class="fas fa-unlock me-2"></i>Release Escrow to Owner
        </button>
    </form>
    @elseif($booking->escrow_released_at)
    <span class="badge badge-active rounded-pill px-3">
        <i class="fas fa-check me-1"></i>Escrow Released {{ $booking->escrow_released_at->format('d M Y') }}
    </span>
    @endif
</div>

<div class="row g-4 mb-4">
    {{-- Booking Details --}}
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="fas fa-info-circle me-2 text-amber"></i>Booking Details</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm mb-0" style="--bs-table-bg:transparent;--bs-table-border-color:var(--border);color:var(--text);">
                    <tr><td class="text-muted pe-3" style="width:45%">Start</td><td>{{ $booking->start_datetime?->format('d M Y, H:i') ?? '—' }}</td></tr>
                    <tr><td class="text-muted pe-3">End</td><td>{{ $booking->end_datetime?->format('d M Y, H:i') ?? '—' }}</td></tr>
                    <tr><td class="text-muted pe-3">Mode</td><td>{{ ucfirst($booking->mode ?? '—') }}</td></tr>
                    <tr><td class="text-muted pe-3">Duration Type</td><td>{{ ucfirst($booking->duration_type ?? '—') }}</td></tr>
                    <tr><td class="text-muted pe-3">Pickup / Delivery</td><td>{{ $booking->pickup_location ?? $booking->delivery_address ?? '—' }}</td></tr>
                    <tr><td class="text-muted pe-3">Notes</td><td>{{ $booking->notes ?? '—' }}</td></tr>
                </table>

                <hr style="border-color:var(--border);">

                <h6 class="text-muted mb-3" style="font-size:0.8rem;text-transform:uppercase;letter-spacing:1px;">Amount Breakdown</h6>
                <table class="table table-sm mb-0" style="--bs-table-bg:transparent;--bs-table-border-color:var(--border);color:var(--text);">
                    <tr><td class="text-muted pe-3">Base Amount</td><td>KES {{ number_format($booking->base_amount ?? 0) }}</td></tr>
                    <tr><td class="text-muted pe-3">Platform Fee</td><td>KES {{ number_format($booking->platform_fee ?? 0) }}</td></tr>
                    <tr><td class="text-muted pe-3">Security Deposit</td><td>KES {{ number_format($booking->security_deposit ?? 0) }}</td></tr>
                    @if($booking->driver_surcharge)
                    <tr><td class="text-muted pe-3">Driver Surcharge</td><td>KES {{ number_format($booking->driver_surcharge) }}</td></tr>
                    @endif
                    @if($booking->delivery_fee)
                    <tr><td class="text-muted pe-3">Delivery Fee</td><td>KES {{ number_format($booking->delivery_fee) }}</td></tr>
                    @endif
                    <tr>
                        <td class="fw-bold">Total</td>
                        <td class="fw-bold text-amber">KES {{ number_format($booking->total_amount ?? 0) }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    {{-- People --}}
    <div class="col-lg-6">
        {{-- Client --}}
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="fas fa-user me-2 text-amber"></i>Client</h6>
            </div>
            <div class="card-body">
                @if($booking->client)
                <div class="fw-semibold">{{ $booking->client->name }}</div>
                <div class="text-muted small">{{ $booking->client->email }}</div>
                <div class="text-muted small">{{ $booking->client->phone ?? 'No phone' }}</div>
                <a href="{{ route('admin.users.show', $booking->client) }}" class="btn btn-sm mt-2" style="background:var(--surface);border:1px solid var(--border);color:var(--text);">
                    <i class="fas fa-external-link-alt me-1"></i>View Profile
                </a>
                @else
                <span class="text-muted">No client assigned.</span>
                @endif
            </div>
        </div>

        {{-- Listing --}}
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="fas fa-car me-2 text-amber"></i>Listing</h6>
            </div>
            <div class="card-body">
                @if($booking->listing)
                <div class="fw-semibold">{{ $booking->listing->title }}</div>
                <div class="text-muted small">{{ $booking->listing->category?->name ?? 'Uncategorized' }}</div>
                <div class="text-muted small">Owner: <a href="{{ route('admin.users.show', $booking->listing->user) }}" class="text-amber">{{ $booking->listing->user?->name ?? '—' }}</a></div>
                <a href="{{ route('admin.listings.show', $booking->listing) }}" class="btn btn-sm mt-2" style="background:var(--surface);border:1px solid var(--border);color:var(--text);">
                    <i class="fas fa-external-link-alt me-1"></i>View Listing
                </a>
                @else
                <span class="text-muted">Listing not found.</span>
                @endif
            </div>
        </div>

        {{-- Operator --}}
        @if($booking->operator)
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="fas fa-hard-hat me-2 text-amber"></i>Operator</h6>
            </div>
            <div class="card-body">
                <div class="fw-semibold">{{ $booking->operator->name }}</div>
                <div class="text-muted small">{{ $booking->operator->email }}</div>
                <div class="text-muted small">{{ $booking->operator->phone ?? 'No phone' }}</div>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Payments --}}
<div class="card mb-4">
    <div class="card-header">
        <h6 class="mb-0 fw-bold"><i class="fas fa-credit-card me-2 text-amber"></i>Payments</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-dark mb-0" style="--bs-table-bg:transparent;--bs-table-border-color:var(--border);">
            <thead style="border-bottom:1px solid var(--border);">
                <tr>
                    <th class="text-muted fw-normal ps-3">Amount</th>
                    <th class="text-muted fw-normal">Method</th>
                    <th class="text-muted fw-normal">Status</th>
                    <th class="text-muted fw-normal">Paid At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($booking->payments as $payment)
                <tr style="border-color:var(--border);">
                    <td class="ps-3 py-3 fw-bold text-amber">KES {{ number_format($payment->amount ?? 0) }}</td>
                    <td class="py-3">{{ ucfirst($payment->method ?? '—') }}</td>
                    <td class="py-3">
                        @if(($payment->status ?? '') === 'completed')
                            <span class="badge badge-active rounded-pill">Completed</span>
                        @elseif(($payment->status ?? '') === 'pending')
                            <span class="badge badge-pending rounded-pill">Pending</span>
                        @else
                            <span class="badge badge-suspended rounded-pill">{{ ucfirst($payment->status ?? '—') }}</span>
                        @endif
                    </td>
                    <td class="py-3 text-muted">{{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('d M Y, H:i') : '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center py-4 text-muted">No payments recorded.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Dispute --}}
@if($booking->dispute)
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-bold"><i class="fas fa-exclamation-triangle me-2" style="color:var(--danger);"></i>Dispute</h6>
        @if($booking->dispute->status === 'open')
            <span class="badge badge-suspended rounded-pill">Open</span>
        @else
            <span class="badge badge-active rounded-pill">Resolved</span>
        @endif
    </div>
    <div class="card-body">
        <div class="mb-2">
            <span class="text-muted small">Reason:</span>
            <p class="mb-0 mt-1">{{ $booking->dispute->description ?? '—' }}</p>
        </div>
        @if($booking->dispute->status === 'resolved')
        <div class="mt-3 p-3 rounded" style="background:rgba(46,204,138,0.08);border:1px solid rgba(46,204,138,0.2);">
            <div class="text-muted small mb-1">Resolution:</div>
            <p class="mb-0">{{ $booking->dispute->resolution ?? $booking->dispute->admin_ruling ?? '—' }}</p>
            <small class="text-muted">Resolved: {{ $booking->dispute->resolved_at?->format('d M Y') ?? '—' }}</small>
        </div>
        @else
        <a href="{{ route('admin.disputes.show', $booking->dispute) }}" class="btn btn-sm btn-amber mt-2">
            <i class="fas fa-gavel me-1"></i>Resolve Dispute
        </a>
        @endif
    </div>
</div>
@endif
@endsection
