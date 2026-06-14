@extends('layouts.dashboard')

@section('title', 'My Wallet')
@section('page-title', 'Wallet')

@push('styles')
<style>
.wallet-hero { background: linear-gradient(135deg, rgba(232,146,42,.15), rgba(232,146,42,.05)); border: 1px solid rgba(232,146,42,.3); border-radius: 14px; padding: 2rem; margin-bottom: 1.5rem; }
.tx-row { display: flex; align-items: center; gap: 1rem; padding: .75rem 0; border-bottom: 1px solid var(--border); }
.tx-row:last-child { border: none; }
.tx-icon { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: .85rem; }
.tx-credit .tx-icon { background: rgba(46,204,138,.15); color: var(--green); }
.tx-debit .tx-icon { background: rgba(232,64,64,.15); color: var(--danger); }
.tx-amount-credit { color: var(--green); font-weight: 700; }
.tx-amount-debit { color: var(--danger); font-weight: 700; }
.form-control, .form-select { background: var(--black); border: 1px solid var(--border); color: var(--text); }
.form-control:focus { background: var(--black); border-color: var(--amber); color: var(--text); box-shadow: none; }
.form-label { color: var(--muted); font-size: .82rem; }
</style>
@endpush

@section('content')

{{-- Wallet Hero --}}
<div class="wallet-hero">
    <div class="row align-items-center g-3">
        <div class="col-md-6">
            <div style="color:var(--muted);font-size:.78rem;text-transform:uppercase;letter-spacing:1px">Available Balance</div>
            <div style="color:var(--amber);font-size:2.5rem;font-weight:800;line-height:1.1">
                KES {{ number_format($user->wallet?->balance ?? 0, 2) }}
            </div>
            @if(($user->wallet?->pending_balance ?? 0) > 0)
            <div style="color:var(--muted);font-size:.82rem;margin-top:.4rem">
                <i class="fas fa-clock me-1"></i>KES {{ number_format($user->wallet->pending_balance, 2) }} pending
            </div>
            @endif
            @if(($user->wallet?->escrow_balance ?? 0) > 0)
            <div style="color:var(--muted);font-size:.82rem">
                <i class="fas fa-lock me-1"></i>KES {{ number_format($user->wallet->escrow_balance, 2) }} in escrow
            </div>
            @endif
        </div>
        <div class="col-md-6">
            {{-- Payout request form --}}
            <div class="p-3 rounded-3" style="background:rgba(0,0,0,.3)">
                <div style="color:var(--text);font-weight:600;font-size:.9rem;margin-bottom:.75rem"><i class="fas fa-money-bill-wave me-2 text-amber"></i>Request Payout</div>
                <form method="POST" action="{{ route('wallet.payout') }}">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label">Amount (KES)</label>
                        <input type="number" name="amount" class="form-control form-control-sm @error('amount') is-invalid @enderror"
                            min="500" max="{{ $user->wallet?->balance ?? 0 }}" step="100" placeholder="Min KES 500">
                        @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-2">
                        <label class="form-label">M-Pesa Number</label>
                        <input type="text" name="mpesa_number" class="form-control form-control-sm @error('mpesa_number') is-invalid @enderror"
                            placeholder="07XXXXXXXX" value="{{ old('mpesa_number', $user->phone) }}">
                        @error('mpesa_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Account Name</label>
                        <input type="text" name="account_name" class="form-control form-control-sm"
                            value="{{ old('account_name', $user->name) }}">
                    </div>
                    <button type="submit" class="btn btn-amber btn-sm w-100">
                        <i class="fas fa-paper-plane me-1"></i>Submit Payout Request
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Transactions --}}
    <div class="col-lg-8">
        <div class="p-3 rounded-3" style="background:var(--surface);border:1px solid var(--border)">
            <h6 style="color:var(--text);font-weight:600;margin-bottom:1rem"><i class="fas fa-history me-2 text-amber"></i>Transaction History</h6>

            @if($transactions->isEmpty())
            <p style="color:var(--muted);font-size:.9rem">No transactions yet.</p>
            @else
            @foreach($transactions as $tx)
            @php $isCredit = in_array($tx->type, ['booking_payment','referral_bonus','wallet_topup','payout_reversal']); @endphp
            <div class="tx-row {{ $isCredit ? 'tx-credit' : 'tx-debit' }}">
                <div class="tx-icon">
                    <i class="fas {{ $isCredit ? 'fa-arrow-down' : 'fa-arrow-up' }}"></i>
                </div>
                <div class="flex-grow-1">
                    <div style="color:var(--text);font-size:.875rem;font-weight:500">{{ $tx->description }}</div>
                    <div style="color:var(--muted);font-size:.75rem">
                        {{ $tx->created_at->format('d M Y · H:i') }}
                        @if($tx->booking_id) &nbsp;· <span style="font-family:monospace">Booking #{{ $tx->booking_id }}</span>@endif
                    </div>
                </div>
                <div class="{{ $isCredit ? 'tx-amount-credit' : 'tx-amount-debit' }}">
                    {{ $isCredit ? '+' : '−' }} KES {{ number_format($tx->amount, 2) }}
                </div>
            </div>
            @endforeach
            <div class="mt-3">{{ $transactions->links('pagination::bootstrap-5') }}</div>
            @endif
        </div>
    </div>

    {{-- Recent Payouts --}}
    <div class="col-lg-4">
        <div class="p-3 rounded-3" style="background:var(--surface);border:1px solid var(--border)">
            <h6 style="color:var(--text);font-weight:600;margin-bottom:1rem"><i class="fas fa-money-bill me-2 text-amber"></i>Recent Payouts</h6>
            @if($payouts->isEmpty())
            <p style="color:var(--muted);font-size:.85rem">No payout requests yet.</p>
            @else
            @foreach($payouts as $payout)
            <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom:1px solid var(--border)">
                <div>
                    <div style="color:var(--text);font-size:.85rem;font-weight:600">KES {{ number_format($payout->amount) }}</div>
                    <div style="color:var(--muted);font-size:.75rem">{{ $payout->created_at->format('d M Y') }}</div>
                </div>
                @if($payout->status === 'approved')
                    <span style="color:var(--green);font-size:.75rem"><i class="fas fa-check-circle me-1"></i>Paid</span>
                @elseif($payout->status === 'pending')
                    <span style="color:var(--amber);font-size:.75rem"><i class="fas fa-clock me-1"></i>Pending</span>
                @else
                    <span style="color:var(--danger);font-size:.75rem"><i class="fas fa-times me-1"></i>Rejected</span>
                @endif
            </div>
            @endforeach
            @endif
        </div>
    </div>
</div>
@endsection
