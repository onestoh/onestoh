@extends('layouts.dashboard')
@section('title', 'Auctioneer Dashboard — EstateYard')
@section('page-title', 'Auction Control Center')
@section('page-subtitle', 'Auctioneer · Live Auctions, Bidders & Revenue')

@section('sidebar-nav')
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Overview</div>
  <a href="{{ url('/dashboard/auctioneer') }}" class="dash-nav-item active"><span class="dash-nav-icon">🔨</span> Dashboard</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">📊</span> Analytics</a>
</div>
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Auctions</div>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">🔴</span> Live Auctions <span class="dash-nav-badge">3</span></a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">⏰</span> Upcoming</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">✅</span> Completed</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">➕</span> Create Auction</a>
</div>
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Bidders</div>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">👥</span> Bidder Registry</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">✅</span> KYC Approvals <span class="dash-nav-badge">8</span></a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">💰</span> Deposits</a>
</div>
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Finance</div>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">💵</span> Revenue</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">🔒</span> Escrow Status</a>
</div>
@endsection

@section('content')

@if(session('success'))
<div class="alert alert-green" style="margin-bottom:24px;">{{ session('success') }}</div>
@endif

<!-- KPIs -->
@php
  $liveAuctions     = $auctions->where('status','live');
  $upcomingAuctions = $auctions->where('status','upcoming');
  $totalBids        = $auctions->sum(fn($a) => $a->bids->count());
@endphp
<div class="kpi-grid">
  <div class="kpi-card">
    <div class="kpi-icon">🔴</div>
    <div class="kpi-value">{{ $liveCount }}</div>
    <div class="kpi-label">Live Auctions</div>
    <div class="kpi-change up">Currently active</div>
  </div>
  <div class="kpi-card">
    <div class="kpi-icon">⏰</div>
    <div class="kpi-value">{{ $upcomingCount }}</div>
    <div class="kpi-label">Upcoming Auctions</div>
    <div class="kpi-change up">Scheduled</div>
  </div>
  <div class="kpi-card">
    <div class="kpi-icon">✅</div>
    <div class="kpi-value">{{ $endedCount }}</div>
    <div class="kpi-label">Completed</div>
    <div class="kpi-change up">All time</div>
  </div>
  <div class="kpi-card">
    <div class="kpi-icon">👥</div>
    <div class="kpi-value">{{ $totalBids }}</div>
    <div class="kpi-label">Total Bids</div>
    <div class="kpi-change up">Across all auctions</div>
  </div>
  <div class="kpi-card">
    <div class="kpi-icon">🏠</div>
    <div class="kpi-value">{{ $auctions->count() }}</div>
    <div class="kpi-label">Total Auctions</div>
    <div class="kpi-change up">All time</div>
  </div>
</div>

