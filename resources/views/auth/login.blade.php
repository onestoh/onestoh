@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="d-flex align-items-center justify-content-center py-5" style="min-height:70vh">
    <div class="w-100" style="max-width:440px">
        <div class="p-4 rounded-4" style="background:var(--surface);border:1px solid var(--border)">
            <div class="text-center mb-4">
                <h4 style="color:var(--text);font-weight:700">Welcome Back</h4>
                <p style="color:var(--muted);font-size:.9rem">Sign in to your account</p>
            </div>

            @if($errors->any())
            <div class="alert alert-danger" style="background:rgba(220,53,69,.1);border-color:rgba(220,53,69,.3);color:#f87171">
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label style="color:var(--muted);font-size:.875rem" class="mb-1">Email Address</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus
                        style="background:var(--dark);border-color:var(--border);color:var(--text)">
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <label style="color:var(--muted);font-size:.875rem">Password</label>
                        @if(Route::has('password.request'))
                        <a href="{{ route('password.request') }}" style="color:var(--amber);font-size:.8rem">Forgot password?</a>
                        @endif
                    </div>
                    <input type="password" name="password" class="form-control" required
                        style="background:var(--dark);border-color:var(--border);color:var(--text)">
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember" style="color:var(--muted);font-size:.875rem">Remember me</label>
                </div>
                <button type="submit" class="btn w-100 py-2" style="background:var(--amber);color:#000;font-weight:700;font-size:1rem">Sign In</button>
            </form>

            <p class="text-center mt-3 mb-0" style="color:var(--muted);font-size:.875rem">
                Don't have an account? <a href="{{ route('register') }}" style="color:var(--amber)">Register here</a>
            </p>
        </div>
    </div>
</div>
@endsection
