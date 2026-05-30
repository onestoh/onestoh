@extends('layouts.dashboard')
@section('title', 'Landlord Dashboard — EstateYard')
@section('page-title', 'My Properties')
@section('page-subtitle', 'Landlord · Property Portfolio & Rent Collection')

@section('sidebar-nav')
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Overview</div>
  <a href="{{ url('/dashboard/landlord') }}" class="dash-nav-item active"><span class="dash-nav-icon">🏠</span> Dashboard</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">🏘️</span> My Properties</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">📈</span> Analytics</a>
</div>
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Tenants</div>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">👥</span> All Tenants</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">💳</span> Rent Collection <span class="dash-nav-badge">3</span></a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">📋</span> Leases</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">📨</span> Invite Tenant</a>
</div>
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Finance</div>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">📊</span> Income Reports</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">💰</span> Expense Ledger</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">🏦</span> Apply for Finance</a>
</div>
<div class="dash-nav-section">
  <div class="dash-nav-section-title">More</div>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">🔧</span> Maintenance <span class="dash-nav-badge">4</span></a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">📂</span> Documents</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">🔗</span> Referral Links</a>
</div>
@endsection

@section('content')

<div class="alert alert-green" style="margin-bottom:24px;">💳 Rent collected: KSh 245,000 received today from 3 tenants. Funds transferred to your M-Pesa.</div>

<!-- KPIs -->
<div class="kpi-grid">
  @foreach([
    ['🏘️','12','Total Properties','8 residential · 4 commercial','up'],
    ['✅','89%','Occupancy Rate','↑ 4% vs last quarter','up'],
    ['💰','KSh 485K','Monthly Rent Income','Expected this month','up'],
    ['⚠️','KSh 95K','Outstanding Arrears','2 tenants overdue','down'],
    ['🔧','4','Maintenance Open','1 urgent · 3 normal','down'],
    ['📋','3','Lease Renewals Due','Within next 30 days','down'],
  ] as $k)
  <div class="kpi-card">
    <div class="kpi-icon">{{ $k[0] }}</div>
    <div class="kpi-value">{{ $k[1] }}</div>
    <div class="kpi-label">{{ $k[2] }}</div>
    <div class="kpi-change {{ $k[4] }}">{{ $k[3] }}</div>
  </div>
  @endforeach
</div>

<!-- QUICK ACTIONS -->
<div style="display:flex; gap:10px; margin-bottom:28px; flex-wrap:wrap;">
  <a href="{{ url('/marketplace') }}" class="btn btn-gold"><span>➕</span> Add Property</a>
  <button onclick="showToast('Invite link copied!', 'green')" class="btn btn-outline"><span>📨</span> Invite Tenant</button>
  <button class="btn btn-outline"><span>💳</span> Collect Rent</button>
  <a href="{{ url('/financing') }}" class="btn btn-outline"><span>🏦</span> Apply Finance</a>
  <button class="btn btn-outline"><span>📊</span> Download Report</button>
</div>

