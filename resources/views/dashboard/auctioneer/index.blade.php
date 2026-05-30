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

<!-- KPIs -->
<div class="kpi-grid">
  @foreach([
    ['🔴','3','Live Auctions','2h 14m avg. remaining','up'],
    ['⏰','8','Upcoming','Next: 2 days','up'],
    ['👥','1,247','Total Bidders','All active auctions','up'],
    ['💰','KSh 284K','Revenue This Month','Auction fees earned','up'],
    ['✅','24','Completed Auctions','This year','up'],
    ['🔒','$4.2M','Escrow Triggered','Awaiting release','up'],
  ] as $k)
  <div class="kpi-card">
    <div class="kpi-icon">{{ $k[0] }}</div>
    <div class="kpi-value">{{ $k[1] }}</div>
    <div class="kpi-label">{{ $k[2] }}</div>
    <div class="kpi-change {{ $k[4] }}">{{ $k[3] }}</div>
  </div>
  @endforeach
</div>

<!-- LIVE AUCTION MANAGER -->
<div class="auction-live" style="margin-bottom:28px;">
  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
    <div>
      <div class="section-tag" style="color:var(--red);">🔴 LIVE AUCTION IN PROGRESS</div>
      <h2 style="font-size:22px; font-weight:700; color:var(--white); margin-top:4px;">Commercial Block, Nairobi CBD</h2>
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
      <div style="font-family:var(--font-serif); font-size:32px; font-weight:700; color:var(--gold);">$512,000</div>
    </div>
    <div style="text-align:center; background:rgba(0,0,0,0.2); border-radius:10px; padding:16px;">
      <div style="font-size:11px; font-family:var(--font-mono); color:var(--blue); letter-spacing:1px; margin-bottom:6px;">BIDDERS</div>
      <div style="font-family:var(--font-serif); font-size:32px; font-weight:700; color:var(--white);">24 / 31</div>
    </div>
    <div style="text-align:center; background:rgba(0,0,0,0.2); border-radius:10px; padding:16px;">
      <div style="font-size:11px; font-family:var(--font-mono); color:var(--green); letter-spacing:1px; margin-bottom:6px;">RESERVE</div>
      <div style="font-family:var(--font-serif); font-size:24px; font-weight:700; color:var(--green);">✓ MET</div>
    </div>
  </div>

  <!-- Live Bid Feed -->
  <div style="background:rgba(0,0,0,0.3); border-radius:10px; padding:16px; max-height:200px; overflow-y:auto;">
    <div style="font-size:11px; font-family:var(--font-mono); color:var(--red); letter-spacing:2px; margin-bottom:12px;">📡 LIVE BID FEED</div>
    @foreach([
      ['Bidder #24','$512,000','Just now','#2ECC8A'],
      ['Bidder #11','$508,000','1 min ago','#7A90B0'],
      ['Bidder #24','$502,000','2 min ago','#7A90B0'],
      ['Bidder #07','$495,000','4 min ago','#7A90B0'],
      ['Bidder #31','$490,000','6 min ago','#7A90B0'],
    ] as $bid)
    <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid rgba(255,255,255,0.05); font-size:13px;">
      <span style="color:{{ $bid[3] }}; font-weight:600;">{{ $bid[0] }}</span>
      <span style="color:var(--white); font-family:var(--font-serif); font-weight:700;">{{ $bid[1] }}</span>
      <span style="color:var(--muted); font-family:var(--font-mono); font-size:11px;">{{ $bid[2] }}</span>
    </div>
    @endforeach
  </div>
</div>

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
      <div class="table-card-title">⏰ Upcoming Auctions</div>
      <a href="#" class="btn btn-gold btn-sm">+ Create Auction</a>
    </div>
    <table class="data-table">
      <thead><tr><th>Property</th><th>Start</th><th>Reserve</th><th>Status</th></tr></thead>
      <tbody>
        @foreach([
          ['4-Bed Villa, Karen','Jun 5, 10am','$250K','scheduled'],
          ['Office Suite, CBD','Jun 8, 2pm','$420K','scheduled'],
          ['Farmland, Nakuru','Jun 10, 9am','$80K','scheduled'],
          ['Off-Plan x5, Kilimani','Jun 15, 11am','$700K','draft'],
        ] as $ua)
        <tr>
          <td class="td-name">{{ $ua[0] }}</td>
          <td style="font-family:var(--font-mono); font-size:12px;">{{ $ua[1] }}</td>
          <td class="td-price">{{ $ua[2] }}</td>
          <td><span class="status-pill status-{{ $ua[3] === 'draft' ? 'draft' : 'active' }}">{{ strtoupper($ua[3]) }}</span></td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
