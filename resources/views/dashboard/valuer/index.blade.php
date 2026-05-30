@extends('layouts.dashboard')
@section('title', 'Valuer Dashboard — EstateYard')
@section('page-title', 'Valuation Jobs')
@section('page-subtitle', 'Valuer · Job Queue, Reports & Revenue')
@section('sidebar-nav')
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Overview</div>
  <a href="{{ url('/dashboard/valuer') }}" class="dash-nav-item active"><span class="dash-nav-icon">📐</span> Dashboard</a>
</div>
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Jobs</div>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">📋</span> Active Jobs <span class="dash-nav-badge">5</span></a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">✅</span> Completed</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">📄</span> Reports</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">🧾</span> Invoices</a>
</div>
@endsection
@section('content')
<div class="kpi-grid">
  @foreach([['📋','5','Active Jobs','3 assigned · 2 in progress','up'],['✅','24','Completed This Month','↑ 8 vs last month','up'],['⏳','2','Pending Reports','Overdue by 1 day','down'],['💰','KSh 284K','Revenue This Month','Per valuation avg KSh 12K','up']] as $k)
  <div class="kpi-card"><div class="kpi-icon">{{ $k[0] }}</div><div class="kpi-value">{{ $k[1] }}</div><div class="kpi-label">{{ $k[2] }}</div><div class="kpi-change {{ $k[4] }}">{{ $k[3] }}</div></div>
  @endforeach
</div>
<div class="table-card" style="margin-bottom:28px;">
  <div class="table-card-header"><div class="table-card-title">📋 Active Job Queue</div></div>
  <table class="data-table">
    <thead><tr><th>Property</th><th>Client</th><th>Purpose</th><th>Due Date</th><th>Status</th><th>Fee</th><th>Action</th></tr></thead>
    <tbody>
      @foreach([
        ['🏠 4-Bed Villa, Karen','Equity Bank','Mortgage','Jun 3, 2026','in-progress','KSh 15,000'],
        ['🏢 Office Block, CBD','KCB Bank','Refinance','Jun 5, 2026','assigned','KSh 25,000'],
        ['🌿 Land, Kitengela','Private Buyer','Purchase','Jun 7, 2026','assigned','KSh 8,000'],
        ['🏡 Kilimani Apt','ABSA Bank','Construction','Jun 10, 2026','assigned','KSh 12,000'],
        ['🏭 Warehouse, Syokimau','Stanbic Bank','Valuation','Jun 12, 2026','assigned','KSh 18,000'],
      ] as $j)
      <tr>
        <td class="td-name">{{ $j[0] }}</td>
        <td>{{ $j[1] }}</td>
        <td>{{ $j[2] }}</td>
        <td style="font-family:var(--font-mono); font-size:12px;">{{ $j[3] }}</td>
        <td><span class="status-pill status-{{ $j[4]==='in-progress' ? 'review' : 'pending' }}">{{ strtoupper($j[4]) }}</span></td>
        <td class="td-price">{{ $j[5] }}</td>
        <td><button onclick="showToast('Report editor opened', 'blue')" class="btn btn-gold btn-sm">Open Report</button></td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
<div class="chart-container">
  <div style="margin-bottom:16px;"><div class="section-tag" style="margin-bottom:4px;">Revenue</div><div style="font-size:16px; font-weight:600; color:var(--white);">Monthly Valuation Revenue</div></div>
  <div class="chart-placeholder">@foreach([60,72,65,80,88,75,90,95,84,88,92,100] as $h)<div class="chart-bar {{ $h >= 90 ? 'highlight' : '' }}" style="height:{{ $h }}%;"></div>@endforeach</div>
</div>
@endsection
