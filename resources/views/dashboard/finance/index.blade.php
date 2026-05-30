@extends('layouts.dashboard')
@section('title', 'Finance Dashboard — EstateYard')
@section('page-title', 'Financial Control Centre')
@section('page-subtitle', 'Finance · General Ledger, Escrow & Tax Reports')
@section('sidebar-nav')
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Overview</div>
  <a href="{{ url('/dashboard/finance') }}" class="dash-nav-item active"><span class="dash-nav-icon">💰</span> Dashboard</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">📊</span> P&L Report</a>
</div>
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Accounting</div>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">📋</span> General Ledger</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">🔒</span> Escrow Reconciliation</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">💳</span> Rent Collections</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">🏦</span> Loan Tracking</a>
</div>
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Tax</div>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">📑</span> Tax Reports</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">📉</span> Depreciation</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">💹</span> Capital Gains</a>
</div>
@endsection
@section('content')
<div class="kpi-grid">
  @foreach([['💰','KSh 8.4M','Total Revenue','This month','up'],['⚠️','KSh 425K','Outstanding Arrears','24 overdue tenants','down'],['🔒','$4.2M','Escrow Balance','14 active deals','up'],['💸','KSh 1.2M','Total Expenses','This month','down']] as $k)
  <div class="kpi-card"><div class="kpi-icon">{{ $k[0] }}</div><div class="kpi-value">{{ $k[1] }}</div><div class="kpi-label">{{ $k[2] }}</div><div class="kpi-change {{ $k[4] }}">{{ $k[3] }}</div></div>
  @endforeach
</div>

<div class="grid-2" style="margin-bottom:28px;">
  <div class="chart-container">
    <div style="margin-bottom:16px;"><div class="section-tag" style="margin-bottom:4px;">Revenue</div><div style="font-size:16px; font-weight:600; color:var(--white);">Revenue vs Expenses</div></div>
    <div class="chart-placeholder">@foreach([72,78,68,82,88,75,90,95,84,90,94,100] as $h)<div class="chart-bar {{ $h >= 90 ? 'highlight' : '' }}" style="height:{{ $h }}%;"></div>@endforeach</div>
  </div>
  <div class="table-card">
    <div class="table-card-header"><div class="table-card-title">🔒 Escrow Reconciliation</div></div>
    <table class="data-table">
      <thead><tr><th>Deal</th><th>Amount</th><th>Stage</th><th>Status</th></tr></thead>
      <tbody>
        @foreach([['Karen Villa Sale','$285K','Conditions Met','active'],['CBD Office Lease','$45K/yr','Terms Agreed','pending'],['Muthaiga Sale','$850K','Funds Locked','active'],['Land, Thika','$45K','Released','closed'],['Off-Plan Units','$480K','Partial','review']] as $e)
        <tr><td class="td-name">{{ $e[0] }}</td><td class="td-price">{{ $e[1] }}</td><td>{{ $e[2] }}</td><td><span class="status-pill status-{{ $e[3] }}">{{ strtoupper($e[3]) }}</span></td></tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<div class="table-card" style="margin-bottom:28px;">
  <div class="table-card-header"><div class="table-card-title">📋 General Ledger — May 2026</div><button class="btn btn-sm btn-outline">📊 Export</button></div>
  <table class="data-table">
    <thead><tr><th>Date</th><th>Account</th><th>Description</th><th>Debit</th><th>Credit</th><th>Balance</th></tr></thead>
    <tbody>
      @foreach([
        ['Jun 1','Rental Income','Rent collection — Westlands Apts','—','KSh 380K','KSh 8.82M'],
        ['Jun 1','Platform Fees','EstateYard service fee (2%)','KSh 7,600','—','KSh 8.81M'],
        ['Jun 1','Escrow Release','Karen Villa Sale — final release','—','$285K','KSh 8.81M +$285K'],
        ['May 31','Maintenance','Plumbing repairs — CBD Office','KSh 18K','—','KSh 8.79M'],
        ['May 31','Loan Repayment','Equity Bank mortgage — Westlands','KSh 85K','—','KSh 8.71M'],
        ['May 28','Referral Payout','Commission payout — 3 brokers','KSh 127K','—','KSh 8.58M'],
      ] as $gl)
      <tr><td style="font-family:var(--font-mono); font-size:12px;">{{ $gl[0] }}</td><td class="td-name">{{ $gl[1] }}</td><td style="color:var(--muted);">{{ $gl[2] }}</td><td style="color:var(--red);">{{ $gl[3] }}</td><td class="td-price">{{ $gl[4] }}</td><td style="color:var(--blue);">{{ $gl[5] }}</td></tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
