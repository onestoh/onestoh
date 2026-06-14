@extends('layouts.dashboard')

@section('title', 'My Commissions')
@section('page-title', 'Commission History')

@section('content')
<h4 class="mb-4" style="color:var(--text);font-weight:700">Commission History</h4>

@if($commissions->isEmpty())
<div class="text-center py-5" style="color:var(--muted)">
    <i class="fas fa-percent fa-3x mb-3 d-block" style="opacity:.3"></i>
    <h5 style="color:var(--text)">No commissions yet</h5>
    <p>Share your referral code with clients to start earning.</p>
</div>
@else
<div class="p-3 rounded-3" style="background:var(--surface);border:1px solid var(--border)">
    <div class="table-responsive">
        <table class="table table-sm mb-0" style="color:var(--text)">
            <thead>
                <tr style="border-color:var(--border);color:var(--muted);font-size:.75rem;text-transform:uppercase">
                    <th>Booking</th>
                    <th>Listing</th>
                    <th>Rate</th>
                    <th>Commission</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($commissions as $c)
                <tr style="border-color:var(--border)">
                    <td style="color:var(--muted);font-family:monospace;font-size:.8rem">{{ $c->booking_ref }}</td>
                    <td style="font-size:.875rem">{{ Str::limit($c->listing_title, 35) }}</td>
                    <td style="font-size:.82rem;color:var(--muted)">{{ $c->commission_rate ?? '—' }}%</td>
                    <td style="color:var(--green);font-weight:600">KES {{ number_format($c->amount) }}</td>
                    <td>
                        @if($c->status === 'paid')
                            <span style="color:var(--green);font-size:.78rem"><i class="fas fa-check-circle me-1"></i>Paid</span>
                        @else
                            <span style="color:var(--amber);font-size:.78rem"><i class="fas fa-clock me-1"></i>Pending</span>
                        @endif
                    </td>
                    <td style="color:var(--muted);font-size:.78rem">{{ \Carbon\Carbon::parse($c->created_at)->format('d M Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $commissions->links('pagination::bootstrap-5') }}</div>
@endif
@endsection
