@extends('layouts.dashboard')
@section('title', 'Surveyor Dashboard — EstateYard')
@section('page-title', 'Survey Jobs')
@section('page-subtitle', 'Surveyor · Site Inspections, GPS Reports & Revenue')
@section('sidebar-nav')
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Overview</div>
  <a href="{{ url('/dashboard/surveyor') }}" class="dash-nav-item active"><span class="dash-nav-icon">🗺️</span> Dashboard</a>
</div>
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Jobs</div>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">📋</span> Active Jobs <span class="dash-nav-badge">4</span></a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">📅</span> Site Visits</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">📄</span> Reports Archive</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">🧾</span> Invoices</a>
</div>
@endsection
@section('content')
<div class="kpi-grid">
  @foreach([['📋','4','Active Jobs','2 site visits this week','up'],['📅','6','Scheduled Visits','Next: Tomorrow 9am','up'],['✅','18','Reports Completed','This month','up'],['💰','KSh 196K','Revenue This Month','Avg KSh 11K/job','up']] as $k)
  <div class="kpi-card"><div class="kpi-icon">{{ $k[0] }}</div><div class="kpi-value">{{ $k[1] }}</div><div class="kpi-label">{{ $k[2] }}</div><div class="kpi-change {{ $k[4] }}">{{ $k[3] }}</div></div>
  @endforeach
</div>
<div class="table-card" style="margin-bottom:28px;">
  <div class="table-card-header"><div class="table-card-title">📋 Survey Job Queue</div></div>
  <table class="data-table">
    <thead><tr><th>Property</th><th>Client</th><th>Survey Type</th><th>Visit Date</th><th>Status</th><th>Fee</th></tr></thead>
    <tbody>
      @foreach([
        ['🌿 1 Acre Land, Kiambu','Private Buyer','Boundary Survey','Jun 2, 2026','scheduled','KSh 18,000'],
        ['🏠 New Build, Runda','Developer','Structural','Jun 4, 2026','in-progress','KSh 25,000'],
        ['🏢 Office, CBD','KCB Bank','Due Diligence','Jun 6, 2026','assigned','KSh 30,000'],
        ['🌾 Farmland, Nakuru','Mortgage Bank','Title Survey','Jun 9, 2026','assigned','KSh 12,000'],
      ] as $j)
      <tr>
        <td class="td-name">{{ $j[0] }}</td>
        <td>{{ $j[1] }}</td>
        <td>{{ $j[2] }}</td>
        <td style="font-family:var(--font-mono); font-size:12px;">{{ $j[3] }}</td>
        <td><span class="status-pill status-{{ $j[4]==='in-progress' ? 'review' : 'pending' }}">{{ strtoupper($j[4]) }}</span></td>
        <td class="td-price">{{ $j[5] }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
<div style="background:var(--navy3); border:1px solid var(--border-dim); border-radius:var(--radius); padding:24px;">
  <div class="table-card-title" style="margin-bottom:16px;">📅 Upcoming Site Visits</div>
  <div class="timeline">
    @foreach([['Tomorrow · 9:00 AM','Boundary Survey — Kiambu Road','Client: Private Buyer · GPS: -1.1782° S, 36.8282° E'],['Jun 4 · 10:00 AM','Structural Survey — Runda','Client: Developer · GPS: -1.2244° S, 36.7714° E'],['Jun 6 · 2:00 PM','Due Diligence — Upper Hill','Client: KCB Bank · GPS: -1.2921° S, 36.8219° E']] as $v)
    <div class="timeline-item">
      <div class="timeline-node">🗺️</div>
      <div class="timeline-body">
        <div class="timeline-time">{{ $v[0] }}</div>
        <div class="timeline-title">{{ $v[1] }}</div>
        <div class="timeline-desc">{{ $v[2] }}</div>
      </div>
    </div>
    @endforeach
  </div>
</div>
@endsection
