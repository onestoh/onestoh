@extends('layouts.admin')
@section('title', 'Disputes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Disputes</h4>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-dark mb-0" style="--bs-table-bg:transparent;--bs-table-border-color:var(--border);">
            <thead style="border-bottom:1px solid var(--border);">
                <tr>
                    <th class="text-muted fw-normal ps-3">Booking Ref</th>
                    <th class="text-muted fw-normal">Client</th>
                    <th class="text-muted fw-normal">Listing</th>
                    <th class="text-muted fw-normal">Reason</th>
                    <th class="text-muted fw-normal">Status</th>
                    <th class="text-muted fw-normal">Created</th>
                    <th class="text-muted fw-normal">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($disputes as $dispute)
                <tr style="border-color:var(--border);">
                    <td class="ps-3 py-3">
                        @if($dispute->booking)
                        <a href="{{ route('admin.bookings.show', $dispute->booking) }}" class="text-amber text-decoration-none" style="font-family:monospace;font-size:0.8rem;">
                            {{ $dispute->booking->booking_ref }}
                        </a>
                        @else
                        <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="py-3">{{ $dispute->booking?->client?->name ?? '—' }}</td>
                    <td class="py-3">{{ Str::limit($dispute->booking?->listing?->title ?? '—', 28) }}</td>
                    <td class="py-3">
                        <span title="{{ $dispute->description }}">{{ Str::limit($dispute->description, 50) }}</span>
                    </td>
                    <td class="py-3">
                        @if($dispute->status === 'open')
                            <span class="badge badge-suspended rounded-pill">Open</span>
                        @else
                            <span class="badge badge-active rounded-pill">Resolved</span>
                        @endif
                    </td>
                    <td class="py-3">
                        <small class="text-muted">{{ $dispute->created_at->format('d M Y') }}</small>
                    </td>
                    <td class="py-3">
                        <a href="{{ route('admin.disputes.show', $dispute) }}" class="btn btn-sm" style="background:var(--surface);border:1px solid var(--border);color:var(--text);">
                            <i class="fas fa-gavel me-1"></i>View
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="fas fa-balance-scale fa-2x mb-2 d-block"></i>
                        No disputes found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($disputes->hasPages())
    <div class="card-footer" style="background:transparent;border-top:1px solid var(--border);">
        {{ $disputes->links() }}
    </div>
    @endif
</div>
@endsection
