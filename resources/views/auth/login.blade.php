@extends('layouts.app')
@section('title', 'Sign In — EstateYard')
@section('content')
<div class="auth-page" style="padding-top:100px;">
  <div class="auth-card">
    <div class="auth-logo">
      <div class="brand">Estate<span>Yard</span></div>
    </div>
    <div class="auth-title">Welcome back</div>
    <div class="auth-sub">Sign in to your EstateYard account</div>

    <form action="{{ url('/login') }}" method="POST" data-validate>
      @csrf
      <div class="form-group">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control" placeholder="you@example.com" required>
      </div>
      <div class="form-group">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
      </div>
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
        <label style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--muted); cursor:pointer;">
          <input type="checkbox" name="remember" style="accent-color:var(--gold);"> Remember me
        </label>
        <a href="#" style="font-size:13px;">Forgot password?</a>
      </div>
      <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center; font-size:15px; padding:14px;">Sign In</button>
    </form>

    <div class="auth-divider"><span>OR DEMO AS</span></div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
      @foreach([['🏛️','Admin','/dashboard/admin'],['🏠','Landlord','/dashboard/landlord'],['📋','Broker','/dashboard/broker'],['🔑','Tenant','/dashboard/tenant'],['🔨','Auctioneer','/dashboard/auctioneer'],['📊','Investor','/dashboard/investor']] as $d)
      <a href="{{ url($d[2]) }}" style="display:flex; align-items:center; gap:8px; background:var(--navy3); border:1px solid var(--border-dim); border-radius:var(--radius-sm); padding:10px 14px; color:var(--muted); font-size:13px; transition:all .2s; text-decoration:none;" onmouseover="this.style.borderColor='var(--gold)';this.style.color='var(--gold)'" onmouseout="this.style.borderColor='var(--border-dim)';this.style.color='var(--muted)'">
        <span>{{ $d[0] }}</span> {{ $d[1] }}
      </a>
      @endforeach
    </div>

    <p style="text-align:center; margin-top:24px; font-size:14px; color:var(--muted);">
      Don't have an account? <a href="{{ url('/register') }}">Create one free</a>
    </p>
  </div>
</div>
@endsection
