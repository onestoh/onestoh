@extends('layouts.dashboard')
@section('title', 'Property Manager — EstateYard')
@section('page-title', 'Operations Dashboard')
@section('page-subtitle', 'Property Manager · Multi-Property Operations')
@section('sidebar-nav')
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Overview</div>
  <a href="{{ url('/dashboard/property-manager') }}" class="dash-nav-item active"><span class="dash-nav-icon">🗂️</span> Dashboard</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">🏘️</span> Properties</a>
</div>
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Operations</div>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">👥</span> Tenants</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">💳</span> Rent Collection</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">🔧</span> Maintenance <span class="dash-nav-badge">7</span></a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">🔨</span> Vendors</a>
</div>
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Reports</div>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">📊</span> Owner Statements</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">📋</span> Leases</a>
</div>
@endsection
@section('content')
<div class="kpi-grid">
  @foreach([['🏘️','34','Properties Managed','8 landlord clients','up'],['✅','91%','Occupancy Rate','↑ 3% this quarter','up'],['🔧','7','Maintenance Open','2 urgent','down'],['💰','KSh 2.8M','Rent Collected','This month','up']] as $k)
  <div class="kpi-card"><div class="kpi-icon">{{ $k[0] }}</div><div class="kpi-value">{{ $k[1] }}</div><div class="kpi-label">{{ $k[2] }}</div><div class="kpi-change {{ $k[4] }}">{{ $k[3] }}</div></div>
  @endforeach
</div>
<div class="table-card" style="margin-bottom:28px;">
  <div class="table-card-header"><div class="table-card-title">🏘️ Properties Under Management</div></div>
  <table class="data-table">
    <thead><tr><th>Property</th><th>Owner</th><th>Units</th><th>Occupancy</th><th>Rent Due</th><th>Maintenance</th></tr></thead>
    <tbody>
      @foreach([
        ['🏢 Westlands Apts','James K.','10/12','83%','KSh 380K','1 open'],
        ['🏠 Karen Villas (x4)','Sarah O.','4/4','100%','KSh 480K','None'],
        ['🏬 CBD Office Block','David W.','3/4','75%','KSh 240K','2 open'],
        ['🏡 Kilimani Flats','Alice N.','6/6','100%','KSh 180K','1 open'],
        ['🏭 Syokimau Warehouse','Peter K.','2/2','100%','KSh 110K','None'],
      ] as $p)
      <tr>
        <td class="td-name">{{ $p[0] }}</td>
        <td>{{ $p[1] }}</td>
        <td>{{ $p[2] }}</td>
        <td><div style="display:flex; align-items:center; gap:8px;"><span style="color:var(--green);">{{ $p[3] }}</span><div class="progress" style="width:50px;"><div class="progress-bar green" style="width:{{ $p[3] }};"></div></div></div></td>
        <td class="td-price">{{ $p[4] }}</td>
        <td style="color:{{ $p[5]==='None' ? 'var(--muted)' : 'var(--orange)' }};">{{ $p[5] }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
<div class="grid-2">
  <div class="table-card">
    <div class="table-card-header"><div class="table-card-title">🔧 Maintenance Work Orders</div><span class="badge badge-orange">7 Open</span></div>
    <table class="data-table">
      <thead><tr><th>Issue</th><th>Property</th><th>Priority</th><th>Status</th></tr></thead>
      <tbody>
        @foreach([['Lift broken','Westlands Apts','High','assigned'],['Pipe burst','CBD Office','High','in-progress'],['AC unit','Karen Villa','Medium','scheduled'],['Painting','Kilimani Flats','Low','pending'],['Gate sensor','Westlands','Medium','assigned']] as $m)
        <tr><td class="td-name">🔧 {{ $m[0] }}</td><td>{{ $m[1] }}</td><td><span class="status-pill status-{{ $m[2]==='High' ? 'overdue' : ($m[2]==='Medium' ? 'review' : 'draft') }}">{{ $m[2] }}</span></td><td><span class="status-pill status-{{ $m[3]==='in-progress' ? 'review' : 'pending' }}">{{ strtoupper($m[3]) }}</span></td></tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="table-card">
    <div class="table-card-header"><div class="table-card-title">📊 Owner Statements</div><button class="btn btn-gold btn-sm">Generate All</button></div>
    <table class="data-table">
      <thead><tr><th>Owner</th><th>Properties</th><th>Gross Rent</th><th>Mgmt Fee</th><th>Net</th></tr></thead>
      <tbody>
        @foreach([['James K.',3,'KSh 380K','KSh 38K','KSh 342K'],['Sarah O.',4,'KSh 480K','KSh 48K','KSh 432K'],['David W.',2,'KSh 240K','KSh 24K','KSh 216K'],['Alice N.',1,'KSh 180K','KSh 18K','KSh 162K']] as $os)
        <tr><td class="td-name">{{ $os[0] }}</td><td>{{ $os[1] }}</td><td>{{ $os[2] }}</td><td style="color:var(--gold);">{{ $os[3] }}</td><td class="td-price">{{ $os[4] }}</td></tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
