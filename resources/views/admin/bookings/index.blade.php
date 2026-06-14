@extends('layouts.admin')
@section('title', 'Bookings')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Bookings</h4>
</div>

{{-- Filters --}}
<div class="card mb-4">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-auto">
                <select name="status" class="form-select form-select-sm" style="background:var(--black);border-color:var(--border);color:var(--text);" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="pending_payment"  {{ request('status') === 'pending_payment'  ? 'selected' : '' }}>Pending Payment</option>
                    <option value="confirmed"        {{ request('status') === 'confirmed'        ? 'selected' : '' }}>Confirmed</option>
                    <option value="active"           {{ request('status') === 'active'           ? 'selected' : '' }}>Active</option>
                    <option value="completed"        {{ request('status') === 'completed'        ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled"        {{ request('status') === 'cancelled'        ? 'selected' : '' }}>Cancelled</option>
                    <option value="disputed"         {{ request('status') === 'disputed'         ? 'selected' : '' }}>Disputed</option>
                </select>
            </div>
            <div class="col-auto">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search ref or client name..."
                    class="form-control form-control-sm" style="background:var(--black);border-color:var(--border);color:var(--text);width:240px;">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-amber">Search</button>
                @if(request()->anyFilled(['status','q']))
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-secondary ms-1">Clear</a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-dark mb-0" style="--bs-table-bg:transparent;--bs-table-border-color:var(--border);">
            <thead style="border-bottom:1px solid var(--border);">
                <tr>
                    <th class="text-muted fw-normal ps-3">Ref</th>
                    <th class="text-muted fw-normal">Client</th>
                    <th class="text-muted fw-normal">Listing</th>
                    <th class="text-muted fw-normal">Total (KES)</th>
                    <th class="text-muted fw-normal">Status</th>
                    <th class="text-muted fw-normal">Start Date</th>
                    <th class="text-muted fw-normal">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                <tr style="border-color:var(--border);">
                    <td class="ps-3 py-3">
                        <a href="{{ route('admin.bookings.show', $booking) }}" class="text-amber text-decoration-none" style="font-family:monospace;font-size:0.8rem;">
                            {{ $booking->booking_ref }}
                        </a>
                    </td>
                    <td class="py-3">
                        <div class="fw-semibold">{{ $booking->client->name ?? '—' }}</div>
                        <small class="text-muted">{{ $booking->client->email ?? '' }}</small>
                    </td>
                    <td class="py-3">
                        <div>{{ Str::limit($booking->listing->title ?? '—', 30) }}</div>
                    </td>
                    <td class="py-3 fw-bold text-amber">{{ number_format($booking->total_amount ?? 0) }}</td>
                    <td class="py-3">
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
                        <span class="badge rounded-pill" style="{{ $style }}">
                            {{ ucfirst(str_replace('_', ' ', $st)) }}
                        </span>
                    </td>
                    <td class="py-3">
                        <small class="text-muted">{{ $booking->start_datetime?->format('d M Y') ?? '—' }}</small>
                    </td>
                    <td class="py-3">
                        <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-sm" style="background:var(--surface);border:1px solid var(--border);color:var(--text);">
                            <i class="fas fa-eye me-1"></i>View
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="fas fa-calendar-times fa-2x mb-2 d-block"></i>
                        No bookings found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($bookings->hasPages())
    <div class="card-footer" style="background:transparent;border-top:1px solid var(--border);">
        {{ $bookings->links() }}
    </div>
    @endif
</div>
@endsection
