@extends('layouts.app')

@section('title', 'Verification Pending')

@section('content')
<div class="d-flex align-items-center justify-content-center py-5" style="min-height:70vh">
    <div class="text-center" style="max-width:520px">
        <div class="mb-4" style="font-size:4rem">
            <i class="fas fa-clock" style="color:var(--amber)"></i>
        </div>
        <h3 style="color:var(--text);font-weight:700">Account Verification Pending</h3>
        <p style="color:var(--muted);font-size:1rem;line-height:1.7" class="mb-4">
            Thank you for submitting your documents. Our team reviews all submissions within <strong style="color:var(--text)">1–2 business days</strong>.
            You'll receive a notification once your account has been verified.
        </p>

        <div class="p-4 rounded-3 mb-4 text-start" style="background:var(--surface);border:1px solid var(--border)">
            <h6 style="color:var(--amber);font-weight:600" class="mb-3">Verification Process</h6>
            <div class="d-flex flex-column gap-3">
                @foreach(['Submit required KYC documents' => 'check', 'Admin reviews documents (1–2 business days)' => 'clock', 'Account verified — full access unlocked' => 'lock-open'] as $step => $icon)
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-{{ $icon }} fa-sm" style="color:var(--amber);width:16px"></i>
                    <span style="color:var(--text);font-size:.9rem">{{ $step }}</span>
                </div>
                @endforeach
            </div>
        </div>

        @php $kycStatus = $user->kycDocuments->first()?->status ?? 'not_submitted' @endphp
        <div class="mb-4">
            <span style="color:var(--muted)">Current Status: </span>
            @if($kycStatus === 'pending')
            <span class="badge" style="background:rgba(232,146,42,.15);color:var(--amber)">Under Review</span>
            @elseif($kycStatus === 'not_submitted')
            <span class="badge" style="background:rgba(112,136,168,.1);color:var(--muted)">Documents Not Submitted</span>
            @endif
        </div>

        <div class="d-flex gap-2 justify-content-center flex-wrap">
            <a href="{{ route('kyc.index') }}" class="btn" style="background:var(--amber);color:#000;font-weight:600">
                <i class="fas fa-upload me-2"></i>Complete KYC
            </a>
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn" style="border:1px solid var(--border);color:var(--muted)">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