<!-- LIVE AUCTION MANAGER -->
@php $firstLive = $liveAuctions->first(); @endphp
@if($firstLive)
<div class="auction-live" style="margin-bottom:28px;">
  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
    <div>
      <div class="section-tag" style="color:var(--red);">🔴 LIVE AUCTION IN PROGRESS</div>
      <h2 style="font-size:22px; font-weight:700; color:var(--white); margin-top:4px;">{{ $firstLive->property->title ?? 'Live Auction' }}</h2>
    </div>
    <div style="display:flex; gap:8px;">
      <button onclick="showToast('Auction extended by 5 minutes (anti-sniping)', 'gold')" class="btn btn-sm btn-outline">⏱ Extend +5min</button>
      <button onclick="showToast('Auction paused', 'blue')" class="btn btn-sm btn-outline">⏸ Pause</button>
      <button onclick="if(confirm('Close auction now?')){showToast('Auction closed. Winner: Bidder #24 — $512,000', 'green')}" class="btn btn-sm btn-danger">🔨 Close</button>
    </div>
  </div>

  <div style="display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:16px; margin-bottom:24px;">
    <div style="text-align:center; background:rgba(0,0,0,0.2); border-radius:10px; padding:16px;">
      <div style="font-size:11px; font-family:var(--font-mono); color:var(--red); letter-spacing:1px; margin-bottom:6px;">ENDS IN</div>
      <div class="countdown-timer" data-countdown="{{ time() + 8040 }}" style="justify-content:center; gap:8px;">
        <div class="countdown-unit"><span class="countdown-num" style="font-size:32px;">02</span><span class="countdown-label">HRS</span></div>
        <div class="countdown-unit"><span class="countdown-num" style="font-size:32px;">14</span><span class="countdown-label">MIN</span></div>
        <div class="countdown-unit"><span class="countdown-num" style="font-size:32px;">00</span><span class="countdown-label">SEC</span></div>
      </div>
    </div>
    <div style="text-align:center; background:rgba(0,0,0,0.2); border-radius:10px; padding:16px;">
      <div style="font-size:11px; font-family:var(--font-mono); color:var(--gold); letter-spacing:1px; margin-bottom:6px;">CURRENT BID</div>
      <div style="font-family:var(--font-serif); font-size:32px; font-weight:700; color:var(--gold);">KSh {{ number_format($firstLive->current_bid ?? $firstLive->starting_bid ?? 0) }}</div>
    </div>
    <div style="text-align:center; background:rgba(0,0,0,0.2); border-radius:10px; padding:16px;">
      <div style="font-size:11px; font-family:var(--font-mono); color:var(--blue); letter-spacing:1px; margin-bottom:6px;">BIDS PLACED</div>
      <div style="font-family:var(--font-serif); font-size:32px; font-weight:700; color:var(--white);">{{ $firstLive->bids->count() }}</div>
    </div>
    <div style="text-align:center; background:rgba(0,0,0,0.2); border-radius:10px; padding:16px;">
      <div style="font-size:11px; font-family:var(--font-mono); color:var(--green); letter-spacing:1px; margin-bottom:6px;">RESERVE</div>
      <div style="font-family:var(--font-serif); font-size:24px; font-weight:700; color:var(--green);">
        {{ ($firstLive->current_bid ?? 0) >= ($firstLive->reserve_price ?? PHP_INT_MAX) ? '✓ MET' : '✗ NOT MET' }}
      </div>
    </div>
  </div>

  <!-- Live Bid Feed -->
  <div style="background:rgba(0,0,0,0.3); border-radius:10px; padding:16px; max-height:200px; overflow-y:auto;">
    <div style="font-size:11px; font-family:var(--font-mono); color:var(--red); letter-spacing:2px; margin-bottom:12px;">📡 LIVE BID FEED</div>
    @forelse($firstLive->bids->sortByDesc('created_at')->take(10) as $bid)
    <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid rgba(255,255,255,0.05); font-size:13px;">
      <span style="color:{{ $bid->is_winning ? '#2ECC8A' : '#7A90B0' }}; font-weight:600;">Bidder #{{ $bid->bidder_id }}</span>
      <span style="color:var(--white); font-family:var(--font-serif); font-weight:700;">KSh {{ number_format($bid->amount) }}</span>
      <span style="color:var(--muted); font-family:var(--font-mono); font-size:11px;">{{ $bid->created_at->diffForHumans() }}</span>
    </div>
    @empty
    <div style="color:var(--muted); font-size:13px;">No bids yet.</div>
    @endforelse
  </div>
</div>
@endif

<!-- BIDDER KYC + UPCOMING -->
<div class="grid-2">
  <!-- KYC Approvals -->
  <div class="table-card">
    <div class="table-card-header">
      <div class="table-card-title">✅ Bidder KYC Queue</div>
      <span class="badge badge-orange">8 Pending</span>
    </div>
    <table class="data-table">
      <thead><tr><th>Bidder</th><th>Deposit</th><th>Docs</th><th>Action</th></tr></thead>
      <tbody>
        @foreach([['John M.','$5,000','Complete'],['Alice K.','$5,000','Complete'],['Peter O.','$5,000','Partial'],['Sarah W.','$5,000','Complete'],['Tom N.','$5,000','Complete']] as $b)
        <tr>
          <td class="td-name">{{ $b[0] }}</td>
          <td class="td-price">{{ $b[1] }}</td>
          <td><span class="status-pill status-{{ $b[2]==='Complete' ? 'active' : 'review' }}">{{ $b[2] }}</span></td>
          <td>
            <div style="display:flex; gap:6px;">
              <button onclick="showToast('Bidder approved!', 'green')" class="btn btn-sm" style="background:rgba(46,204,138,0.15); border:1px solid rgba(46,204,138,0.3); color:var(--green);">✓</button>
              <button class="btn btn-sm btn-danger">✗</button>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <!-- Upcoming Auctions -->
  <div class="table-card">
    <div class="table-card-header">
      <div class="table-card-title">⏰ All Auctions</div>
      <a href="#" class="btn btn-gold btn-sm">+ Create Auction</a>
    </div>
    <table class="data-table">
      <thead><tr><th>Property</th><th>Starts</th><th>Reserve</th><th>Current Bid</th><th>Bids</th><th>Status</th></tr></thead>
      <tbody>
        @forelse($auctions as $a)
        <tr>
          <td class="td-name">{{ $a->property->title ?? 'N/A' }}</td>
          <td style="font-family:var(--font-mono); font-size:12px;">{{ $a->start_time ? \Carbon\Carbon::parse($a->start_time)->format('d M, H:i') : '—' }}</td>
          <td class="td-price">KSh {{ number_format($a->reserve_price ?? 0) }}</td>
          <td class="td-price">{{ $a->current_bid ? 'KSh '.number_format($a->current_bid) : '—' }}</td>
          <td>{{ $a->bids->count() }}</td>
          <td><span class="status-pill status-{{ $a->status === 'live' ? 'active' : ($a->status === 'ended' ? 'sold' : 'draft') }}">{{ strtoupper($a->status) }}</span></td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align:center; color:var(--muted); padding:24px;">No auctions yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
