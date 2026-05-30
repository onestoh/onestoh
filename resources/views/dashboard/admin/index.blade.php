@extends('layouts.dashboard')
@section('title', 'Super Admin — EstateYard')
@section('page-title', 'Platform Dashboard')
@section('page-subtitle', 'Super Admin · Full Platform Overview')

@section('sidebar-nav')
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Overview</div>
  <a href="{{ url('/dashboard/admin') }}" class="dash-nav-item active"><span class="dash-nav-icon">🏛️</span> Dashboard</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">📊</span> Analytics</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">📈</span> Revenue</a>
</div>
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Users</div>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">👥</span> All Users <span class="dash-nav-badge">24</span></a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">✅</span> KYC Queue <span class="dash-nav-badge">7</span></a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">🏷️</span> Verification Badges</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">🚫</span> Suspended</a>
</div>
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Marketplace</div>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">🏠</span> All Listings</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">⏳</span> Pending Review <span class="dash-nav-badge">12</span></a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">🔍</span> Duplicate Detection</a>
</div>
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Finance</div>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">🔒</span> Escrow Accounts</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">💰</span> Commissions</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">⚖️</span> Disputes <span class="dash-nav-badge">3</span></a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">🏦</span> Bank Partners</a>
</div>
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Platform</div>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">🔨</span> Auctions</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">⚙️</span> Platform Config</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">📡</span> System Health</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">📝</span> Audit Logs</a>
</div>
@endsection

@section('content')

<!-- KPI GRID -->
<div class="kpi-grid">
  @foreach([
    ['💰','$12.4M','Total GMV','↑ 23% this month','up'],
    ['👥','50,247','Total Users','↑ 1,842 this week','up'],
    ['🏠','24,183','Active Listings','↑ 312 today','up'],
    ['📊','$184,200','Monthly Revenue','↑ 18% vs last month','up'],
    ['⏳','47','Pending KYC','7 urgent','down'],
    ['⚖️','8','Open Disputes','3 high priority','down'],
    ['🏷️','2,841','Verified Users','92% renewal rate','up'],
    ['🔒','$3.2M','Escrow Held','14 active transactions','up'],
  ] as $k)
  <div class="kpi-card">
    <div class="kpi-icon">{{ $k[0] }}</div>
    <div class="kpi-value">{{ $k[1] }}</div>
    <div class="kpi-label">{{ $k[2] }}</div>
    <div class="kpi-change {{ $k[4] }}">{{ $k[4] === 'up' ? '↑' : '↓' }} {{ $k[3] }}</div>
  </div>
  @endforeach
</div>

