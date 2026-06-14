@extends('layouts.dashboard')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@push('styles')
<style>
.form-section { background: var(--black); border: 1px solid var(--border); border-radius: 10px; padding: 1.5rem; margin-bottom: 1.25rem; }
.form-section h6 { color: var(--amber); font-size: .75rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 1rem; padding-bottom: .5rem; border-bottom: 1px solid var(--border); }
.form-control { background: var(--surface); border: 1px solid var(--border); color: var(--text); }
.form-control:focus { background: var(--surface); border-color: var(--amber); color: var(--text); box-shadow: 0 0 0 2px rgba(232,146,42,.15); }
.form-label { color: var(--muted); font-size: .82rem; margin-bottom: .3rem; }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <h4 class="mb-4" style="color:var(--text);font-weight:700">My Profile</h4>

        {{-- Avatar + summary --}}
        <div class="form-section d-flex align-items-center gap-4 mb-4">
            @if($user->avatar)
                <img src="{{ Storage::url($user->avatar) }}" class="rounded-circle" width="72" height="72" style="object-fit:cover">
            @else
                <div style="width:72px;height:72px;border-radius:50%;background:rgba(232,146,42,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i class="fas fa-user-circle text-amber fa-2x"></i>
                </div>
            @endif
            <div>
                <div style="color:var(--text);font-weight:700;font-size:1.1rem">{{ $user->name }}</div>
                <div style="color:var(--muted);font-size:.85rem">{{ $user->email }}</div>
                <div class="mt-1">
                    <span class="badge" style="background:rgba(232,146,42,.15);color:var(--amber);border:1px solid rgba(232,146,42,.3)">
                        {{ ucwords(str_replace('_', ' ', $user->role)) }}
                    </span>
                    <span class="badge ms-1 {{ $user->status === 'verified' ? 'text-success' : '' }}" style="background:rgba(255,255,255,.05)">
                        {{ ucfirst($user->status) }}
                    </span>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf @method('PUT')

            <div class="form-section">
                <h6><i class="fas fa-user me-1"></i>Personal Information</h6>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control" value="{{ $user->email }}" disabled style="opacity:.6">
                        <div style="color:var(--muted);font-size:.72rem;margin-top:.2rem">Email cannot be changed</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}" placeholder="+254...">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Profile Photo</label>
                        <input type="file" name="avatar" class="form-control" accept="image/*">
                        <div style="color:var(--muted);font-size:.72rem;margin-top:.2rem">JPG/PNG · max 2MB</div>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h6><i class="fas fa-lock me-1"></i>Change Password</h6>
                <div style="color:var(--muted);font-size:.82rem;margin-bottom:1rem">Leave blank to keep your current password.</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">New Password</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Min 8 characters">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat password">
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3 justify-content-end">
                <button type="submit" class="btn btn-amber px-5"><i class="fas fa-save me-2"></i>Save Profile</button>
            </div>
        </form>

        {{-- Referral Info --}}
        @if($user->referral_code)
        <div class="mt-4 p-3 rounded-3" style="background:var(--surface);border:1px solid var(--border)">
            <div style="color:var(--muted);font-size:.78rem;text-transform:uppercase;letter-spacing:1px">Your Referral Code</div>
            <div style="color:var(--amber);font-size:1.2rem;font-weight:700;font-family:monospace;letter-spacing:2px;margin:.25rem 0">{{ $user->referral_code }}</div>
            <div style="color:var(--muted);font-size:.8rem">Share to earn referral bonuses when friends join</div>
        </div>
        @endif
    </div>
</div>
@endsection
