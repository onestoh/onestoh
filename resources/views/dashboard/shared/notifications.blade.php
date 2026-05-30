@extends('layouts.dashboard')
@section('title', 'Notifications — EstateYard')
@section('page-title', 'All Notifications')
@section('page-subtitle', 'Stay updated with platform activity')
@section('sidebar-nav')
<div class="dash-nav-section">
  <a href="{{ url('/dashboard') }}" class="dash-nav-item"><span class="dash-nav-icon">←</span> Back to Dashboard</a>
</div>
@endsection
@section('content')
<div class="table-card">
  <div class="table-card-header"><div class="table-card-title">All Notifications</div><button onclick="showToast('All notifications marked as read', 'green')" class="btn btn-sm btn-outline">Mark All Read</button></div>
  <div style="padding:0 24px;">
    @foreach([
      ['💳','Rent payment received','James Kamau paid KSh 95,000 for June 2026','2 min ago',true,'green'],
      ['✅','Verification approved','Your KYC documents have been verified. Badge is now active.','1 hour ago',true,'green'],
      ['🏠','New inspection booking','Peter Njoroge booked an inspection for 4-Bed Villa, Karen on Jun 3.','3 hours ago',false,'blue'],
      ['💰','Referral commission paid','KSh 50,000 commission for deal: Karen Villa Sale has been credited.','Yesterday',false,'gold'],
      ['⚠️','Rent overdue','Tenant Sarah Odhiambo is 2 days overdue on June payment.','2 days ago',false,'red'],
      ['📋','Document received','Tom Opiyo has uploaded the required verification documents.','3 days ago',false,'blue'],
      ['🔨','Auction ended','Auction #24 — Commercial Block, CBD has ended. Winner: Bidder #24.','4 days ago',false,'gold'],
      ['🏦','Loan disbursement','Construction loan drawdown of KSh 50M approved for Kilimani Heights.','1 week ago',false,'green'],
    ] as $n)
    <div style="display:flex; gap:16px; padding:16px 0; border-bottom:1px solid var(--border-dim); background:{{ $n[4] ? 'rgba(212,168,67,0.03)' : 'transparent' }}; cursor:pointer;">
      <div style="width:44px; height:44px; border-radius:50%; background:rgba({{ ['green'=>'46,204,138','blue'=>'74,159,224','gold'=>'212,168,67','red'=>'224,82,82'][$n[5]] }},0.15); display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0;">{{ $n[0] }}</div>
      <div style="flex:1;">
        <div style="font-size:14px; font-weight:{{ $n[4] ? '600' : '400' }}; color:{{ $n[4] ? 'var(--white)' : 'var(--text)' }}; margin-bottom:4px;">{{ $n[1] }}</div>
        <div style="font-size:13px; color:var(--muted);">{{ $n[2] }}</div>
      </div>
      <div style="font-size:12px; font-family:var(--font-mono); color:var(--muted); white-space:nowrap; flex-shrink:0;">{{ $n[3] }}</div>
      @if($n[4])<div style="width:8px; height:8px; border-radius:50%; background:var(--gold); flex-shrink:0; margin-top:4px;"></div>@endif
    </div>
    @endforeach
  </div>
</div>
@endsection
