@extends('layouts.dashboard')
@section('title', 'Promoter Dashboard — EstateYard')
@section('page-title', 'Promoter Dashboard')
@section('page-subtitle', 'Affiliate Promoter · Referral Earnings & Social Media')

@section('sidebar-nav')
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Overview</div>
  <a href="{{ url('/dashboard/promoter') }}" class="dash-nav-item active"><span class="dash-nav-icon">📲</span> Dashboard</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">📊</span> Analytics</a>
</div>
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Referrals</div>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">🔗</span> My Referral Links</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">🏠</span> Browse Properties</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">📋</span> My Portfolio Page</a>
</div>
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Social Media</div>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">📲</span> Linked Accounts</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">📤</span> Auto-Post</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">📅</span> Post Scheduler</a>
</div>
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Earnings</div>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">💰</span> Earnings History</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">💳</span> Withdraw</a>
</div>
@endsection

@section('content')

<!-- KPIs -->
<div class="kpi-grid">
  @foreach([
    ['👁️','12,847','Total Link Clicks','All time','up'],
    ['🔄','284','Conversions','Inspections + Deals','up'],
    ['⏳','KSh 85,000','Pending Earnings','3 deals in closing','up'],
    ['✅','KSh 580,000','Total Earned','All time earnings','up'],
    ['🏠','24','Properties Promoted','In my portfolio','up'],
    ['💰','KSh 180,000','This Quarter','Apr–Jun 2026','up'],
  ] as $k)
  <div class="kpi-card">
    <div class="kpi-icon">{{ $k[0] }}</div>
    <div class="kpi-value">{{ $k[1] }}</div>
    <div class="kpi-label">{{ $k[2] }}</div>
    <div class="kpi-change {{ $k[4] }}">{{ $k[3] }}</div>
  </div>
  @endforeach
</div>

<!-- PORTFOLIO LINK -->
<div style="background:var(--navy3); border:1px solid var(--border); border-radius:var(--radius); padding:20px; margin-bottom:28px;">
  <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px;">
    <div>
      <div style="font-size:15px; font-weight:600; color:var(--white); margin-bottom:6px;">🌐 Your Portfolio Referral Link</div>
      <div style="font-size:13px; color:var(--muted);">Share this link to show ALL 24 of your promoted properties. Earn on ANY deal from this link within 90 days.</div>
    </div>
    <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
      <div class="referral-link-box" style="min-width:280px;">
        <span class="referral-link-url">estateyard.com/portfolio/amina-hassan?ref=AH2847</span>
        <button class="btn btn-gold btn-sm" data-copy="https://estateyard.com/portfolio/amina-hassan?ref=AH2847" onclick="showToast('Portfolio link copied!', 'green')">Copy Link</button>
      </div>
      <button class="btn btn-outline btn-sm">📱 QR Code</button>
      <button class="btn btn-outline btn-sm">🔗 Short Link</button>
    </div>
  </div>
</div>

<!-- ANALYTICS TABLE -->
<div class="table-card" style="margin-bottom:28px;">
  <div class="table-card-header">
    <div class="table-card-title">📊 Referral Performance by Property</div>
    <select class="form-control" style="width:auto; font-size:12px; padding:6px 12px;"><option>Last 30 Days</option><option>Last 90 Days</option><option>All Time</option></select>
  </div>
  <table class="data-table">
    <thead><tr><th>Property</th><th>Commission</th><th>Clicks</th><th>Inspections</th><th>Deals</th><th>Earnings</th><th>Status</th></tr></thead>
    <tbody>
      @foreach([
        ['🏠 4-Bed Villa, Karen','KSh 50,000',2847,18,2,'KSh 100,000','paid'],
        ['🏢 Westlands Apt','KSh 15,000',1924,12,4,'KSh 60,000','paid'],
        ['🌿 Land, Kitengela','KSh 8,000',987,8,1,'KSh 8,000','paid'],
        ['🏡 3-Bed Home, Kilimani','KSh 25,000',1247,15,3,'KSh 75,000','paid'],
        ['✈️ Airbnb, Mombasa','$120/booking',842,24,0,'KSh 0','active'],
        ['🏗️ Off-Plan, Lavington','KSh 80,000',654,7,1,'KSh 80,000','pending'],
        ['🔨 Auction, CBD','3%',324,4,0,'—','active'],
      ] as $r)
      <tr>
        <td class="td-name">{{ $r[0] }}</td>
        <td style="color:var(--gold);">{{ $r[1] }}</td>
        <td>{{ $r[2] }}</td>
        <td>{{ $r[3] }}</td>
        <td style="color:var(--green); font-weight:600;">{{ $r[4] }}</td>
        <td class="td-price">{{ $r[5] }}</td>
        <td><span class="status-pill status-{{ $r[6] }}">{{ strtoupper($r[6]) }}</span></td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

