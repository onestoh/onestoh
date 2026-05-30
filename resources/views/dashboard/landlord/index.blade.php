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

@php
  $paidCount    = $rentPaymentsByStatus->get('paid')->count    ?? 0;
  $pendingCount = $rentPaymentsByStatus->get('pending')->count  ?? 0;
  $overdueCount = $rentPaymentsByStatus->get('overdue')->count  ?? 0;
  $paidTotal    = $rentPaymentsByStatus->get('paid')->total     ?? 0;
  $pendingTotal = $rentPaymentsByStatus->get('pending')->total  ?? 0;
  $overdueTotal = $rentPaymentsByStatus->get('overdue')->total  ?? 0;
@endphp

@if(session('success'))
<div class="alert alert-green" style="margin-bottom:24px;">{{ session('success') }}</div>
@endif

@if($paidCount > 0)
<div class="alert alert-green" style="margin-bottom:24px;">💳 Rent collected: KSh {{ number_format($paidTotal) }} from {{ $paidCount }} payment(s) this period.</div>
@endif

<!-- KPIs -->
<div class="kpi-grid">
  <div class="kpi-card">
    <div class="kpi-icon">🏘️</div>
    <div class="kpi-value">{{ $properties->count() }}</div>
    <div class="kpi-label">Total Properties</div>
    <div class="kpi-change up">{{ $properties->where('status','active')->count() }} active</div>
  </div>
  <div class="kpi-card">
    <div class="kpi-icon">📋</div>
    <div class="kpi-value">{{ $leases->count() }}</div>
    <div class="kpi-label">Total Leases</div>
    <div class="kpi-change up">{{ $leases->where('status','active')->count() }} active</div>
  </div>
  <div class="kpi-card">
    <div class="kpi-icon">💰</div>
    <div class="kpi-value">KSh {{ number_format($paidTotal) }}</div>
    <div class="kpi-label">Rent Collected</div>
    <div class="kpi-change up">{{ $paidCount }} payment(s)</div>
  </div>
  <div class="kpi-card">
    <div class="kpi-icon">⏳</div>
    <div class="kpi-value">KSh {{ number_format($pendingTotal) }}</div>
    <div class="kpi-label">Pending Rent</div>
    <div class="kpi-change down">{{ $pendingCount }} payment(s)</div>
  </div>
  <div class="kpi-card">
    <div class="kpi-icon">⚠️</div>
    <div class="kpi-value">KSh {{ number_format($overdueTotal) }}</div>
    <div class="kpi-label">Overdue Arrears</div>
    <div class="kpi-change down">{{ $overdueCount }} overdue</div>
  </div>
  <div class="kpi-card">
    <div class="kpi-icon">🔧</div>
    <div class="kpi-value">{{ $maintenanceRequests->where('status','open')->count() }}</div>
    <div class="kpi-label">Maintenance Open</div>
    <div class="kpi-change down">{{ $maintenanceRequests->where('priority','urgent')->count() }} urgent</div>
  </div>
</div>

<!-- QUICK ACTIONS -->
<div style="display:flex; gap:10px; margin-bottom:28px; flex-wrap:wrap;">
  <a href="{{ url('/properties/create') }}" class="btn btn-gold"><span>➕</span> List New Property</a>
  <button onclick="showToast('Invite link copied!', 'green')" class="btn btn-outline"><span>📨</span> Invite Tenant</button>
  <a href="{{ url('/financing') }}" class="btn btn-outline"><span>🏦</span> Apply Finance</a>
  <button class="btn btn-outline"><span>📊</span> Download Report</button>
</div>

