@extends('layouts.dashboard')
@section('title', 'Licensed Broker — EstateYard')
@section('page-title', 'Broker Dashboard')
@section('page-subtitle', 'Licensed Broker · Listings, CRM & Commissions')

@section('sidebar-nav')
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Overview</div>
  <a href="{{ url('/dashboard/broker') }}" class="dash-nav-item active"><span class="dash-nav-icon">📋</span> Dashboard</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">📊</span> Analytics</a>
</div>
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Listings</div>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">🏠</span> My Listings</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">➕</span> Create Listing</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">📷</span> Media Manager</a>
</div>
<div class="dash-nav-section">
  <div class="dash-nav-section-title">CRM</div>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">🎯</span> Lead Pipeline</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">📅</span> Appointments</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">📨</span> Client Invites</a>
</div>
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Finance</div>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">💰</span> Commissions</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">🔗</span> Referral Links</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">📲</span> Social Media</a>
</div>
@endsection

@section('content')

<!-- KPIs -->
<div class="kpi-grid">
  @foreach([
    ['🏠','28','Active Listings','↑ 3 new this week','up'],
    ['🎯','47','Leads This Month','↑ 12 vs last month','up'],
    ['💰','KSh 842K','Commission Earned','This month (YTD: 6.2M)','up'],
    ['✅','8','Deals Closed','This month · 94 all-time','up'],
    ['🔗','2,847','Referral Clicks','Last 30 days','up'],
    ['⏳','KSh 280K','Pending Commission','3 deals in escrow','up'],
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
  <a href="#" class="btn btn-gold">➕ Create Listing</a>
  <button onclick="showToast('Invite link copied!', 'green')" class="btn btn-outline">📨 Send Client Invite</button>
  <button class="btn btn-outline">🔗 Get Referral Link</button>
  <button class="btn btn-outline">📲 Auto-Post Social</button>
  <button class="btn btn-outline">🤖 AI Description Gen</button>
</div>

<!-- CRM KANBAN -->
<div class="table-card" style="margin-bottom:28px;">
  <div class="table-card-header">
    <div class="table-card-title">🎯 Lead Pipeline — CRM Kanban</div>
    <button class="btn btn-sm btn-outline">+ Add Lead</button>
  </div>
  <div style="padding:20px; overflow-x:auto;">
    <div style="display:grid; grid-template-columns:repeat(5,1fr); gap:14px; min-width:900px;">
      @foreach([
        ['New Lead','#4A9FE0','var(--blue)',[['John K.','4-Bed Villa, Karen','$285K'],['Mary A.','2-Bed Apt','$65K']]],
        ['Viewing Scheduled','#D4A843','var(--gold)',[['James M.','Office Suite','$450K'],['Sarah O.','Land Plot','$85K'],['David W.','Townhouse','$180K']]],
        ['Offer Made','#E8883A','var(--orange)',[['Alice N.','3-Bed Home','$220K']]],
        ['Negotiation','#9B72CF','var(--purple)',[['Peter K.','Villa, Runda','$850K'],['Tom A.','Apt, Westlands','$95K']]],
        ['Closed ✓','#2ECC8A','var(--green)',[['Grace L.','House, Karen','$310K'],['Bill M.','Land, Thika','$42K'],['Ann K.','Office','$380K']]],
      ] as $col)
      <div style="background:var(--navy3); border:1px solid var(--border-dim); border-radius:var(--radius); padding:14px;">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px;">
          <div style="font-size:11px; font-family:var(--font-mono); color:{{ $col[2] }}; letter-spacing:1.5px; text-transform:uppercase; font-weight:600;">{{ $col[0] }}</div>
          <div style="background:var(--surface); border-radius:100px; padding:2px 8px; font-size:11px; color:var(--muted); font-family:var(--font-mono);">{{ count($col[3]) }}</div>
        </div>
        @foreach($col[3] as $lead)
        <div style="background:var(--surface); border:1px solid var(--border-dim); border-radius:var(--radius-sm); padding:12px; margin-bottom:10px; cursor:grab;">
          <div style="font-size:13px; font-weight:600; color:var(--white); margin-bottom:4px;">{{ $lead[0] }}</div>
          <div style="font-size:12px; color:var(--muted);">{{ $lead[1] }}</div>
          <div style="font-family:var(--font-serif); font-size:14px; color:{{ $col[2] }}; margin-top:6px; font-weight:600;">{{ $lead[2] }}</div>
        </div>
        @endforeach
      </div>
      @endforeach
    </div>
  </div>
</div>

<!-- LISTINGS + REFERRAL LINKS -->
<div class="grid-2" style="margin-bottom:28px;">
  <!-- Active Listings -->
  <div class="table-card">
    <div class="table-card-header">
      <div class="table-card-title">🏠 My Active Listings</div>
      <a href="#" class="btn btn-gold btn-sm">+ New Listing</a>
    </div>
    <table class="data-table">
      <thead><tr><th>Property</th><th>Price</th><th>Views</th><th>Leads</th><th>Status</th></tr></thead>
    <tbody>
      @foreach([['🏠','4-Bed Villa, Karen','$285K',847,12,'active'],['🏢','Office Suite, CBD','$450K',632,8,'active'],['🏡','3-Bed Home, Kilimani','$180K',1247,22,'active'],['🌿','Land, Thika','$45K',394,5,'active'],['🏘️','Westlands Apt','$95K',1089,18,'review']] as $l)
      <tr>
        <td><div class="td-name" style="display:flex; align-items:center; gap:6px;"><span>{{ $l[0] }}</span>{{ $l[1] }}</div></td>
        <td class="td-price">{{ $l[2] }}</td>
        <td style="color:var(--blue);">{{ $l[3] }}</td>
        <td style="color:var(--green);">{{ $l[4] }}</td>
        <td><span class="status-pill status-{{ $l[5] }}">{{ strtoupper($l[5]) }}</span></td>
      </tr>
      @endforeach
    </tbody>
    </table>
  </div>

  <!-- Social Media Auto-Post -->
  <div style="background:var(--navy3); border:1px solid var(--border-dim); border-radius:var(--radius); padding:24px;">
    <div class="table-card-title" style="margin-bottom:16px;">📲 Social Media Auto-Post</div>
    <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:20px;">
      @foreach([['📘','Facebook','Connected'],['📷','Instagram','Connected'],['🎵','TikTok','Connect'],['🐦','X (Twitter)','Connected'],['💼','LinkedIn','Connect']] as $sm)
      <div style="background:var(--surface); border:1px solid {{ $sm[2]==='Connected' ? 'rgba(46,204,138,0.3)' : 'var(--border-dim)' }}; border-radius:var(--radius-sm); padding:10px 14px; display:flex; align-items:center; gap:8px;">
        <span>{{ $sm[0] }}</span>
        <div>
          <div style="font-size:12px; font-weight:600; color:var(--white);">{{ $sm[1] }}</div>
          <div style="font-size:10px; color:{{ $sm[2]==='Connected' ? 'var(--green)' : 'var(--muted)' }};">{{ $sm[2] }}</div>
        </div>
      </div>
      @endforeach
    </div>
    <div class="form-group">
      <label class="form-label">Select Listing to Post</label>
      <select class="form-control">
        <option>4-Bed Villa, Karen — $285K</option>
        <option>Office Suite, CBD — $450K</option>
        <option>3-Bed Home, Kilimani — $180K</option>
      </select>
    </div>
    <div class="form-group">
      <label class="form-label">Post Caption (AI-generated)</label>
      <textarea class="form-control" rows="3">🏠 JUST LISTED: Stunning 4-Bedroom Villa in Karen! Private pool, 340m², priced at $285K. ✅ Verified on EstateYard. 🔗 Click link to view & book inspection. #RealEstate #NairobiProperty</textarea>
    </div>
    <div style="display:flex; gap:8px;">
      <button onclick="showToast('Post scheduled for all platforms!', 'green')" class="btn btn-gold" style="flex:1; justify-content:center;">📤 Post Now</button>
      <button class="btn btn-outline btn-sm">📅 Schedule</button>
    </div>
  </div>
</div>

<!-- COMMISSION TRACKER -->
<div class="table-card">
  <div class="table-card-header">
    <div class="table-card-title">💰 Commission Tracker</div>
    <button class="btn btn-sm btn-outline">📊 Full Report</button>
  </div>
  <table class="data-table">
    <thead><tr><th>Deal</th><th>Property Value</th><th>Commission Rate</th><th>Commission</th><th>Referral Share</th><th>Status</th></tr></thead>
    <tbody>
      @foreach([
        ['3-Bed Home, Kilimani','$180,000','3%','$5,400','$1,080','paid'],
        ['4-Bed Villa, Karen','$285,000','3%','$8,550','$1,710','paid'],
        ['Land, Kitengela','$45,000','4%','$1,800','$360','paid'],
        ['Office Suite, CBD','$450,000','2.5%','$11,250','$2,250','pending'],
        ['Westlands Apt','$95,000','3%','$2,850','$570','pending'],
        ['Villa, Muthaiga','$850,000','2%','$17,000','$3,400','escrow'],
      ] as $c)
      <tr>
        <td class="td-name">{{ $c[0] }}</td>
        <td>{{ $c[1] }}</td>
        <td>{{ $c[2] }}</td>
        <td class="td-price">{{ $c[3] }}</td>
        <td style="color:var(--muted);">{{ $c[4] }}</td>
        <td><span class="status-pill status-{{ $c[5] }}">{{ strtoupper($c[5]) }}</span></td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

@endsection
