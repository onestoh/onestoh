@extends('layouts.app')

@section('title', 'Create Account')

@section('content')
<div class="d-flex align-items-center justify-content-center py-5" style="min-height:70vh">
    <div class="w-100" style="max-width:520px">
        <div class="p-4 rounded-4" style="background:var(--surface);border:1px solid var(--border)" x-data="{ role: '{{ old('role', 'client') }}' }">
            <div class="text-center mb-4">
                <h4 style="color:var(--text);font-weight:700">Create an Account</h4>
                <p style="color:var(--muted);font-size:.9rem">Join TheOnlineYard today</p>
            </div>

            @if($errors->any())
            <div class="alert" style="background:rgba(220,53,69,.1);border-color:rgba(220,53,69,.3);color:#f87171">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-12">
                        <label style="color:var(--muted);font-size:.875rem" class="mb-1">Full Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required
                            style="background:var(--dark);border-color:var(--border);color:var(--text)">
                    </div>
                    <div class="col-md-6">
                        <label style="color:var(--muted);font-size:.875rem" class="mb-1">Email Address</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required
                            style="background:var(--dark);border-color:var(--border);color:var(--text)">
                    </div>
                    <div class="col-md-6">
                        <label style="color:var(--muted);font-size:.875rem" class="mb-1">Phone Number</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required placeholder="+254..."
                            style="background:var(--dark);border-color:var(--border);color:var(--text)">
                    </div>
                    <div class="col-12">
                        <label style="color:var(--muted);font-size:.875rem" class="mb-1">Account Type</label>
                        <select name="role" class="form-select" x-model="role" required
                            style="background:var(--dark);border-color:var(--border);color:var(--text)">
                            <option value="client">Client — I want to rent or buy</option>
                            <option value="yard_owner">Yard Owner — I manage a fleet/yard</option>
                            <option value="individual_owner">Individual Owner — I own assets personally</option>
                            <option value="broker">Broker — I connect clients and owners</option>
                            <option value="operator">Operator — I drive/operate equipment</option>
                        </select>
                    </div>
                    <div class="col-12" x-show="role === 'broker'" x-transition>
                        <label style="color:var(--muted);font-size:.875rem" class="mb-1">Referral Code <span style="color:var(--muted)">(optional)</span></label>
                        <input type="text" name="referral_code" class="form-control" value="{{ old('referral_code') }}"
                            style="background:var(--dark);border-color:var(--border);color:var(--text)">
                    </div>
                    <div class="col-md-6">
                        <label style="color:var(--muted);font-size:.875rem" class="mb-1">Password</label>
                        <input type="password" name="password" class="form-control" required
                            style="background:var(--dark);border-color:var(--border);color:var(--text)">
                    </div>
                    <div class="col-md-6">
                        <label style="color:var(--muted);font-size:.875rem" class="mb-1">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required
                            style="background:var(--dark);border-color:var(--border);color:var(--text)">
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="terms" id="terms" required {{ old('terms') ? 'checked' : '' }}>
                            <label class="form-check-label" for="terms" style="color:var(--muted);font-size:.875rem">
                                I agree to the <a href="#" style="color:var(--amber)">Terms of Service</a> and <a href="#" style="color:var(--amber)">Privacy Policy</a>
                            </label>
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn w-100 py-2" style="background:var(--amber);color:#000;font-weight:700;font-size:1rem">Create Account</button>
                    </div>
                </div>
            </form>

            <p class="text-center mt-3 mb-0" style="color:var(--muted);font-size:.875rem">
                Already have an account? <a href="{{ route('login') }}" style="color:var(--amber)">Sign in</a>
            </p>
        </div>
    </div>
</div>
@endsection