<!-- SOCIAL + WITHDRAW -->
<div class="grid-2">
  <!-- Social Media Auto-Post -->
  <div style="background:var(--navy3); border:1px solid var(--border-dim); border-radius:var(--radius); padding:24px;">
    <div class="table-card-title" style="margin-bottom:16px;">📲 Social Media Auto-Post</div>
    <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:20px;">
      @foreach([['📘','Facebook','Connected'],['📷','Instagram','Connected'],['🎵','TikTok','Connected'],['🐦','X','Connect'],['💼','LinkedIn','Connect']])
      <div style="background:var(--surface); border:1px solid {{ $sm[2]==='Connected' ? 'rgba(46,204,138,0.3)' : 'var(--border-dim)' }}; border-radius:var(--radius-sm); padding:8px 12px; text-align:center; cursor:pointer;">
        <div style="font-size:20px; margin-bottom:4px;">{{ $sm[0] }}</div>
        <div style="font-size:11px; color:{{ $sm[2]==='Connected' ? 'var(--green)' : 'var(--muted)' }};">{{ $sm[2] }}</div>
      </div>
      @endforeach
    </div>
    <div class="form-group">
      <label class="form-label">Select Listing to Post</label>
      <select class="form-control"><option>4-Bed Villa, Karen — Commission: KSh 50K</option><option>Westlands Apt — Commission: KSh 15K</option></select>
    </div>
    <div class="form-group">
      <label class="form-label">Caption</label>
      <textarea class="form-control" rows="3">🏠 Looking for your dream home? Check out this 4-Bed Villa in Karen! Priced at KSh 28.5M 😍 Full escrow protection & verified. Book inspection via link in bio! #NairobiRealEstate</textarea>
    </div>
    <button onclick="showToast('Posted to all connected platforms!', 'green')" class="btn btn-gold" style="width:100%; justify-content:center;">📤 Post to All Platforms</button>
  </div>

  <!-- Withdrawal Widget -->
  <div style="background:var(--navy3); border:1px solid var(--border-dim); border-radius:var(--radius); padding:24px;">
    <div class="table-card-title" style="margin-bottom:20px;">💳 Withdraw Earnings</div>
    <div style="background:var(--surface); border-radius:12px; padding:20px; text-align:center; margin-bottom:20px;">
      <div style="font-size:12px; font-family:var(--font-mono); color:var(--muted); margin-bottom:8px;">AVAILABLE BALANCE</div>
      <div style="font-family:var(--font-serif); font-size:48px; font-weight:700; color:var(--gold); line-height:1;">KSh 495,000</div>
      <div style="font-size:12px; color:var(--muted); margin-top:6px;">KSh 85,000 pending · Clears in 2-5 days</div>
    </div>
    <div class="form-group">
      <label class="form-label">Withdrawal Method</label>
      <select class="form-control">
        <option>💳 M-Pesa — +254 712 XXX XXX</option>
        <option>🏦 Bank — Equity Bank — **** 4821</option>
      </select>
    </div>
    <div class="form-group">
      <label class="form-label">Amount</label>
      <input type="number" class="form-control" placeholder="Enter amount" value="200000">
    </div>
    <button onclick="showToast('Withdrawal initiated! Funds arriving in 1-3 min via M-Pesa', 'green')" class="btn btn-gold" style="width:100%; justify-content:center; margin-bottom:12px;">💸 Withdraw Now</button>
    <div style="font-size:12px; color:var(--muted); text-align:center;">Platform fee: 2% · Min withdrawal: KSh 1,000</div>

    <div style="margin-top:20px; padding-top:16px; border-top:1px solid var(--border-dim);">
      <div class="section-tag" style="margin-bottom:12px;">Recent Withdrawals</div>
      @foreach([['KSh 150,000','M-Pesa','15 May 2026'],['KSh 200,000','Bank','1 May 2026'],['KSh 100,000','M-Pesa','18 Apr 2026']] as $w)
      <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid var(--border-dim); font-size:13px;">
        <span style="color:var(--white); font-weight:600;">{{ $w[0] }}</span>
        <span style="color:var(--muted);">{{ $w[1] }}</span>
        <span style="color:var(--green); font-family:var(--font-mono); font-size:11px;">{{ $w[2] }}</span>
      </div>
      @endforeach
    </div>
  </div>
</div>

@endsection
