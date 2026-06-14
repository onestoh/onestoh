<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Dashboard') — TheOnlineYard</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Outfit:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
:root{--black:#080C12;--dark:#0B1018;--dark2:#0F1520;--surface:#141D2B;--surface2:#1A2436;--amber:#E8922A;--amber2:#F5B050;--amber-dim:rgba(232,146,42,0.13);--border:rgba(232,146,42,0.14);--text:#DCE5F2;--muted:#7088A8;--green:#2ECC8A;--red:#E05252;--blue:#4A9FE0;--sidebar-w:260px;}
*{box-sizing:border-box;}
body{background:var(--black);color:var(--text);font-family:'Outfit',sans-serif;font-size:14px;line-height:1.75;margin:0;overflow-x:hidden;}
a{color:var(--amber);text-decoration:none;}a:hover{color:var(--amber2);}
/* Sidebar */
.toy-sidebar{width:var(--sidebar-w);background:var(--dark2);border-right:1px solid var(--border);position:fixed;left:0;top:0;height:100vh;overflow-y:auto;display:flex;flex-direction:column;z-index:100;}
.sidebar-brand{padding:24px 20px;border-bottom:1px solid var(--border);font-family:'Cormorant Garamond',serif;font-size:20px;font-weight:700;color:#fff;display:block;}
.sidebar-brand span{color:var(--amber);}
.sidebar-section{padding:20px 16px 8px;font-size:9px;letter-spacing:3px;text-transform:uppercase;font-family:'JetBrains Mono',monospace;color:var(--muted);}
.sidebar-nav{list-style:none;padding:0 8px;margin:0;}
.sidebar-nav li a{display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:8px;color:var(--muted);font-size:13.5px;font-weight:500;transition:all .2s;margin-bottom:2px;}
.sidebar-nav li a:hover,.sidebar-nav li a.active{background:var(--amber-dim);color:var(--amber);}
.sidebar-nav li a i{width:18px;text-align:center;font-size:14px;}
.sidebar-wallet{margin:16px;background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:16px;}
.sidebar-wallet .label{font-size:10px;letter-spacing:2px;font-family:'JetBrains Mono',monospace;color:var(--muted);text-transform:uppercase;margin-bottom:4px;}
.sidebar-wallet .amount{font-family:'Cormorant Garamond',serif;font-size:26px;font-weight:700;color:var(--amber);}
/* Main */
.toy-main{margin-left:var(--sidebar-w);min-height:100vh;display:flex;flex-direction:column;}
.toy-topbar{background:var(--dark2);border-bottom:1px solid var(--border);padding:0 28px;height:60px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:50;}
.toy-topbar .page-title{font-size:15px;font-weight:600;color:#fff;}
.toy-content{padding:28px;flex:1;}
/* Cards */
.stat-card{background:var(--surface);border:1px solid rgba(255,255,255,0.06);border-radius:12px;padding:22px;border-top:3px solid var(--amber);}
.stat-card .val{font-family:'Cormorant Garamond',serif;font-size:32px;font-weight:700;color:#fff;}
.stat-card .label{font-size:12px;color:var(--muted);font-family:'JetBrains Mono',monospace;letter-spacing:1px;text-transform:uppercase;}
.toy-card{background:var(--surface);border:1px solid rgba(255,255,255,0.06);border-radius:12px;padding:22px;}
/* Table */
.toy-table{width:100%;border-collapse:collapse;}
.toy-table th{background:var(--surface2);color:var(--amber);font-family:'JetBrains Mono',monospace;font-size:10px;letter-spacing:2px;text-transform:uppercase;padding:12px 16px;text-align:left;border-bottom:1px solid var(--border);}
.toy-table td{padding:12px 16px;font-size:13px;color:var(--muted);border-bottom:1px solid rgba(255,255,255,0.03);}
.toy-table tr:hover td{background:rgba(232,146,42,0.03);color:var(--text);}
/* Badges */
.badge-amber{background:rgba(232,146,42,0.15);color:var(--amber);padding:3px 8px;border-radius:4px;font-size:10px;font-family:'JetBrains Mono',monospace;}
.badge-green{background:rgba(46,204,138,0.15);color:var(--green);padding:3px 8px;border-radius:4px;font-size:10px;font-family:'JetBrains Mono',monospace;}
.badge-red{background:rgba(224,82,82,0.15);color:var(--red);padding:3px 8px;border-radius:4px;font-size:10px;font-family:'JetBrains Mono',monospace;}
.badge-blue{background:rgba(74,159,224,0.15);color:var(--blue);padding:3px 8px;border-radius:4px;font-size:10px;font-family:'JetBrains Mono',monospace;}
/* Forms */
.form-control,.form-select{background:var(--surface2);border:1px solid rgba(255,255,255,0.1);color:var(--text);border-radius:8px;}
.form-control:focus,.form-select:focus{background:var(--surface2);border-color:var(--amber);color:var(--text);box-shadow:0 0 0 3px rgba(232,146,42,0.15);}
.form-control::placeholder{color:var(--muted);}
.form-label{color:var(--muted);font-size:13px;margin-bottom:5px;}
.btn-amber{background:var(--amber)!important;color:var(--black)!important;border:none!important;border-radius:8px;font-weight:600;}
.btn-amber:hover{background:var(--amber2)!important;}
.btn-outline-amber{border:1px solid var(--amber)!important;color:var(--amber)!important;background:transparent!important;border-radius:8px;}
.alert{border-radius:10px;border:none;}
.alert-success{background:rgba(46,204,138,0.1);color:var(--green);border:1px solid rgba(46,204,138,0.3);}
.alert-danger{background:rgba(224,82,82,0.1);color:var(--red);border:1px solid rgba(224,82,82,0.3);}
.alert-warning{background:rgba(232,146,42,0.1);color:var(--amber2);border:1px solid var(--border);}
hr{border-color:var(--border);opacity:1;}
.dropdown-menu{background:var(--surface)!important;border:1px solid var(--border)!important;}
.dropdown-item{color:var(--text)!important;}
.dropdown-item:hover{background:var(--amber-dim)!important;color:var(--amber)!important;}
@media(max-width:991px){.toy-sidebar{transform:translateX(-100%);transition:transform .3s;}.toy-sidebar.open{transform:translateX(0);}.toy-main{margin-left:0;}}
</style>
@stack('styles')
</head>
<body>

<!-- SIDEBAR -->
<aside class="toy-sidebar" id="sidebar">
  <a href="{{ route('home') }}" class="sidebar-brand">The<span>Online</span>Yard</a>

  @if(auth()->user()->wallet)
    <div class="sidebar-wallet">
      <div class="label">Wallet Balance</div>
      <div class="amount">KES {{ number_format(auth()->user()->wallet->balance, 0) }}</div>
      <a href="{{ route('wallet.index') }}" class="btn btn-amber btn-sm mt-2 w-100">Withdraw</a>
    </div>
  @endif

  <div class="sidebar-section">Main</div>
  <ul class="sidebar-nav">
    <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
      <i class="fas fa-tachometer-alt"></i> Dashboard
    </a></li>
    <li><a href="{{ route('profile.edit') }}">
      <i class="fas fa-user-circle"></i> My Profile
    </a></li>
    <li><a href="{{ route('kyc.index') }}">
      <i class="fas fa-id-card"></i> KYC Documents
    </a></li>
  </ul>

  @if(auth()->user()->isYardOwner())
    <div class="sidebar-section">Fleet</div>
    <ul class="sidebar-nav">
      <li><a href="{{ route('listings.index') }}"><i class="fas fa-car"></i> My Listings</a></li>
      <li><a href="{{ route('listings.create') }}"><i class="fas fa-plus-circle"></i> Add Listing</a></li>
      <li><a href="{{ route('yard.index') }}"><i class="fas fa-warehouse"></i> My Yard</a></li>
      <li><a href="{{ route('yard.bookings') }}"><i class="fas fa-calendar-check"></i> Bookings</a></li>
      <li><a href="{{ route('yard.drivers') }}"><i class="fas fa-user-tie"></i> Drivers</a></li>
      <li><a href="{{ route('yard.analytics') }}"><i class="fas fa-chart-bar"></i> Analytics</a></li>
    </ul>
  @endif

  @if(auth()->user()->isClient())
    <div class="sidebar-section">Bookings</div>
    <ul class="sidebar-nav">
      <li><a href="{{ route('client.bookings') }}"><i class="fas fa-list"></i> My Bookings</a></li>
      <li><a href="{{ route('marketplace') }}"><i class="fas fa-search"></i> Browse Listings</a></li>
      <li><a href="{{ route('client.saved') }}"><i class="fas fa-heart"></i> Saved Listings</a></li>
    </ul>
  @endif

  @if(auth()->user()->isBroker())
    <div class="sidebar-section">Referrals</div>
    <ul class="sidebar-nav">
      <li><a href="{{ route('broker.dashboard') }}"><i class="fas fa-link"></i> My Referrals</a></li>
      <li><a href="{{ route('broker.commissions') }}"><i class="fas fa-coins"></i> Commissions</a></li>
    </ul>
  @endif

  @if(auth()->user()->isOperator())
    <div class="sidebar-section">Assignments</div>
    <ul class="sidebar-nav">
      <li><a href="{{ route('operator.assignments') }}"><i class="fas fa-tasks"></i> My Assignments</a></li>
    </ul>
  @endif

  <div class="sidebar-section">Account</div>
  <ul class="sidebar-nav">
    <li><a href="{{ route('wallet.index') }}"><i class="fas fa-wallet"></i> Wallet</a></li>
    <li><a href="{{ route('messages.index') }}"><i class="fas fa-comments"></i> Messages</a></li>
    <li><a href="{{ route('notifications.index') }}"><i class="fas fa-bell"></i> Notifications</a></li>
  </ul>

  <div class="mt-auto p-3">
    <form method="POST" action="{{ route('logout') }}">@csrf
      <button type="submit" class="btn btn-sm w-100" style="border:1px solid var(--red);color:var(--red);background:transparent;border-radius:8px;">
        <i class="fas fa-sign-out-alt me-1"></i> Logout
      </button>
    </form>
  </div>
</aside>

<!-- MAIN -->
<div class="toy-main">
  <!-- TOP BAR -->
  <div class="toy-topbar">
    <div class="d-flex align-items-center gap-3">
      <button class="d-lg-none btn btn-sm" style="color:var(--muted);border:none;background:none;" onclick="document.getElementById('sidebar').classList.toggle('open')">
        <i class="fas fa-bars fa-lg"></i>
      </button>
      <span class="page-title">@yield('page-title', 'Dashboard')</span>
    </div>
    <div class="d-flex align-items-center gap-3">
      <a href="{{ route('notifications.index') }}" style="color:var(--muted);">
        <i class="fas fa-bell"></i>
      </a>
      <div class="dropdown">
        <button class="btn btn-sm dropdown-toggle" style="color:var(--text);border:1px solid var(--border);background:var(--surface);border-radius:8px;" data-bs-toggle="dropdown">
          <i class="fas fa-user-circle me-1" style="color:var(--amber)"></i> {{ auth()->user()->name }}
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fas fa-user me-2" style="color:var(--amber)"></i> Profile</a></li>
          <li><a class="dropdown-item" href="{{ route('home') }}"><i class="fas fa-home me-2" style="color:var(--amber)"></i> Main Site</a></li>
        </ul>
      </div>
    </div>
  </div>

  <!-- CONTENT -->
  <div class="toy-content">
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
