@extends('layouts.dashboard')
@section('title', 'Developer Dashboard — EstateYard')
@section('page-title', 'Project Pipeline')
@section('page-subtitle', 'Developer · Projects, Off-Plan Sales & Financing')

@section('sidebar-nav')
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Overview</div>
  <a href="{{ url('/dashboard/developer') }}" class="dash-nav-item active"><span class="dash-nav-icon">🏗️</span> Dashboard</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">📊</span> Analytics</a>
</div>
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Projects</div>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">🏗️</span> All Projects</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">🏘️</span> Unit Inventory</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">📅</span> Gantt Timeline</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">👷</span> Contractors</a>
</div>
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Sales</div>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">💰</span> Off-Plan Sales</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">🔗</span> Referral Links</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">📈</span> Investor Reports</a>
</div>
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Finance</div>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">🏦</span> Construction Loans</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">📋</span> Compliance</a>
</div>
@endsection

@section('content')
<div class="kpi-grid">
  @foreach([
    ['🏗️','6','Active Projects','2 pre-sale · 3 construction · 1 completed','up'],
    ['🏘️','284','Units Sold','↑ 42 this quarter','up'],
    ['🏠','156','Units Available','Across all projects','up'],
    ['📈','68%','Avg Construction Progress','On schedule','up'],
    ['💰','KSh 2.8B','Revenue to Date','This fiscal year','up'],
    ['🏦','KSh 850M','Financing Draw','Construction loan utilized','up'],
  ] as $k)
  <div class="kpi-card"><div class="kpi-icon">{{ $k[0] }}</div><div class="kpi-value">{{ $k[1] }}</div><div class="kpi-label">{{ $k[2] }}</div><div class="kpi-change {{ $k[4] }}">{{ $k[3] }}</div></div>
  @endforeach
</div>

<div class="table-card" style="margin-bottom:28px;">
  <div class="table-card-header"><div class="table-card-title">🏗️ Project Pipeline</div><button class="btn btn-gold btn-sm">+ New Project</button></div>
  <table class="data-table">
    <thead><tr><th>Project</th><th>Stage</th><th>Units</th><th>Sold</th><th>Progress</th><th>Revenue</th><th>Status</th></tr></thead>
    <tbody>
      @foreach([
        ['🏘️ Kilimani Heights','Under Construction','120 units','84 sold','72%','KSh 840M','active'],
        ['🏢 Westlands Tower','Pre-Sale','200 units','67 sold','15%','KSh 670M','active'],
        ['🏡 Karen Villas','Completed','48 units','48 sold','100%','KSh 1.44B','closed'],
        ['🏗️ Muthaiga Gardens','Under Construction','80 units','32 sold','45%','KSh 320M','active'],
        ['🏠 Parklands Residences','Pre-Sale','160 units','28 sold','5%','KSh 280M','active'],
        ['🏭 Syokimau Offices','Under Construction','24 units','18 sold','60%','KSh 270M','active'],
      ] as $p)
      <tr>
        <td class="td-name">{{ $p[0] }}</td>
        <td>{{ $p[1] }}</td>
        <td>{{ $p[2] }}</td>
        <td style="color:var(--green);">{{ $p[3] }}</td>
        <td><div style="font-size:12px; color:var(--gold); margin-bottom:4px;">{{ $p[4] }}</div><div class="progress" style="width:80px;"><div class="progress-bar" style="width:{{ $p[4] }};"></div></div></td>
        <td class="td-price">{{ $p[5] }}</td>
        <td><span class="status-pill status-{{ $p[6] }}">{{ strtoupper($p[6]) }}</span></td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

<div class="grid-2">
  <div class="chart-container">
    <div style="margin-bottom:16px;"><div class="section-tag" style="margin-bottom:4px;">Sales</div><div style="font-size:16px; font-weight:600; color:var(--white);">Monthly Unit Sales</div></div>
    <div class="chart-placeholder">@foreach([20,35,28,42,55,48,62,70,58,65,72,84] as $h)<div class="chart-bar {{ $h >= 70 ? 'highlight' : '' }}" style="height:{{ $h }}%;"></div>@endforeach</div>
  </div>
  <div class="table-card">
    <div class="table-card-header"><div class="table-card-title">🏦 Construction Loans</div><a href="{{ url('/financing') }}" class="btn btn-gold btn-sm">Apply</a></div>
    <table class="data-table">
      <thead><tr><th>Project</th><th>Loan</th><th>Drawn</th><th>Status</th></tr></thead>
      <tbody>
        @foreach([['Kilimani Heights','KSh 500M','KSh 360M','active'],['Westlands Tower','KSh 800M','KSh 120M','active'],['Parklands','KSh 400M','KSh 20M','active']] as $l)
        <tr><td class="td-name">{{ $l[0] }}</td><td class="td-price">{{ $l[1] }}</td><td style="color:var(--orange);">{{ $l[2] }}</td><td><span class="status-pill status-active">ACTIVE</span></td></tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