<!-- CHARTS ROW -->
<div class="grid-2" style="margin-bottom:28px;">
  <!-- Revenue Chart -->
  <div class="chart-container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
      <div>
        <div class="section-tag" style="margin-bottom:4px;">Revenue</div>
        <div style="font-size:16px; font-weight:600; color:var(--white);">Monthly Revenue Trend</div>
      </div>
      <select class="form-control" style="width:auto; font-size:12px; padding:6px 12px;">
        <option>Last 12 Months</option><option>Last 6 Months</option><option>This Year</option>
      </select>
    </div>
    <div class="chart-placeholder">
      @foreach([40,60,45,75,85,70,90,95,80,88,92,100] as $h)
      <div class="chart-bar {{ $h >= 90 ? 'highlight' : '' }}" style="height:{{ $h }}%;" title="Month {{ $loop->index + 1 }}"></div>
      @endforeach
    </div>
    <div style="display:flex; justify-content:space-between; margin-top:8px;">
      @foreach(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'])
      <div style="font-size:9px; font-family:var(--font-mono); color:var(--muted); text-align:center; flex:1;">{{ $m }}</div>
      @endforeach
    </div>
  </div>

  <!-- User Growth -->
  <div class="chart-container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
      <div>
        <div class="section-tag" style="margin-bottom:4px;">Users</div>
        <div style="font-size:16px; font-weight:600; color:var(--white);">New User Registrations</div>
      </div>
      <div style="display:flex; gap:12px;">
        <span style="font-size:12px; color:var(--green);">● Verified</span>
        <span style="font-size:12px; color:var(--blue);">● Registered</span>
      </div>
    </div>
    <div class="chart-placeholder">
      @foreach([30,50,40,65,70,60,80,85,72,78,88,95] as $h)
      <div class="chart-bar" style="height:{{ $h }}%; background:{{ $h >= 80 ? 'rgba(46,204,138,0.3)' : 'rgba(74,159,224,0.2)' }}; border-color:{{ $h >= 80 ? 'rgba(46,204,138,0.5)' : 'rgba(74,159,224,0.3)' }};"></div>
      @endforeach
    </div>
  </div>
</div>

<!-- KYC QUEUE -->
<div class="table-card" style="margin-bottom:28px;">
  <div class="table-card-header">
    <div class="table-card-title">⏳ KYC Verification Queue</div>
    <div style="display:flex; gap:8px;">
      <input type="text" class="form-control" placeholder="Search..." style="width:200px; font-size:13px; padding:8px 14px;">
      <a href="#" class="btn btn-gold btn-sm">Process All</a>
    </div>
  </div>
  <table class="data-table">
    <thead><tr><th>User</th><th>Role</th><th>Submitted</th><th>Documents</th><th>Risk</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
      @foreach([
        ['James Kamau','Landlord','2 hours ago','3/3 docs','Low','pending'],
        ['Sarah Odhiambo','Licensed Broker','5 hours ago','4/4 docs','Low','pending'],
        ['David Waweru','Developer','Yesterday','5/5 docs','Medium','review'],
        ['Amina Hassan','Promoter','Yesterday','2/2 docs','Low','pending'],
        ['Peter Njoroge','Valuer','2 days ago','3/4 docs','Medium','review'],
        ['Alice Mwangi','Surveyor','2 days ago','4/4 docs','Low','pending'],
        ['Tom Opiyo','Auctioneer','3 days ago','5/5 docs','Low','review'],
      ] as $kyc)
      <tr>
        <td><div class="td-name" style="display:flex; align-items:center; gap:8px;"><div style="width:32px; height:32px; border-radius:50%; background:var(--gold-dim); display:flex; align-items:center; justify-content:center; font-size:14px; flex-shrink:0;">👤</div>{{ $kyc[0] }}</div></td>
        <td>{{ $kyc[1] }}</td>
        <td style="font-family:var(--font-mono); font-size:12px;">{{ $kyc[2] }}</td>
        <td><span style="color:var(--green);">{{ $kyc[3] }}</span></td>
        <td><span class="status-pill {{ $kyc[4]==='Low' ? 'status-active' : 'status-review' }}">{{ $kyc[4] }}</span></td>
        <td><span class="status-pill status-{{ $kyc[5] }}">{{ strtoupper($kyc[5]) }}</span></td>
        <td>
          <div style="display:flex; gap:6px;">
            <button onclick="showToast('User approved!', 'green')" class="btn btn-sm" style="background:rgba(46,204,138,0.15); border:1px solid rgba(46,204,138,0.3); color:var(--green);">✓ Approve</button>
            <button onclick="showToast('User rejected.', 'red')" class="btn btn-sm btn-danger">✗ Reject</button>
            <button class="btn btn-sm btn-outline">📋 View Docs</button>
          </div>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

<!-- BOTTOM GRID -->
<div class="grid-2">
  <!-- Recent Escrow Transactions -->
  <div class="table-card">
    <div class="table-card-header">
      <div class="table-card-title">🔒 Escrow Transactions</div>
      <a href="#" style="font-size:13px; color:var(--gold);">View All →</a>
    </div>
    <table class="data-table">
      <thead><tr><th>Property</th><th>Amount</th><th>Status</th></tr></thead>
      <tbody>
        @foreach([['4-Bed Villa, Karen','$285,000','active'],['Office Complex, CBD','$2.1M','active'],['3-Bed Apt, Kilimani','$95,000','closed'],['Land, Kitengela','$18,000','pending'],['Hotel, Mombasa','$1.8M','review']] as $e)
        <tr>
          <td class="td-name">{{ $e[0] }}</td>
          <td class="td-price">{{ $e[1] }}</td>
          <td><span class="status-pill status-{{ $e[2] }}">{{ strtoupper($e[2]) }}</span></td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <!-- System Health -->
  <div style="background:var(--navy3); border:1px solid var(--border-dim); border-radius:var(--radius); padding:24px;">
    <div class="table-card-title" style="margin-bottom:20px;">📡 System Health</div>
    @foreach([['API Response Time','98ms','good'],['Database Load','23%','good'],['Storage Used','67%','warn'],['Payment Gateway','Online','good'],['Email Service','Online','good'],['SMS Service','Online','good'],['Active Sessions','1,847','info'],['Error Rate','0.02%','good']] as $s)
    <div style="display:flex; align-items:center; justify-content:space-between; padding:10px 0; border-bottom:1px solid var(--border-dim);">
      <span style="font-size:13px; color:var(--muted);">{{ $s[0] }}</span>
      <span style="font-family:var(--font-mono); font-size:12px; color:{{ $s[2]==='good' ? 'var(--green)' : ($s[2]==='warn' ? 'var(--orange)' : 'var(--blue)') }};">{{ $s[1] }}</span>
    </div>
    @endforeach
  </div>
</div>

@endsection
