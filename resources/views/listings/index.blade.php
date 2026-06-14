@extends('layouts.dashboard')

@section('title', 'My Listings')
@section('page-title', 'My Listings')

@push('styles')
<style>
    .listing-row { background: var(--black); border: 1px solid var(--border); border-radius: 10px; padding: 1rem; margin-bottom: 0.75rem; display: flex; gap: 1rem; align-items: flex-start; }
    .listing-thumb { width: 90px; height: 68px; object-fit: cover; border-radius: 7px; flex-shrink: 0; background: var(--surface); display: flex; align-items: center; justify-content: center; }
    .listing-thumb img { width: 90px; height: 68px; object-fit: cover; border-radius: 7px; }
    .status-badge { padding: 2px 8px; border-radius: 12px; font-size: 0.72rem; font-weight: 600; }
    .status-active { background: rgba(46,204,138,0.15); color: var(--green); border: 1px solid rgba(46,204,138,0.3); }
    .status-pending { background: rgba(232,146,42,0.15); color: var(--amber); border: 1px solid rgba(232,146,42,0.3); }
    .status-inactive, .status-rejected { background: rgba(232,64,64,0.15); color: var(--danger); border: 1px solid rgba(232,64,64,0.3); }
    .status-deleted { background: rgba(100,100,100,0.2); color: var(--muted); border: 1px solid var(--border); }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 style="color:var(--text);font-weight:700;margin:0">My Listings</h4>
        <span style="color:var(--muted);font-size:.875rem">{{ $listings->total() }} total</span>
    </div>
    <a href="{{ route('listings.create') }}" class="btn btn-amber">
        <i class="fas fa-plus me-2"></i>Add Listing
    </a>
</div>

@if($listings->isEmpty())
<div class="text-center py-5" style="color:var(--muted)">
    <i class="fas fa-car-side fa-3x mb-3 d-block" style="opacity:.3"></i>
    <h5 style="color:var(--text)">No listings yet</h5>
    <p>Start earning by listing your vehicle or machinery.</p>
    <a href="{{ route('listings.create') }}" class="btn btn-amber mt-2">Create Your First Listing</a>
</div>
@else

@foreach($listings as $listing)
<div class="listing-row {{ $listing->trashed() ? 'opacity-50' : '' }}">
    <div class="listing-thumb">
        @if($listing->primaryPhoto)
            <img src="{{ Storage::url($listing->primaryPhoto->file_path) }}" alt="">
        @else
            <i class="{{ $listing->category?->icon ?? 'fas fa-car' }} text-amber" style="font-size:1.4rem"></i>
        @endif
    </div>

    <div class="flex-grow-1 min-w-0">
        <div class="d-flex align-items-start gap-2 flex-wrap mb-1">
            <span class="fw-semibold" style="color:var(--text); font-size:.95rem">{{ $listing->title }}</span>
            @if($listing->trashed())
                <span class="status-badge status-deleted">Deleted</span>
            @else
                <span class="status-badge status-{{ $listing->status }}">{{ ucfirst($listing->status) }}</span>
            @endif
            @if($listing->is_featured)
                <span class="status-badge" style="background:rgba(232,146,42,0.2);color:var(--amber);"><i class="fas fa-star me-1" style="font-size:.65rem"></i>Featured</span>
            @endif
        </div>
        <div style="color:var(--muted); font-size:.8rem; margin-bottom:.4rem">
            {{ $listing->category?->name }} &nbsp;·&nbsp;
            <i class="fas fa-map-marker-alt me-1"></i>{{ $listing->county }}
            @if($listing->daily_rate)
                &nbsp;·&nbsp; KES {{ number_format($listing->daily_rate) }}/day
            @endif
        </div>
        <div style="font-size:.78rem; color:var(--muted)">
            {{ $listing->photos->count() ?? 0 }} photo(s) &nbsp;·&nbsp;
            {{ $listing->created_at->format('d M Y') }}
        </div>
    </div>

    <div class="d-flex gap-2 flex-shrink-0">
        @unless($listing->trashed())
        <a href="{{ route('listings.edit', $listing) }}" class="btn btn-sm" style="border:1px solid var(--border);color:var(--muted)">
            <i class="fas fa-edit"></i>
        </a>
        <a href="{{ route('listings.show', $listing->slug) }}" target="_blank" class="btn btn-sm" style="border:1px solid var(--border);color:var(--muted)">
            <i class="fas fa-eye"></i>
        </a>
        <form method="POST" action="{{ route('listings.destroy', $listing) }}" onsubmit="return confirm('Remove this listing?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-sm" style="border:1px solid rgba(232,64,64,0.3);color:var(--danger)">
                <i class="fas fa-trash"></i>
            </button>
        </form>
        @endunless
    </div>
</div>
@endforeach

<div class="mt-3">{{ $listings->links('pagination::bootstrap-5') }}</div>
@endif
@endsection
