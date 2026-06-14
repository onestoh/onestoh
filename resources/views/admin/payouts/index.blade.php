@extends('layouts.admin')
@section('title', 'Payouts')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-0 fw-bold">Payout Requests</h4>
        <small class="text-muted">Manage M-Pesa payout requests from owners</small>
    </div>
</div>

{{-- Filters --}}
<div class="card mb-4">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-auto">
                <select name="status" class="form-select form-select-sm" style="background:var(--black);border-color:var(--border);color:var(--text);" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-auto">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search M-Pesa number..." class="form-control form-control-sm" style="background:var(--black);border-color:var(--border);color:var(--text);width:220px;">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-amber">Search</button>
                @if(request()->anyFilled(['status','q']))
                    <a href="{{ route('admin.payouts.index') }}" class="btn btn-sm btn-outline-secondary ms-1">Clear</a>
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
                    <th class="text-muted fw-normal ps-3">User</th>
                    <th class="text-muted fw-normal">Amount (KES)</th>
                    <th class="text-muted fw-normal">M-Pesa Number</th>
                    <th class="text-muted fw-normal">Account Name</th>
                    <th class="text-muted fw-normal">Status</th>
                    <th class="text-muted fw-normal">Requested</th>
                    <th class="text-muted fw-normal">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payouts as $payout)
                <tr style="border-color:var(--border);" x-data="{ showReject: false }">
                    <td class="ps-3 py-3">
                        <div class="fw-semibold">{{ $payout->user->name ?? '—' }}</div>
                        <small class="text-muted">{{ $payout->user->email ?? '' }}</small>
                    </td>
                    <td class="py-3">
                        <span class="fw-bold text-amber">{{ number_format($payout->amount, 2) }}</span>
                    </td>
                    <td class="py-3">{{ $payout->destination ?? '—' }}</td>
                    <td class="py-3">{{ $payout->account_name ?? '—' }}</td>
                    <td class="py-3">
                        @if($payout->status === 'pending')
                            <span class="badge badge-pending rounded-pill">Pending</span>
                        @elseif($payout->status === 'approved')
                            <span class="badge badge-active rounded-pill">Approved</span>
                        @else
                            <span class="badge badge-suspended rounded-pill">Rejected</span>
                        @endif
                    </td>
                    <td class="py-3">
                        <small class="text-muted">{{ $payout->created_at->format('d M Y, H:i') }}</small>
                    </td>
                    <td class="py-3">
                        @if($payout->status === 'pending')
                        <div class="d-flex gap-2 align-items-start flex-wrap">
                            {{-- Approve --}}
                            <form method="POST" action="{{ route('admin.payouts.approve', $payout) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm" style="background:rgba(46,204,138,0.15);color:var(--green);border:1px solid rgba(46,204,138,0.3);"
                                    onclick="return confirm('Approve this payout of KES {{ number_format($payout->amount,2) }}?')">
                                    <i class="fas fa-check me-1"></i>Approve
                                </button>
                            </form>

                            {{-- Reject toggle --}}
                            <div>
                                <button type="button" class="btn btn-sm" style="background:rgba(232,64,64,0.15);color:var(--danger);border:1px solid rgba(232,64,64,0.3);"
                                    @click="showReject = !showReject">
                                    <i class="fas fa-times me-1"></i>Reject
                                </button>
                                <div x-show="showReject" x-cloak class="mt-2" style="min-width:220px;">
                                    <form method="POST" action="{{ route('admin.payouts.reject', $payout) }}">
                                        @csrf
                                        <textarea name="reason" rows="2" required placeholder="Reason for rejection..." class="form-control form-control-sm mb-1" style="background:var(--black);border-color:var(--border);color:var(--text);"></textarea>
                                        <button type="submit" class="btn btn-sm w-100" style="background:var(--danger);color:#fff;">Confirm Reject</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @else
                            @if($payout->admin_notes)
                                <small class="text-muted" title="{{ $payout->admin_notes }}">
                                    <i class="fas fa-info-circle"></i> {{ Str::limit($payout->admin_notes, 40) }}
                                </small>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="fas fa-money-bill-wave fa-2x mb-2 d-block"></i>
                        No payout requests found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($payouts->hasPages())
    <div class="card-footer" style="background:transparent;border-top:1px solid var(--border);">
        {{ $payouts->links() }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush
