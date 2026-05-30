@extends('layouts.dashboard')
@section('title', 'Investor Dashboard — EstateYard')
@section('page-title', 'Portfolio Overview')
@section('page-subtitle', 'Investor / REIT · ROI, Yield & Cash Flow Analysis')
@section('sidebar-nav')
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Overview</div>
  <a href="{{ url('/dashboard/investor') }}" class="dash-nav-item active"><span class="dash-nav-icon">📊</span> Dashboard</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">🏘️</span> Portfolio</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">📈</span> Performance</a>
</div>
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Finance</div>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">💰</span> Cash Flow</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">🏦</span> Loans</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">📋</span> Tax Reports</a>
</div>
@endsection
@section('content')
<div class="kpi-grid">
  @foreach([
    ['🏘️','$4.2M','Portfolio Value','↑ 12% YoY appreciation','up'],
    ['📈','8.4%','Average Yield','Net rental yield','up'],
    ['💰','14.2%','IRR','Internal Rate of Return','up'],
    ['🏗️','6.8%','Cap Rate','Portfolio average','up'],
    ['💳','KSh 485K','Monthly Income','Net after expenses','up'],
    ['🏦','$850K','Loans Outstanding','2 active mortgages','down'],
  ] as $k)
  <div class="kpi-card"><div class="kpi-icon">{{ $k[0] }}</div><div class="kpi-value">{{ $k[1] }}</div><div class="kpi-label">{{ $k[2] }}</div><div class="kpi-change {{ $k[4] }}">{{ $k[3] }}</div></div>
  @endforeach
</div>

<div class="table-card" style="margin-bottom:28px;">
  <div class="table-card-header"><div class="table-card-title">🏘️ Portfolio Performance</div><button class="btn btn-sm btn-outline">📊 Export Report</button></div>
  <table class="data-table">
    <thead><tr><th>Asset</th><th>Value</th><th>Monthly Income</th><th>Yield</th><th>IRR</th><th>Loan</th><th>Cap Rate</th></tr></thead>
    <tbody>
      @foreach([
        ['🏠 Karen Villa','$850K','$3,200/mo','4.5%','11.2%','None','5.8%'],
        ['🏢 Westlands Apts (x8)','$1.2M','$8,400/mo','8.4%','14.8%','$350K','7.2%'],
        ['🏬 CBD Office','$1.8M','$12,000/mo','8%','15.2%','$500K','8.1%'],
        ['🌿 Kiambu Land','$280K','Appreciation','—','18% est.','None','—'],
        ['✈️ Airbnb, Mombasa','$220K','$4,800/mo','26.2%','28%','None','22%'],
      ] as $a)
      <tr>
        <td class="td-name">{{ $a[0] }}</td>
        <td class="td-price">{{ $a[1] }}</td>
        <td style="color:var(--green);">{{ $a[2] }}</td>
        <td>{{ $a[3] }}</td>
        <td style="color:var(--green);">{{ $a[4] }}</td>
        <td style="color:{{ $a[5]==='None' ? 'var(--muted)' : 'var(--red)' }};">{{ $a[5] }}</td>
        <td>{{ $a[6] }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

<div class="grid-2">
  <div class="chart-container"><div style="margin-bottom:16px;"><div class="section-tag" style="margin-bottom:4px;">Cash Flow</div><div style="font-size:16px; font-weight:600; color:var(--white);">Monthly Net Cash Flow</div></div><div class="chart-placeholder">@foreach([65,70,68,75,80,72,85,90,82,88,92,100] as $h)<div class="chart-bar {{ $h >= 88 ? 'highlight' : '' }}" style="height:{{ $h }}%;"></div>@endforeach</div></div>
  <div class="table-card">
    <div class="table-card-header"><div class="table-card-title">🏦 Loan Repayments</div></div>
    <table class="data-table">
      <thead><tr><th>Loan</th><th>Balance</th><th>Next Payment</th><th>Status</th></tr></thead>
      <tbody>
        @foreach([['Westlands Mortgage','$320K','Jun 1 · $2,400','active'],['CBD Office Loan','$480K','Jun 1 · $3,800','active']] as $l)
        <tr><td class="td-name">{{ $l[0] }}</td><td class="td-price">{{ $l[1] }}</td><td style="font-family:var(--font-mono); font-size:12px;">{{ $l[2] }}</td><td><span class="status-pill status-active">ACTIVE</span></td></tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