<!-- PROPERTY PORTFOLIO -->
<div class="table-card" style="margin-bottom:28px;">
  <div class="table-card-header">
    <div class="table-card-title">🏘️ Property Portfolio</div>
    <a href="#" class="btn btn-gold btn-sm">+ Add Property</a>
  </div>
  <table class="data-table">
    <thead><tr><th>Property</th><th>Type</th><th>Units</th><th>Occupancy</th><th>Monthly Income</th><th>Status</th><th></th></tr></thead>
    <tbody>
      @foreach([
        ['🏠','4-Bed Villa, Karen','Residential','1/1','100%','KSh 120K','active'],
        ['🏢','Westlands Apartments','Residential','8/10','80%','KSh 280K','active'],
        ['🏬','CBD Office Block','Commercial','3/4','75%','KSh 180K','active'],
        ['🏡','3-Bed Bungalow, Lavington','Residential','1/1','100%','KSh 85K','active'],
        ['🏭','Warehouse, Syokimau','Industrial','1/1','100%','KSh 55K','active'],
        ['🏘️','Kilimani Flats (x6)','Residential','5/6','83%','KSh 150K','active'],
      ] as $p)
      <tr>
        <td><div class="td-name" style="display:flex; align-items:center; gap:8px;"><span>{{ $p[0] }}</span>{{ $p[1] }}</div></td>
        <td>{{ $p[2] }}</td>
        <td>{{ $p[3] }}</td>
        <td>
          <div style="font-size:13px; color:{{ $p[4]==='100%' ? 'var(--green)' : 'var(--gold)' }}; margin-bottom:4px;">{{ $p[4] }}</div>
          <div class="progress" style="width:80px;"><div class="progress-bar {{ $p[4]==='100%' ? 'green' : '' }}" style="width:{{ $p[4] }};"></div></div>
        </td>
        <td class="td-price">{{ $p[5] }}</td>
        <td><span class="status-pill status-active">ACTIVE</span></td>
        <td><div style="display:flex; gap:6px;"><button class="btn btn-sm btn-outline">Manage</button><button class="btn btn-sm btn-outline">📋 Leases</button></div></td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

<!-- RENT COLLECTION & CHART -->
<div class="grid-2" style="margin-bottom:28px;">

  <!-- Rent Collection Status -->
  <div class="table-card">
    <div class="table-card-header">
      <div class="table-card-title">💳 Rent Collection — {{ date('F Y') }}</div>
      <button onclick="showToast('Reminders sent to overdue tenants', 'gold')" class="btn btn-sm btn-outline">Send Reminders</button>
    </div>
    <table class="data-table">
      <thead><tr><th>Tenant</th><th>Property</th><th>Rent</th><th>Due Date</th><th>Status</th></tr></thead>
      <tbody>
        @foreach([
          ['James M.','Karen Villa','KSh 120K','1st Jun','paid'],
          ['Sarah O.','Westlands Apt 2A','KSh 35K','1st Jun','paid'],
          ['David W.','Westlands Apt 2B','KSh 35K','1st Jun','paid'],
          ['Alice N.','Kilimani Flat 3','KSh 25K','5th Jun','pending'],
          ['Peter K.','CBD Office 2F','KSh 45K','1st Jun','overdue'],
          ['Tom A.','Westlands Apt 4C','KSh 35K','1st Jun','overdue'],
          ['Grace L.','Lavington','KSh 85K','3rd Jun','pending'],
        ] as $t)
        <tr>
          <td class="td-name">{{ $t[0] }}</td>
          <td>{{ $t[1] }}</td>
          <td class="td-price">{{ $t[2] }}</td>
          <td style="font-family:var(--font-mono); font-size:12px;">{{ $t[3] }}</td>
          <td><span class="status-pill status-{{ $t[4] }}">{{ strtoupper($t[4]) }}</span></td>
        </tr>
        @endforeach
      </tbody>
    </table>
    <div style="padding:16px 24px; border-top:1px solid var(--border-dim); display:flex; justify-content:space-between;">
      <div style="font-size:13px; color:var(--muted);">Collected: <span style="color:var(--green); font-weight:600;">KSh 245K</span> · Pending: <span style="color:var(--gold); font-weight:600;">KSh 110K</span> · Overdue: <span style="color:var(--red); font-weight:600;">KSh 80K</span></div>
    </div>
  </div>

  <!-- Monthly Income Chart -->
  <div class="chart-container">
    <div style="margin-bottom:16px;">
      <div class="section-tag" style="margin-bottom:4px;">Income</div>
      <div style="font-size:16px; font-weight:600; color:var(--white);">Monthly Rent Income</div>
    </div>
    <div class="chart-placeholder">
      @foreach([68,72,75,80,85,78,88,92,87,94,96,100] as $h)
      <div class="chart-bar {{ $h >= 90 ? 'highlight' : '' }}" style="height:{{ $h }}%;"></div>
      @endforeach
    </div>
    <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px; margin-top:16px; padding-top:12px; border-top:1px solid var(--border-dim);">
      <div><div style="font-size:10px; font-family:var(--font-mono); color:var(--muted); margin-bottom:4px;">THIS MONTH</div><div style="font-family:var(--font-serif); font-size:20px; font-weight:700; color:var(--gold);">KSh 485K</div></div>
      <div><div style="font-size:10px; font-family:var(--font-mono); color:var(--muted); margin-bottom:4px;">LAST MONTH</div><div style="font-family:var(--font-serif); font-size:20px; font-weight:700; color:var(--white);">KSh 460K</div></div>
      <div><div style="font-size:10px; font-family:var(--font-mono); color:var(--muted); margin-bottom:4px;">YTD INCOME</div><div style="font-family:var(--font-serif); font-size:20px; font-weight:700; color:var(--green);">KSh 5.1M</div></div>
    </div>
  </div>
