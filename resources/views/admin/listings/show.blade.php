@extends('layouts.admin')
@section('title', 'Listing: ' . $listing->title)

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.listings.index') }}" class="text-muted text-decoration-none">
        <i class="fas fa-arrow-left me-1"></i>Back to Listings
    </a>
</div>

{{-- Header --}}
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-1 fw-bold">{{ $listing->title }}</h4>
        <div class="d-flex align-items-center gap-2">
            @if($listing->status === 'approved')
                <span class="badge badge-active rounded-pill">Approved</span>
            @elseif($listing->status === 'pending')
                <span class="badge badge-pending rounded-pill">Pending</span>
            @else
                <span class="badge badge-suspended rounded-pill">{{ ucfirst($listing->status) }}</span>
            @endif
            @if($listing->is_featured ?? false)
                <span class="badge rounded-pill" style="background:rgba(232,146,42,0.2);color:var(--amber);border:1px solid rgba(232,146,42,0.4);">
                    <i class="fas fa-star me-1"></i>Featured
                </span>
            @endif
        </div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        @if($listing->status !== 'approved')
        <form method="POST" action="{{ route('admin.listings.approve', $listing) }}">
            @csrf
            <button type="submit" class="btn btn-sm btn-amber">
                <i class="fas fa-check me-1"></i>Approve Listing
            </button>
        </form>
        @endif
        <form method="POST" action="{{ route('admin.listings.feature', $listing) }}">
            @csrf
            <button type="submit" class="btn btn-sm" style="background:rgba(232,146,42,0.15);color:var(--amber);border:1px solid rgba(232,146,42,0.3);">
                <i class="fas fa-star me-1"></i>{{ ($listing->is_featured ?? false) ? 'Unfeature' : 'Feature' }}
            </button>
        </form>
        <form method="POST" action="{{ route('admin.listings.destroy', $listing) }}" onsubmit="return confirm('Delete this listing permanently?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm" style="background:rgba(232,64,64,0.15);color:var(--danger);border:1px solid rgba(232,64,64,0.3);">
                <i class="fas fa-trash me-1"></i>Delete
            </button>
        </form>
    </div>
</div>

<div class="row g-4">
    {{-- Left: Details --}}
    <div class="col-lg-7">
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="fas fa-info-circle me-2 text-amber"></i>Listing Details</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm mb-3" style="--bs-table-bg:transparent;--bs-table-border-color:var(--border);color:var(--text);">
                    <tr>
                        <td class="text-muted pe-3" style="width:140px;">Owner</td>
                        <td>
                            <a href="{{ route('admin.users.show', $listing->user) }}" class="text-amber text-decoration-none">
                                {{ $listing->user->name ?? '—' }}
                            </a>
                            <small class="text-muted d-block">{{ $listing->user->email ?? '' }}</small>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted pe-3">Category</td>
                        <td>{{ $listing->category->name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted pe-3">Price / Day</td>
                        <td class="fw-bold text-amber">KES {{ number_format($listing->price_per_day ?? 0) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted pe-3">Location</td>
                        <td>{{ $listing->location ?? $listing->county ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted pe-3">Created</td>
                        <td>{{ $listing->created_at->format('d M Y') }}</td>
                    </tr>
                </table>

                @if($listing->description)
                <div class="mb-3">
                    <div class="text-muted mb-1" style="font-size:0.8rem;text-transform:uppercase;letter-spacing:1px;">Description</div>
                    <p style="line-height:1.7;color:var(--text);">{{ $listing->description }}</p>
                </div>
                @endif

                @if($listing->features)
                <div>
                    <div class="text-muted mb-2" style="font-size:0.8rem;text-transform:uppercase;letter-spacing:1px;">Features</div>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach((is_array($listing->features) ? $listing->features : explode(',', $listing->features)) as $feature)
                        @if(trim($feature))
                        <span style="background:var(--black);border:1px solid var(--border);border-radius:6px;padding:3px 10px;font-size:0.8rem;">
                            <i class="fas fa-check me-1" style="color:var(--green);font-size:0.7rem;"></i>{{ trim($feature) }}
                        </span>
                        @endif
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Right: Photos --}}
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="fas fa-images me-2 text-amber"></i>Photos ({{ $listing->photos->count() }})</h6>
            </div>
            <div class="card-body">
                @if($listing->photos->isEmpty())
                    <div class="text-center py-4 text-muted"><i class="fas fa-image fa-2x mb-2 d-block"></i>No photos uploaded.</div>
                @else
                <div class="row g-2">
                    @foreach($listing->photos as $photo)
                    <div class="col-6">
                        <a href="{{ Storage::url($photo->path) }}" target="_blank">
                            <img src="{{ Storage::url($photo->path) }}" alt="Photo" class="img-fluid rounded" style="object-fit:cover;height:120px;width:100%;">
                        </a>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Bookings --}}
<div class="card mt-4">
    <div class="card-header">
        <h6 class="mb-0 fw-bold"><i class="fas fa-calendar-check me-2 text-amber"></i>Recent Bookings</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-dark mb-0" style="--bs-table-bg:transparent;--bs-table-border-color:var(--border);">
            <thead style="border-bottom:1px solid var(--border);">
                <tr>
                    <th class="text-muted fw-normal ps-3">Ref</th>
                    <th class="text-muted fw-normal">Client</th>
                    <th class="text-muted fw-normal">Start</th>
                    <th class="text-muted fw-normal">End</th>
                    <th class="text-muted fw-normal">Amount</th>
                    <th class="text-muted fw-normal">Status</th>
                    <th class="text-muted fw-normal">View</th>
                </tr>
            </thead>
            <tbody>
                @forelse($listing->bookings->take(10) as $booking)
                <tr style="border-color:var(--border);">
                    <td class="ps-3 py-2"><code style="color:var(--amber);font-size:0.75rem;">{{ $booking->booking_ref }}</code></td>
                    <td class="py-2">{{ $booking->client->name ?? '—' }}</td>
                    <td class="py-2"><small class="text-muted">{{ $booking->start_datetime?->format('d M Y') ?? '—' }}</small></td>
                    <td class="py-2"><small class="text-muted">{{ $booking->end_datetime?->format('d M Y') ?? '—' }}</small></td>
                    <td class="py-2 text-amber">KES {{ number_format($booking->total_amount ?? 0) }}</td>
                    <td class="py-2">
                        <span class="badge rounded-pill"
                            @if(in_array($booking->status, ['cancelled','disputed'])) style="background:rgba(232,64,64,0.15);color:var(--danger);border:1px solid rgba(232,64,64,0.3);"
                            @elseif($booking->status === 'completed') style="background:rgba(112,136,168,0.15);color:var(--muted);border:1px solid rgba(112,136,168,0.3);"
                            @elseif($booking->status === 'active') style="background:rgba(46,204,138,0.15);color:var(--green);border:1px solid rgba(46,204,138,0.3);"
                            @else style="background:rgba(232,146,42,0.15);color:var(--amber);border:1px solid rgba(232,146,42,0.3);"
                            @endif>
                            {{ ucfirst(str_replace('_',' ',$booking->status)) }}
                        </span>
                    </td>
                    <td class="py-2">
                        <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-sm" style="background:var(--surface);border:1px solid var(--border);color:var(--text);">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4 text-muted">No bookings for this listing yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