<!-- PROPERTY PORTFOLIO -->
<div class="table-card" style="margin-bottom:28px;">
  <div class="table-card-header">
    <div class="table-card-title">🏘️ Property Portfolio</div>
    <a href="{{ url('/properties/create') }}" class="btn btn-gold btn-sm">+ List New Property</a>
  </div>
  @if($properties->isEmpty())
  <div style="padding:40px; text-align:center; color:var(--muted);">
    <div style="font-size:48px; margin-bottom:12px;">🏡</div>
    <div style="font-size:15px; margin-bottom:16px;">No properties listed yet.</div>
    <a href="{{ url('/properties/create') }}" class="btn btn-gold">List Your First Property</a>
  </div>
  @else
  <table class="data-table">
    <thead><tr><th>Property</th><th>Type</th><th>Leases</th><th>Price</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
      @foreach($properties as $p)
      <tr>
        <td>
          <div class="td-name" style="display:flex; align-items:center; gap:8px;">
            <span>🏠</span>
            <div>
              <div style="font-weight:600; color:var(--white);">{{ $p->title }}</div>
              <div style="font-size:12px; color:var(--muted);">{{ $p->county }} · {{ $p->location }}</div>
            </div>
          </div>
        </td>
        <td>{{ ucfirst($p->type) }}</td>
        <td>{{ $p->leases_count }}</td>
        <td class="td-price">KSh {{ number_format($p->price) }}</td>
        <td><span class="status-pill status-{{ $p->status }}">{{ strtoupper($p->status) }}</span></td>
        <td>
          <div style="display:flex; gap:6px;">
            <a href="{{ url('/properties/' . $p->id . '/edit') }}" class="btn btn-sm btn-outline">✏️ Edit</a>
            <form method="POST" action="{{ url('/properties/' . $p->id) }}" onsubmit="return confirm('Delete this property?')">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-sm btn-danger">🗑</button>
            </form>
          </div>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @endif
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
      <thead><tr><th>Tenant</th><th>Property</th><th>Amount</th><th>Due Date</th><th>Status</th></tr></thead>
      <tbody>
        @forelse($leases as $lease)
        <tr>
          <td class="td-name">{{ $lease->tenant->name ?? 'N/A' }}</td>
          <td>{{ $lease->property->title ?? 'N/A' }}</td>
          <td class="td-price">KSh {{ number_format($lease->monthly_rent ?? 0) }}</td>
          <td style="font-family:var(--font-mono); font-size:12px;">
            {{ $lease->end_date ? $lease->end_date->format('d M Y') : '—' }}
          </td>
          <td><span class="status-pill status-{{ $lease->status }}">{{ strtoupper($lease->status) }}</span></td>
        </tr>
        @empty
        <tr><td colspan="5" style="text-align:center; color:var(--muted); padding:24px;">No leases found.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div style="padding:16px 24px; border-top:1px solid var(--border-dim); display:flex; justify-content:space-between;">
      <div style="font-size:13px; color:var(--muted);">
        Collected: <span style="color:var(--green); font-weight:600;">KSh {{ number_format($paidTotal) }}</span> ·
        Pending: <span style="color:var(--gold); font-weight:600;">KSh {{ number_format($pendingTotal) }}</span> ·
        Overdue: <span style="color:var(--red); font-weight:600;">KSh {{ number_format($overdueTotal) }}</span>
      </div>
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
      <span class="badge badge-orange">{{ $maintenanceRequests->where('status','open')->count() }} Open</span>
    </div>
    <div style="padding:0 24px;">
      @forelse($maintenanceRequests as $m)
      <div style="display:flex; gap:12px; padding:14px 0; border-bottom:1px solid var(--border-dim); align-items:flex-start;">
        <span style="font-size:24px;">🔧</span>
        <div style="flex:1;">
          <div style="font-size:13px; font-weight:600; color:var(--white);">{{ $m->property->title ?? 'N/A' }} — {{ $m->title }}</div>
          <div style="font-size:12px; color:var(--muted); margin-top:2px;">{{ $m->created_at->diffForHumans() }}</div>
        </div>
        <span class="status-pill {{ $m->priority==='urgent'||$m->priority==='high' ? 'status-overdue' : ($m->priority==='medium' ? 'status-review' : 'status-draft') }}">{{ strtoupper($m->priority) }}</span>
      </div>
      @empty
      <div style="padding:24px 0; text-align:center; color:var(--muted);">No maintenance requests.</div>
      @endforelse
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