</div>

<!-- MAINTENANCE + REFERRAL LINKS -->
<div class="grid-2">
  <!-- Maintenance -->
  <div class="table-card">
    <div class="table-card-header">
      <div class="table-card-title">🔧 Maintenance Requests</div>
      <span class="badge badge-orange">4 Open</span>
    </div>
    <div style="padding:0 24px;">
      @foreach([
        ['Peter K.','CBD Office — Blocked sink','High','2 days ago'],
        ['Sarah O.','Westlands Apt — AC unit faulty','Medium','3 days ago'],
        ['David W.','Westlands Apt — Door lock broken','Medium','5 days ago'],
        ['Alice N.','Kilimani Flat — Leaking tap','Low','1 week ago'],
      ] as $m)
      <div style="display:flex; gap:12px; padding:14px 0; border-bottom:1px solid var(--border-dim); align-items:flex-start;">
        <span style="font-size:24px;">🔧</span>
        <div style="flex:1;">
          <div style="font-size:13px; font-weight:600; color:var(--white);">{{ $m[1] }}</div>
          <div style="font-size:12px; color:var(--muted); margin-top:2px;">From: {{ $m[0] }} · {{ $m[3] }}</div>
        </div>
        <span class="status-pill {{ $m[2]==='High' ? 'status-overdue' : ($m[2]==='Medium' ? 'status-review' : 'status-draft') }}">{{ $m[2] }}</span>
      </div>
      @endforeach
    </div>
  </div>

  <!-- Expense Ledger -->
  <div class="table-card">
    <div class="table-card-header">
      <div class="table-card-title">💼 Expense Ledger</div>
      <button class="btn btn-sm btn-outline">+ Add Expense</button>
    </div>
    <table class="data-table">
      <thead><tr><th>Description</th><th>Property</th><th>Amount</th></tr></thead>
      <tbody>
        @foreach([
          ['🔧 Plumbing Repair','Westlands Apt','KSh 8,500'],
          ['🔒 Security Upgrade','Karen Villa','KSh 25,000'],
          ['🌿 Garden Service','Karen Villa','KSh 5,000'],
          ['💡 Electrical Works','CBD Office','KSh 18,000'],
          ['🏛️ County Rates','All Properties','KSh 42,000'],
          ['🔑 Property Insurance','All Properties','KSh 35,000'],
        ] as $exp)
        <tr>
          <td class="td-name">{{ $exp[0] }}</td>
          <td>{{ $exp[1] }}</td>
          <td style="color:var(--red);">{{ $exp[2] }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
    <div style="padding:14px 24px; border-top:1px solid var(--border-dim);">
      <div style="font-size:13px; color:var(--muted);">Total This Month: <span style="color:var(--red); font-weight:600;">KSh 133,500</span></div>
    </div>
  </div>
</div>

@endsection
