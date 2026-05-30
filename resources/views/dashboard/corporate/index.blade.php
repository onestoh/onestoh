@extends('layouts.dashboard')
@section('title', 'Corporate Dashboard — EstateYard')
@section('page-title', 'Corporate Hub')
@section('page-subtitle', 'Corporate Partner · Agency, Agents & Company Analytics')
@section('sidebar-nav')
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Overview</div>
  <a href="{{ url('/dashboard/corporate') }}" class="dash-nav-item active"><span class="dash-nav-icon">🏢</span> Dashboard</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">👥</span> Agents</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">🏠</span> All Listings</a>
</div>
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Tools</div>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">📤</span> Bulk Upload</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">🏷️</span> White-Label</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">🔌</span> API Access</a>
</div>
@endsection
@section('content')
<div class="kpi-grid">
  @foreach([['👥','24','Total Agents','18 active this month','up'],['🏠','480','Company Listings','↑ 42 this quarter','up'],['💰','KSh 8.4M','Company Revenue','This month','up'],['🎯','1,247','Total Leads','Company-wide','up']] as $k)
  <div class="kpi-card"><div class="kpi-icon">{{ $k[0] }}</div><div class="kpi-value">{{ $k[1] }}</div><div class="kpi-label">{{ $k[2] }}</div><div class="kpi-change {{ $k[4] }}">{{ $k[3] }}</div></div>
  @endforeach
</div>
<div class="table-card" style="margin-bottom:28px;">
  <div class="table-card-header"><div class="table-card-title">👥 Agent Sub-Accounts</div><button class="btn btn-gold btn-sm">+ Add Agent</button></div>
  <table class="data-table">
    <thead><tr><th>Agent</th><th>Listings</th><th>Deals Closed</th><th>Commission</th><th>Status</th></tr></thead>
    <tbody>
      @foreach([['👨‍💼 James K.',28,12,'KSh 1.2M','active'],['👩‍💼 Sarah O.',22,8,'KSh 840K','active'],['👨‍💼 David W.',18,6,'KSh 620K','active'],['👩‍💼 Alice N.',31,15,'KSh 1.5M','active'],['👨‍💼 Peter K.',14,4,'KSh 380K','active'],['👩‍💼 Grace L.',8,2,'KSh 180K','review']] as $ag)
      <tr>
        <td class="td-name">{{ $ag[0] }}</td>
        <td>{{ $ag[1] }}</td>
        <td style="color:var(--green);">{{ $ag[2] }}</td>
        <td class="td-price">{{ $ag[3] }}</td>
        <td><span class="status-pill status-{{ $ag[4] }}">{{ strtoupper($ag[4]) }}</span></td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
<div class="grid-2">
  <div class="chart-container"><div style="margin-bottom:16px;"><div class="section-tag" style="margin-bottom:4px;">Revenue</div><div style="font-size:16px; font-weight:600; color:var(--white);">Company Monthly Revenue</div></div><div class="chart-placeholder">@foreach([55,65,60,72,78,68,82,88,80,86,92,100] as $h)<div class="chart-bar {{ $h >= 88 ? 'highlight' : '' }}" style="height:{{ $h }}%;"></div>@endforeach</div></div>
  <div style="background:var(--navy3); border:1px solid var(--border-dim); border-radius:var(--radius); padding:24px;">
    <div class="table-card-title" style="margin-bottom:16px;">🔌 API Integration</div>
    <div style="background:var(--surface); border-radius:8px; padding:16px; margin-bottom:16px; font-family:var(--font-mono); font-size:12px; color:var(--green);">
      API Key: <span style="color:var(--gold);">ey_live_xk8m2...3j9p</span><br>
      Rate Limit: 10,000 req/day<br>
      Status: <span style="color:var(--green);">● Active</span>
    </div>
    <div style="display:flex; gap:8px;"><button onclick="showToast('API key copied!', 'green')" class="btn btn-sm btn-outline">Copy Key</button><button class="btn btn-sm btn-outline">Regenerate</button><button class="btn btn-sm btn-outline">Docs</button></div>
  </div>
</div>
@endsection
