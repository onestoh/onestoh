@extends('layouts.admin')

@section('title', 'Listings')

@push('styles')
<style>
.form-control, .form-select { background: var(--black); border: 1px solid var(--border); color: var(--text); font-size: .85rem; }
.s-pill { padding: 2px 8px; border-radius: 12px; font-size: .72rem; font-weight: 600; }
.s-active { background: rgba(46,204,138,.15); color: var(--green); }
.s-pending { background: rgba(232,146,42,.15); color: var(--amber); }
.s-inactive, .s-rejected { background: rgba(232,64,64,.15); color: var(--danger); }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 style="color:var(--text);font-weight:700;margin:0">Listings</h4>
    <span style="color:var(--muted);font-size:.875rem">{{ $listings->total() }} total</span>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-6"><input type="text" name="q" class="form-control" placeholder="Search title, make…" value="{{ request('q') }}"></div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach(['pending','active','inactive','rejected'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3"><button type="submit" class="btn btn-amber w-100 btn-sm">Search</button></div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-sm mb-0" style="color:var(--text);--bs-table-bg:transparent">
            <thead style="font-size:.72rem;color:var(--muted);text-transform:uppercase;border-bottom:1px solid var(--border)">
                <tr><th class="px-3 py-2">Listing</th><th>Owner</th><th>Category</th><th>Price/Day</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @foreach($listings as $listing)
                <tr style="border-color:var(--border)" class="{{ $listing->trashed() ? 'opacity-50' : '' }}">
                    <td class="px-3 py-2">
                        <div class="d-flex align-items-center gap-2">
                            @if($listing->primaryPhoto)
                            <img src="{{ Storage::url($listing->primaryPhoto->file_path) }}" width="44" height="34" style="object-fit:cover;border-radius:4px">
                            @endif
                            <div>
                                <a href="{{ route('admin.listings.show', $listing) }}" style="color:var(--text);font-size:.875rem;font-weight:500;text-decoration:none">{{ Str::limit($listing->title, 35) }}</a>
                                @if($listing->is_featured)<span class="ms-1" style="color:var(--amber);font-size:.7rem">★ Featured</span>@endif
                            </div>
                        </div>
                    </td>
                    <td style="font-size:.82rem;color:var(--muted)">{{ $listing->user?->name }}</td>
                    <td style="font-size:.82rem;color:var(--muted)">{{ $listing->category?->name }}</td>
                    <td style="font-size:.85rem">{{ $listing->daily_rate ? 'KES '.number_format($listing->daily_rate) : '—' }}</td>
                    <td><span class="s-pill s-{{ $listing->status }}">{{ ucfirst($listing->status) }}</span></td>
                    <td>
                        <div class="d-flex gap-1">
                            @if($listing->status === 'pending')
                            <form method="POST" action="{{ route('admin.listings.approve', $listing) }}">
                                @csrf
                                <button class="btn btn-sm px-2 py-1" style="background:rgba(46,204,138,.15);color:var(--green);font-size:.72rem" title="Approve"><i class="fas fa-check"></i></button>
                            </form>
                            @endif
                            <form method="POST" action="{{ route('admin.listings.feature', $listing) }}">
                                @csrf
                                <button class="btn btn-sm px-2 py-1" style="background:rgba(232,146,42,.15);color:var(--amber);font-size:.72rem" title="{{ $listing->is_featured ? 'Unfeature' : 'Feature' }}">
                                    <i class="fas fa-star"></i>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.listings.destroy', $listing) }}" onsubmit="return confirm('Delete listing?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm px-2 py-1" style="background:rgba(232,64,64,.15);color:var(--danger);font-size:.72rem"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer" style="background:transparent;border-top:1px solid var(--border)">
        {{ $listings->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
