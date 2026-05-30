@extends('layouts.dashboard')
@section('title', 'Verification — EstateYard')
@section('page-title', 'Verification Badge')
@section('page-subtitle', 'Manage your EstateYard verification status')
@section('sidebar-nav')
<div class="dash-nav-section">
  <a href="{{ url('/dashboard') }}" class="dash-nav-item"><span class="dash-nav-icon">←</span> Back to Dashboard</a>
</div>
@endsection
@section('content')
<div class="alert alert-green" style="margin-bottom:28px; font-size:14px;">✅ Your verification badge is active and expires on December 31, 2026. Your account is fully visible on the marketplace.</div>
<div style="display:grid; grid-template-columns:1fr 1fr; gap:24px; max-width:800px;">
  <div style="background:var(--navy3); border:2px solid var(--gold); border-radius:var(--radius); padding:28px; text-align:center;">
    <div style="font-size:48px; margin-bottom:12px;">✅</div>
    <div style="font-size:18px; font-weight:600; color:var(--white); margin-bottom:6px;">Verified Landlord</div>
    <div class="verified-badge" style="margin:0 auto 16px; width:fit-content;">BADGE ACTIVE</div>
    <div style="font-size:13px; color:var(--muted); margin-bottom:16px;">Valid until: December 31, 2026<br>Plan: 1 Year — $249</div>
    <div class="progress"><div class="progress-bar green" style="width:58%;"></div></div>
    <div style="font-size:11px; color:var(--muted); margin-top:6px; font-family:var(--font-mono);">7 months remaining</div>
  </div>
  <div style="background:var(--navy3); border:1px solid var(--border-dim); border-radius:var(--radius); padding:28px;">
    <div class="table-card-title" style="margin-bottom:16px;">📋 Submitted Documents</div>
    @foreach([['🪪','National ID / Passport','Verified','green'],['📋','Proof of Property Ownership','Verified','green'],['💼','Bank Statement','Verified','green'],['📸','Profile Photo','Verified','green'],['📝','Platform Agreement','Signed','green']] as $doc)
    <div style="display:flex; align-items:center; gap:10px; padding:10px 0; border-bottom:1px solid var(--border-dim);">
      <span>{{ $doc[0] }}</span>
      <span style="font-size:13px; color:var(--text); flex:1;">{{ $doc[1] }}</span>
      <span class="status-pill status-active">{{ $doc[2] }}</span>
    </div>
    @endforeach
  </div>
</div>
<div style="margin-top:28px; max-width:800px;">
  <div class="pricing-grid">
    @foreach([['3 Months','$79','/qtr',false],['6 Months','$149','/6mo',true],['1 Year','$249','/yr',false]] as $plan)
    <div class="pricing-card {{ $plan[3] ? 'popular' : '' }}" style="text-align:center; padding:20px;">
      @if($plan[3])<div class="pricing-popular-tag">BEST VALUE</div>@endif
      <div class="pricing-title">Renew {{ $plan[0] }}</div>
      <div class="pricing-price">{{ $plan[1] }}<sub>{{ $plan[2] }}</sub></div>
      <button onclick="showToast('Renewal initiated! Badge will be extended.', 'green')" class="btn btn-{{ $plan[3] ? 'gold' : 'outline' }} btn-sm" style="width:100%; justify-content:center; margin-top:16px;">Renew</button>
    </div>
    @endforeach
  </div>
</div>
@endsection
