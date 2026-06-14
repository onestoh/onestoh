@extends('layouts.app')

@section('title', 'Verification Pending')

@section('content')
<div class="d-flex align-items-center justify-content-center py-5" style="min-height:70vh">
    <div class="text-center" style="max-width:520px">
        <div class="mb-4" style="font-size:4rem;color:var(--amber)">
            <i class="fas fa-shield-alt"></i>
        </div>
        <h3 style="color:var(--text);font-weight:700">Account Verification Pending</h3>
        <p style="color:var(--muted);line-height:1.7" class="my-3">
            Your account is currently under review. Our team checks all submitted documents within 24–48 hours.
            You will receive an email notification once your verification is complete.
        </p>

        @auth
        <div class="p-3 rounded-3 mb-4 d-inline-block" style="background:var(--surface);border:1px solid var(--border)">
            <span style="color:var(--muted);font-size:.85rem">Current Status:</span>
            @php $status = auth()->user()->status @endphp
            @if($status === 'pending')
            <span class="ms-2 badge" style="background:rgba(232,146,42,.15);color:var(--amber)"><i class="fas fa-clock me-1"></i>Pending</span>
            @elseif($status === 'rejected')
            <span class="ms-2 badge" style="background:rgba(220,53,69,.15);color:#f87171"><i class="fas fa-times-circle me-1"></i>Rejected</span>
            @else
            <span class="ms-2 badge" style="background:rgba(112,136,168,.15);color:var(--muted)">{{ ucfirst($status) }}</span>
            @endif
        </div>

        <div class="d-flex gap-3 justify-content-center flex-wrap mt-2">
            <a href="{{ route('kyc.index') }}" class="btn btn-amber px-4">
                <i class="fas fa-id-card me-2"></i>Manage Documents
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn px-4" style="border:1px solid var(--border);color:var(--muted)">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </button>
            </form>
        </div>
        @endauth
    </div>
</div>
@endsection
