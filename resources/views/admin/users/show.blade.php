@extends('layouts.admin')
@section('title', 'User: ' . $user->name)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.users.index') }}" class="text-muted text-decoration-none">
        <i class="fas fa-arrow-left me-1"></i>Back to Users
    </a>
</div>

<div class="row g-4">
    {{-- Left: User Info --}}
    <div class="col-lg-5">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold"><i class="fas fa-user me-2 text-amber"></i>User Info</h6>
                @if($user->status === 'active')
                    <span class="badge badge-active rounded-pill">Active</span>
                @elseif($user->status === 'suspended')
                    <span class="badge badge-suspended rounded-pill">Suspended</span>
                @else
                    <span class="badge badge-pending rounded-pill">{{ ucfirst($user->status) }}</span>
                @endif
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div style="width:72px;height:72px;border-radius:50%;background:var(--amber);display:flex;align-items:center;justify-content:center;font-size:1.8rem;font-weight:800;color:#000;margin:0 auto;">
                        {{ strtoupper(substr($user->name,0,1)) }}
                    </div>
                    <h5 class="mt-3 mb-0">{{ $user->name }}</h5>
                    <small class="text-muted">{{ $user->email }}</small>
                </div>

                <table class="table table-sm mb-0" style="--bs-table-bg:transparent;--bs-table-border-color:var(--border);color:var(--text);">
                    <tr><td class="text-muted pe-3">Phone</td><td>{{ $user->phone ?? '—' }}</td></tr>
                    <tr><td class="text-muted pe-3">Role</td><td><span class="badge badge-pending">{{ str_replace('_',' ', ucwords($user->role ?? 'user', '_')) }}</span></td></tr>
                    <tr><td class="text-muted pe-3">Joined</td><td>{{ $user->created_at->format('d M Y') }}</td></tr>
                    <tr><td class="text-muted pe-3">Referral Code</td><td><code style="color:var(--amber);">{{ $user->referral_code ?? '—' }}</code></td></tr>
                    <tr>
                        <td class="text-muted pe-3">Wallet</td>
                        <td class="fw-bold text-amber">KES {{ number_format($user->wallet?->balance ?? 0, 2) }}</td>
                    </tr>
                </table>

                <div class="d-flex gap-2 mt-4">
                    @if($user->status !== 'suspended')
                    <form method="POST" action="{{ route('admin.users.suspend', $user) }}" class="flex-fill">
                        @csrf
                        <button type="submit" class="btn btn-sm w-100" style="background:rgba(232,64,64,0.15);color:var(--danger);border:1px solid rgba(232,64,64,0.3);"
                            onclick="return confirm('Suspend this user?')">
                            <i class="fas fa-ban me-1"></i>Suspend
                        </button>
                    </form>
                    @endif
                    @if(!$user->email_verified_at || $user->status !== 'active')
                    <form method="POST" action="{{ route('admin.users.verify', $user) }}" class="flex-fill">
                        @csrf
                        <button type="submit" class="btn btn-sm w-100 btn-amber">
                            <i class="fas fa-check me-1"></i>Verify / Activate
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- KYC Documents --}}
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="fas fa-id-card me-2 text-amber"></i>KYC Documents</h6>
            </div>
            <div class="card-body p-0">
                @forelse($user->kycDocuments ?? [] as $doc)
                <div class="p-3" style="border-bottom:1px solid var(--border);">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="fw-semibold">{{ str_replace('_',' ',ucwords($doc->document_type ?? $doc->type, '_')) }}</span>
                        @if(($doc->status ?? '') === 'approved')
                            <span class="badge badge-active rounded-pill">Approved</span>
                        @elseif(($doc->status ?? '') === 'rejected')
                            <span class="badge badge-suspended rounded-pill">Rejected</span>
                        @else
                            <span class="badge badge-pending rounded-pill">Pending</span>
                        @endif
                    </div>
                    <small class="text-muted">Uploaded: {{ $doc->created_at->format('d M Y') }}</small>
                    <div class="d-flex gap-2 mt-2">
                        @if($doc->file_path ?? $doc->path ?? null)
                        <a href="{{ Storage::url($doc->file_path ?? $doc->path) }}" target="_blank" class="btn btn-sm" style="background:var(--surface);border:1px solid var(--border);color:var(--text);">
                            <i class="fas fa-eye me-1"></i>View File
                        </a>
                        @endif
                        @if(($doc->status ?? '') === 'pending')
                        <form method="POST" action="{{ route('admin.kyc.approve', $doc) }}">
                            @csrf
                            <button type="submit" class="btn btn-sm" style="background:rgba(46,204,138,0.15);color:var(--green);border:1px solid rgba(46,204,138,0.3);">
                                <i class="fas fa-check me-1"></i>Approve
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.kyc.reject', $doc) }}">
                            @csrf
                            <button type="submit" class="btn btn-sm" style="background:rgba(232,64,64,0.15);color:var(--danger);border:1px solid rgba(232,64,64,0.3);">
                                <i class="fas fa-times me-1"></i>Reject
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @empty
                <div class="p-4 text-center text-muted">No KYC documents uploaded.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Right: Listings & Bookings --}}
    <div class="col-lg-7">
        {{-- Recent Listings --}}
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold"><i class="fas fa-car me-2 text-amber"></i>Recent Listings</h6>
                <small class="text-muted">Latest 5</small>
            </div>
            <div class="table-responsive">
                <table class="table table-dark mb-0" style="--bs-table-bg:transparent;--bs-table-border-color:var(--border);">
                    <thead style="border-bottom:1px solid var(--border);">
                        <tr>
                            <th class="text-muted fw-normal ps-3">Title</th>
                            <th class="text-muted fw-normal">Category</th>
                            <th class="text-muted fw-normal">Price/Day</th>
                            <th class="text-muted fw-normal">Status</th>
                            <th class="text-muted fw-normal">View</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($user->listings->take(5) as $listing)
                        <tr style="border-color:var(--border);">
                            <td class="ps-3 py-2 fw-semibold">{{ Str::limit($listing->title, 30) }}</td>
                            <td class="py-2"><small class="text-muted">{{ $listing->category->name ?? '—' }}</small></td>
                            <td class="py-2 text-amber">KES {{ number_format($listing->price_per_day ?? 0) }}</td>
                            <td class="py-2">
                                @if($listing->status === 'approved')
                                    <span class="badge badge-active rounded-pill">Approved</span>
                                @elseif($listing->status === 'pending')
                                    <span class="badge badge-pending rounded-pill">Pending</span>
                                @else
                                    <span class="badge badge-suspended rounded-pill">{{ ucfirst($listing->status) }}</span>
                                @endif
                            </td>
                            <td class="py-2">
                                <a href="{{ route('admin.listings.show', $listing) }}" class="btn btn-sm" style="background:var(--surface);border:1px solid var(--border);color:var(--text);">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-3 text-muted">No listings found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent Bookings as Client --}}
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold"><i class="fas fa-calendar-check me-2 text-amber"></i>Recent Bookings (as Client)</h6>
                <small class="text-muted">Latest 5</small>
            </div>
            <div class="table-responsive">
                <table class="table table-dark mb-0" style="--bs-table-bg:transparent;--bs-table-border-color:var(--border);">
                    <thead style="border-bottom:1px solid var(--border);">
                        <tr>
                            <th class="text-muted fw-normal ps-3">Ref</th>
                            <th class="text-muted fw-normal">Listing</th>
                            <th class="text-muted fw-normal">Total</th>
                            <th class="text-muted fw-normal">Status</th>
                            <th class="text-muted fw-normal">View</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($user->clientBookings->take(5) as $booking)
                        <tr style="border-color:var(--border);">
                            <td class="ps-3 py-2"><code style="color:var(--amber);font-size:0.75rem;">{{ $booking->booking_ref }}</code></td>
                            <td class="py-2"><small>{{ Str::limit($booking->listing->title ?? '—', 25) }}</small></td>
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
                        <tr><td colspan="5" class="text-center py-3 text-muted">No bookings found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
