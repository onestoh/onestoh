<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Admin') — TheOnlineYard Control Panel</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Outfit:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
:root{--black:#080C12;--dark:#0B1018;--dark2:#0F1520;--surface:#141D2B;--surface2:#1A2436;--amber:#E8922A;--amber2:#F5B050;--amber-dim:rgba(232,146,42,0.13);--border:rgba(232,146,42,0.14);--text:#DCE5F2;--muted:#7088A8;--green:#2ECC8A;--red:#E05252;--blue:#4A9FE0;--purple:#9B72CF;--sidebar-w:250px;}
*{box-sizing:border-box;}
body{background:var(--black);color:var(--text);font-family:'Outfit',sans-serif;font-size:14px;line-height:1.7;margin:0;}
a{color:var(--amber);text-decoration:none;}
.admin-sidebar{width:var(--sidebar-w);background:var(--dark2);border-right:1px solid var(--border);position:fixed;left:0;top:0;height:100vh;overflow-y:auto;z-index:100;}
.admin-brand{padding:20px 18px;border-bottom:1px solid var(--border);display:block;}
.admin-brand .name{font-family:'Cormorant Garamond',serif;font-size:18px;font-weight:700;color:#fff;}
.admin-brand .name span{color:var(--amber);}
.admin-brand .role{font-size:9px;letter-spacing:2px;text-transform:uppercase;font-family:'JetBrains Mono',monospace;color:var(--muted);margin-top:2px;}
.admin-section{padding:16px 14px 6px;font-size:9px;letter-spacing:3px;text-transform:uppercase;font-family:'JetBrains Mono',monospace;color:var(--muted);}
.admin-nav{list-style:none;padding:0 8px;margin:0;}
.admin-nav li a{display:flex;align-items:center;gap:10px;padding:9px 14px;border-radius:8px;color:var(--muted);font-size:13px;font-weight:500;transition:all .2s;margin-bottom:2px;}
.admin-nav li a:hover,.admin-nav li a.active{background:var(--amber-dim);color:var(--amber);}
.admin-nav li a i{width:16px;text-align:center;}
.admin-nav li a .count{margin-left:auto;background:var(--red);color:#fff;font-size:9px;padding:1px 6px;border-radius:10px;font-family:'JetBrains Mono',monospace;}
.admin-main{margin-left:var(--sidebar-w);min-height:100vh;}
.admin-topbar{background:var(--dark2);border-bottom:1px solid var(--border);padding:0 24px;height:58px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:50;}
.admin-topbar .page-title{font-size:15px;font-weight:600;color:#fff;}
.admin-content{padding:24px;}
.stat-card{background:var(--surface);border:1px solid rgba(255,255,255,0.06);border-radius:12px;padding:20px;position:relative;overflow:hidden;}
.stat-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:var(--card-color, var(--amber));}
.stat-card .val{font-family:'Cormorant Garamond',serif;font-size:30px;font-weight:700;color:#fff;}
.stat-card .label{font-size:11px;color:var(--muted);font-family:'JetBrains Mono',monospace;letter-spacing:1px;text-transform:uppercase;}
.stat-card .icon{position:absolute;right:16px;top:16px;font-size:28px;opacity:0.15;}
.toy-card{background:var(--surface);border:1px solid rgba(255,255,255,0.06);border-radius:12px;padding:20px;}
.toy-card .card-header-dark{background:var(--surface2);margin:-20px -20px 20px;padding:14px 20px;border-bottom:1px solid var(--border);border-radius:12px 12px 0 0;}
.toy-card .card-header-dark h6{font-family:'JetBrains Mono',monospace;font-size:11px;letter-spacing:2px;text-transform:uppercase;color:var(--amber);margin:0;}
.toy-table{width:100%;border-collapse:collapse;}
.toy-table th{background:var(--surface2);color:var(--amber);font-family:'JetBrains Mono',monospace;font-size:10px;letter-spacing:2px;text-transform:uppercase;padding:11px 14px;text-align:left;border-bottom:1px solid var(--border);}
.toy-table td{padding:11px 14px;font-size:13px;color:var(--muted);border-bottom:1px solid rgba(255,255,255,0.03);}
.toy-table tr:hover td{background:rgba(232,146,42,0.03);color:var(--text);}
.badge-amber{background:rgba(232,146,42,0.15);color:var(--amber);padding:2px 8px;border-radius:4px;font-size:10px;font-family:'JetBrains Mono',monospace;}
.badge-green{background:rgba(46,204,138,0.15);color:var(--green);padding:2px 8px;border-radius:4px;font-size:10px;font-family:'JetBrains Mono',monospace;}
.badge-red{background:rgba(224,82,82,0.15);color:var(--red);padding:2px 8px;border-radius:4px;font-size:10px;font-family:'JetBrains Mono',monospace;}
.badge-blue{background:rgba(74,159,224,0.15);color:var(--blue);padding:2px 8px;border-radius:4px;font-size:10px;font-family:'JetBrains Mono',monospace;}
.badge-purple{background:rgba(155,114,207,0.15);color:var(--purple);padding:2px 8px;border-radius:4px;font-size:10px;font-family:'JetBrains Mono',monospace;}
.form-control,.form-select{background:var(--surface2);border:1px solid rgba(255,255,255,0.1);color:var(--text);border-radius:8px;}
.form-control:focus,.form-select:focus{background:var(--surface2);border-color:var(--amber);color:var(--text);box-shadow:0 0 0 3px rgba(232,146,42,0.15);}
.form-control::placeholder{color:var(--muted);}
.form-label{color:var(--muted);font-size:12px;margin-bottom:5px;}
.btn-amber{background:var(--amber)!important;color:var(--black)!important;border:none!important;border-radius:8px;font-weight:600;}
.btn-amber:hover{background:var(--amber2)!important;}
.btn-outline-amber{border:1px solid var(--amber)!important;color:var(--amber)!important;background:transparent!important;border-radius:8px;}
.alert{border-radius:10px;border:none;}
.alert-success{background:rgba(46,204,138,0.1);color:var(--green);border:1px solid rgba(46,204,138,0.3);}
.alert-danger{background:rgba(224,82,82,0.1);color:var(--red);border:1px solid rgba(224,82,82,0.3);}
.alert-warning{background:rgba(232,146,42,0.1);color:var(--amber2);border:1px solid var(--border);}
hr{border-color:var(--border);opacity:1;}
.dropdown-menu{background:var(--surface)!important;border:1px solid var(--border)!important;}
.dropdown-item{color:var(--text)!important;font-size:13px;}
.dropdown-item:hover{background:var(--amber-dim)!important;color:var(--amber)!important;}
</style>
@stack('styles')
</head>
<body>

<aside class="admin-sidebar">
  <div class="admin-brand">
    <div class="name">The<span>Online</span>Yard</div>
    <div class="role">🛡️ Admin Control Panel</div>
  </div>

  <div class="admin-section">Overview</div>
  <ul class="admin-nav">
    <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
  </ul>

  <div class="admin-section">Users</div>
  <ul class="admin-nav">
    <li><a href="{{ route('admin.users.index') }}"><i class="fas fa-users"></i> All Users</a></li>
    <li><a href="{{ route('admin.kyc.index') }}"><i class="fas fa-id-card"></i> KYC Queue</a></li>
    <li><a href="{{ route('admin.yards.index') }}"><i class="fas fa-warehouse"></i> Yards</a></li>
  </ul>

  <div class="admin-section">Marketplace</div>
  <ul class="admin-nav">
    <li><a href="{{ route('admin.listings.index') }}"><i class="fas fa-car"></i> Listings</a></li>
    <li><a href="{{ route('admin.bookings.index') }}"><i class="fas fa-calendar"></i> Bookings</a></li>
    <li><a href="{{ route('admin.disputes.index') }}"><i class="fas fa-gavel"></i> Disputes</a></li>
  </ul>

  <div class="admin-section">Finance</div>
  <ul class="admin-nav">
    <li><a href="{{ route('admin.payments.index') }}"><i class="fas fa-credit-card"></i> Payments</a></li>
    <li><a href="{{ route('admin.payouts.index') }}"><i class="fas fa-money-bill-wave"></i> Payout Requests</a></li>
    <li><a href="{{ route('admin.commissions.index') }}"><i class="fas fa-percentage"></i> Commissions</a></li>
  </ul>

  <div class="admin-section">Config</div>
  <ul class="admin-nav">
    <li><a href="{{ route('admin.categories.index') }}"><i class="fas fa-tags"></i> Categories</a></li>
    <li><a href="{{ route('admin.settings.index') }}"><i class="fas fa-cog"></i> Platform Settings</a></li>
  </ul>

  <div class="p-3 mt-auto">
    <form method="POST" action="{{ route('logout') }}">@csrf
      <button type="submit" class="btn btn-sm w-100" style="border:1px solid var(--red);color:var(--red);background:transparent;border-radius:8px;">
        <i class="fas fa-sign-out-alt me-1"></i> Logout
      </button>
    </form>
  </div>
</aside>

<div class="admin-main">
  <div class="admin-topbar">
    <span class="page-title">@yield('page-title', 'Dashboard')</span>
    <div class="d-flex align-items-center gap-3">
      <a href="{{ route('home') }}" style="color:var(--muted);font-size:13px;"><i class="fas fa-external-link-alt me-1"></i> View Site</a>
      <div class="dropdown">
        <button class="btn btn-sm dropdown-toggle" style="color:var(--text);border:1px solid var(--border);background:var(--surface);border-radius:8px;" data-bs-toggle="dropdown">
          <i class="fas fa-shield-alt me-1" style="color:var(--amber)"></i> {{ auth()->user()->name }}
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
        </ul>
      </div>
    </div>
  </div>

  <div class="admin-content">
    @foreach(['success','error','warning','info'] as $type)
      @if(session($type))
        <div class="alert alert-{{ $type === 'error' ? 'danger' : $type }} alert-dismissible fade show mb-4">
          {{ session($type) }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif
    @endforeach

    @yield('content')
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@stack('scripts')
</body>
</html>
