@extends('layouts.admin')
@section('title', 'Dispute #' . $dispute->id)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.disputes.index') }}" class="text-muted text-decoration-none">
        <i class="fas fa-arrow-left me-1"></i>Back to Disputes
    </a>
</div>

<div class="d-flex align-items-center gap-3 mb-4">
    <h4 class="mb-0 fw-bold">Dispute #{{ $dispute->id }}</h4>
    @if($dispute->status === 'open')
        <span class="badge badge-suspended rounded-pill px-3">Open</span>
    @else
        <span class="badge badge-active rounded-pill px-3">Resolved</span>
    @endif
</div>

<div class="row g-4 mb-4">
    {{-- Dispute Info --}}
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="fas fa-exclamation-triangle me-2" style="color:var(--danger);"></i>Dispute Details</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="text-muted mb-1" style="font-size:0.8rem;text-transform:uppercase;letter-spacing:1px;">Reason / Description</div>
                    <p style="line-height:1.7;">{{ $dispute->description ?? '—' }}</p>
                </div>
                @if($dispute->type)
                <div class="mb-3">
                    <span class="text-muted small">Type: </span>
                    <span class="badge badge-pending rounded-pill">{{ ucfirst(str_replace('_',' ',$dispute->type)) }}</span>
                </div>
                @endif
                <table class="table table-sm mb-0" style="--bs-table-bg:transparent;--bs-table-border-color:var(--border);color:var(--text);">
                    <tr><td class="text-muted pe-3">Raised By</td><td>{{ $dispute->raisedBy?->name ?? '—' }}</td></tr>
                    <tr><td class="text-muted pe-3">Against</td><td>{{ $dispute->against?->name ?? '—' }}</td></tr>
                    <tr><td class="text-muted pe-3">Filed</td><td>{{ $dispute->created_at->format('d M Y, H:i') }}</td></tr>
                </table>
            </div>
        </div>
    </div>

    {{-- Booking Info --}}
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="fas fa-calendar-check me-2 text-amber"></i>Booking Information</h6>
            </div>
            <div class="card-body">
                @if($dispute->booking)
                @php $booking = $dispute->booking; @endphp
                <table class="table table-sm mb-0" style="--bs-table-bg:transparent;--bs-table-border-color:var(--border);color:var(--text);">
                    <tr>
                        <td class="text-muted pe-3">Ref</td>
                        <td>
                            <a href="{{ route('admin.bookings.show', $booking) }}" class="text-amber text-decoration-none" style="font-family:monospace;">
                                {{ $booking->booking_ref }}
                            </a>
                        </td>
                    </tr>
                    <tr><td class="text-muted pe-3">Client</td><td>{{ $booking->client?->name ?? '—' }}</td></tr>
                    <tr><td class="text-muted pe-3">Listing</td><td>{{ $booking->listing?->title ?? '—' }}</td></tr>
                    <tr><td class="text-muted pe-3">Owner</td><td>{{ $booking->listing?->user?->name ?? '—' }}</td></tr>
                    <tr><td class="text-muted pe-3">Amount</td><td class="fw-bold text-amber">KES {{ number_format($booking->total_amount ?? 0) }}</td></tr>
                    <tr><td class="text-muted pe-3">Start</td><td>{{ $booking->start_datetime?->format('d M Y') ?? '—' }}</td></tr>
                    <tr><td class="text-muted pe-3">End</td><td>{{ $booking->end_datetime?->format('d M Y') ?? '—' }}</td></tr>
                    <tr><td class="text-muted pe-3">Booking Status</td><td>
                        <span class="badge badge-pending rounded-pill">{{ ucfirst(str_replace('_',' ',$booking->status)) }}</span>
                    </td></tr>
                </table>
                @else
                <div class="text-center text-muted py-4">Booking not found.</div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Resolution --}}
@if($dispute->status === 'open')
<div class="card">
    <div class="card-header">
        <h6 class="mb-0 fw-bold"><i class="fas fa-gavel me-2 text-amber"></i>Resolve Dispute</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.disputes.resolve', $dispute) }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Resolution Notes <span class="text-danger">*</span></label>
                <textarea name="resolution" rows="4" required placeholder="Describe the resolution and reasoning..."
                    class="form-control" style="background:var(--black);border-color:var(--border);color:var(--text);">{{ old('resolution') }}</textarea>
                @error('resolution') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>
            <div class="mb-4">
                <label class="form-label">Outcome <span class="text-danger">*</span></label>
                <select name="outcome" required class="form-select" style="background:var(--black);border-color:var(--border);color:var(--text);">
                    <option value="">Select outcome...</option>
                    <option value="refund_client" {{ old('outcome') === 'refund_client' ? 'selected' : '' }}>
                        Refund Client (full refund, cancel booking)
                    </option>
                    <option value="release_owner" {{ old('outcome') === 'release_owner' ? 'selected' : '' }}>
                        Release to Owner (pay owner, complete booking)
                    </option>
                    <option value="split" {{ old('outcome') === 'split' ? 'selected' : '' }}>
                        Split 50/50 (half to each party)
                    </option>
                </select>
                @error('outcome') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>
            <button type="submit" class="btn btn-amber px-4"
                onclick="return confirm('Resolve this dispute? This action will credit wallets and update booking status.')">
                <i class="fas fa-gavel me-2"></i>Submit Resolution
            </button>
        </form>
    </div>
</div>

@else
{{-- Already resolved --}}
<div class="card">
    <div class="card-header">
        <h6 class="mb-0 fw-bold"><i class="fas fa-check-circle me-2 text-green"></i>Resolution</h6>
    </div>
    <div class="card-body">
        <div class="p-3 rounded mb-3" style="background:rgba(46,204,138,0.08);border:1px solid rgba(46,204,138,0.2);">
            <p class="mb-0">{{ $dispute->resolution ?? $dispute->admin_ruling ?? '—' }}</p>
        </div>
        <small class="text-muted">
            <i class="fas fa-clock me-1"></i>Resolved: {{ $dispute->resolved_at?->format('d M Y, H:i') ?? '—' }}
        </small>
    </div>
</div>
@endif
@endsection
