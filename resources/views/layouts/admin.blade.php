<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin — @yield('title', 'Dashboard') | {{ \App\Models\PlatformSetting::get('company_name', 'TheOnlineYard') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    @php
    $primaryColor   = \App\Models\PlatformSetting::get('primary_color', '#E8922A');
    $secondaryColor = \App\Models\PlatformSetting::get('secondary_color', '#2ECC8A');
    $gradientStart  = \App\Models\PlatformSetting::get('gradient_start', '#E8922A');
    $gradientEnd    = \App\Models\PlatformSetting::get('gradient_end', '#E84040');
    @endphp
    <style>
        :root {
            --black: #080C12;
            --dark: #0B1018;
            --surface: #141D2B;
            --amber: {{ $primaryColor }};
            --text: #DCE5F2;
            --muted: #7088A8;
            --green: {{ $secondaryColor }};
            --danger: #E84040;
            --border: #1E2D42;
            --gradient: linear-gradient(135deg, {{ $gradientStart }}, {{ $gradientEnd }});
            --sidebar-width: 240px;
        }
        body { background: var(--dark); color: var(--text); font-family: 'Segoe UI', sans-serif; margin: 0; }
        .admin-sidebar {
            position: fixed; top: 0; left: 0; height: 100vh; width: var(--sidebar-width);
            background: var(--black); border-right: 1px solid var(--border);
            overflow-y: auto; z-index: 1000;
        }
        .admin-sidebar .brand { padding: 1rem 1.5rem; border-bottom: 1px solid var(--border); }
        .admin-sidebar .brand a { color: var(--amber); font-weight: 800; font-size: 1.1rem; text-decoration: none; }
        .admin-sidebar .brand small { display: block; color: var(--danger); font-size: 0.65rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; }
        .admin-link { display: flex; align-items: center; padding: 0.55rem 1.5rem; color: var(--muted); text-decoration: none; font-size: 0.875rem; font-weight: 500; border-left: 3px solid transparent; transition: all 0.2s; }
        .admin-link:hover, .admin-link.active { color: var(--amber); background: rgba(232,146,42,0.08); border-left-color: var(--amber); }
        .admin-link i { width: 18px; margin-right: 10px; font-size: 0.85rem; }
        .section-label { padding: 0.75rem 1.5rem 0.25rem; font-size: 0.65rem; text-transform: uppercase; letter-spacing: 1.5px; color: var(--muted); font-weight: 700; }
        .admin-topbar {
            position: fixed; top: 0; left: var(--sidebar-width); right: 0; height: 60px;
            background: var(--black); border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 1.5rem; z-index: 999;
        }
        .stats-bar { display: flex; gap: 1rem; align-items: center; overflow-x: auto; }
        .stat-chip { background: var(--surface); border: 1px solid var(--border); border-radius: 8px; padding: 3px 12px; font-size: 0.78rem; white-space: nowrap; }
        .stat-chip .value { color: var(--amber); font-weight: 700; }
        .admin-main { margin-left: var(--sidebar-width); margin-top: 60px; padding: 1.5rem; min-height: calc(100vh - 60px); }
        .card { background: var(--surface); border: 1px solid var(--border); color: var(--text); }
        .card-header { background: rgba(0,0,0,0.2); border-bottom: 1px solid var(--border); }
        .btn-amber { background: var(--amber); color: #000; font-weight: 600; border: none; }
        .text-amber { color: var(--amber) !important; }
        .text-green { color: var(--green) !important; }
        .badge-pending { background: rgba(232,146,42,0.15); color: var(--amber); border: 1px solid rgba(232,146,42,0.3); }
        .badge-active { background: rgba(46,204,138,0.15); color: var(--green); border: 1px solid rgba(46,204,138,0.3); }
        .badge-suspended { background: rgba(232,64,64,0.15); color: var(--danger); border: 1px solid rgba(232,64,64,0.3); }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Admin Sidebar -->
    <aside class="admin-sidebar">
        <div class="brand">
            <a href="#"><i class="fas fa-warehouse me-2"></i>{{ \App\Models\PlatformSetting::get('company_name', 'TheOnlineYard') }}</a>
            <small><i class="fas fa-shield-alt me-1"></i>Admin Panel</small>
        </div>
        
        <div class="section-label">Overview</div>
        <a href="{{ route('admin.dashboard') }}" class="admin-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="fas fa-tachometer-alt"></i>Dashboard</a>

        <div class="section-label">Management</div>
        <a href="{{ route('admin.users.index') }}" class="admin-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}"><i class="fas fa-users"></i>Users</a>
        <a href="{{ route('admin.yards.index') }}" class="admin-link {{ request()->routeIs('admin.yards*') ? 'active' : '' }}"><i class="fas fa-warehouse"></i>Yards</a>
        <a href="{{ route('admin.listings.index') }}" class="admin-link {{ request()->routeIs('admin.listings*') ? 'active' : '' }}"><i class="fas fa-car"></i>Listings</a>
        <a href="{{ route('admin.kyc.index') }}" class="admin-link {{ request()->routeIs('admin.kyc*') ? 'active' : '' }}"><i class="fas fa-id-card"></i>KYC Reviews</a>
        <a href="{{ route('admin.categories.index') }}" class="admin-link {{ request()->routeIs('admin.categories*') ? 'active' : '' }}"><i class="fas fa-tags"></i>Categories</a>

        <div class="section-label">Transactions</div>
        <a href="{{ route('admin.bookings.index') }}" class="admin-link {{ request()->routeIs('admin.bookings*') ? 'active' : '' }}"><i class="fas fa-calendar-check"></i>Bookings</a>
        <a href="{{ route('admin.payments.index') }}" class="admin-link {{ request()->routeIs('admin.payments*') ? 'active' : '' }}"><i class="fas fa-credit-card"></i>Payments</a>
        <a href="{{ route('admin.payouts.index') }}" class="admin-link {{ request()->routeIs('admin.payouts*') ? 'active' : '' }}"><i class="fas fa-money-bill-wave"></i>Payouts</a>
        <a href="{{ route('admin.commissions.index') }}" class="admin-link {{ request()->routeIs('admin.commissions*') ? 'active' : '' }}"><i class="fas fa-percent"></i>Commissions</a>

        <div class="section-label">Support</div>
        <a href="{{ route('admin.disputes.index') }}" class="admin-link {{ request()->routeIs('admin.disputes*') ? 'active' : '' }}"><i class="fas fa-exclamation-triangle"></i>Disputes</a>

        <div class="section-label">System</div>
        <a href="{{ route('admin.settings.index') }}" class="admin-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}"><i class="fas fa-cog"></i>Settings</a>
        <form method="POST" action="{{ route('logout') }}" class="mt-2">
            @csrf
            <button type="submit" class="admin-link w-100 border-0 text-start" style="background:none;">
                <i class="fas fa-sign-out-alt"></i>Logout
            </button>
        </form>
    </aside>

    <!-- Top Bar -->
    <header class="admin-topbar">
        <div class="stats-bar">
            <span class="stat-chip">Users: <span class="value" id="statUsers">—</span></span>
            <span class="stat-chip">Listings: <span class="value" id="statListings">—</span></span>
            <span class="stat-chip">Bookings: <span class="value" id="statBookings">—</span></span>
            <span class="stat-chip">Revenue: <span class="value" id="statRevenue">—</span></span>
            <span class="stat-chip">Disputes: <span class="value" id="statDisputes">—</span></span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span style="font-size:0.8rem; color: var(--muted);">{{ auth()->user()->name ?? 'Admin' }}</span>
            <span style="font-size:0.75rem; background: rgba(232,64,64,0.15); color: var(--danger); border: 1px solid rgba(232,64,64,0.3); padding: 2px 8px; border-radius: 20px;">Super Admin</span>
        </div>
    </header>

    <!-- Main -->
    <main class="admin-main">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-3">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
