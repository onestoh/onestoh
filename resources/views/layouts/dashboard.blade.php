<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — TheOnlineYard</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --black: #080C12;
            --dark: #0B1018;
            --surface: #141D2B;
            --amber: #E8922A;
            --text: #DCE5F2;
            --muted: #7088A8;
            --green: #2ECC8A;
            --danger: #E84040;
            --border: #1E2D42;
            --sidebar-width: 260px;
        }
        * { box-sizing: border-box; }
        body { background-color: var(--dark); color: var(--text); font-family: 'Segoe UI', sans-serif; margin: 0; }
        .sidebar {
            position: fixed; top: 0; left: 0; height: 100vh; width: var(--sidebar-width);
            background-color: var(--black); border-right: 1px solid var(--border);
            overflow-y: auto; z-index: 1000; transition: transform 0.3s;
        }
        .sidebar-brand { padding: 1.2rem 1.5rem; border-bottom: 1px solid var(--border); }
        .sidebar-brand a { color: var(--amber); font-weight: 800; font-size: 1.2rem; text-decoration: none; }
        .sidebar-nav { padding: 1rem 0; }
        .sidebar-section { padding: 0.5rem 1.5rem 0.25rem; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; color: var(--muted); font-weight: 600; }
        .sidebar-link { display: flex; align-items: center; padding: 0.6rem 1.5rem; color: var(--muted); text-decoration: none; font-weight: 500; font-size: 0.9rem; transition: all 0.2s; border-left: 3px solid transparent; }
        .sidebar-link:hover, .sidebar-link.active { color: var(--amber); background: rgba(232,146,42,0.08); border-left-color: var(--amber); }
        .sidebar-link i { width: 20px; margin-right: 10px; }
        .topbar {
            position: fixed; top: 0; left: var(--sidebar-width); right: 0; height: 64px;
            background: var(--black); border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 1.5rem; z-index: 999;
        }
        .main-content { margin-left: var(--sidebar-width); margin-top: 64px; padding: 1.5rem; min-height: calc(100vh - 64px); }
        .card { background-color: var(--surface); border: 1px solid var(--border); color: var(--text); }
        .btn-amber { background-color: var(--amber); color: #000; font-weight: 600; border: none; }
        .btn-amber:hover { background-color: #d4811f; color: #000; }
        .text-amber { color: var(--amber) !important; }
        .badge-role { font-size: 0.65rem; padding: 3px 8px; border-radius: 20px; background: rgba(232,146,42,0.15); color: var(--amber); border: 1px solid rgba(232,146,42,0.3); }
        .wallet-badge { background: rgba(46,204,138,0.1); color: var(--green); border: 1px solid rgba(46,204,138,0.3); padding: 4px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; }
        .notification-bell { position: relative; color: var(--muted); cursor: pointer; font-size: 1.1rem; transition: color 0.2s; }
        .notification-bell:hover { color: var(--amber); }
        .notif-dot { position: absolute; top: -3px; right: -3px; width: 8px; height: 8px; background: var(--danger); border-radius: 50%; }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .topbar { left: 0; }
            .main-content { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body x-data="{ sidebarOpen: false }">

    <!-- Sidebar -->
    <aside class="sidebar" :class="{ 'open': sidebarOpen }">
        <div class="sidebar-brand">
            <a href="{{ url('/') }}"><i class="fas fa-warehouse me-2"></i>TheOnlineYard</a>
            <div class="mt-1">
                <span class="badge-role">{{ ucfirst(str_replace('_', ' ', auth()->user()->role ?? 'client')) }}</span>
            </div>
        </div>
        
        <nav class="sidebar-nav">
            <div class="sidebar-section">Main</div>
            <a href="#" class="sidebar-link active"><i class="fas fa-tachometer-alt"></i>Dashboard</a>
            <a href="#" class="sidebar-link"><i class="fas fa-user"></i>My Profile</a>
            <a href="#" class="sidebar-link"><i class="fas fa-wallet"></i>Wallet</a>
            <a href="#" class="sidebar-link"><i class="fas fa-bell"></i>Notifications</a>
            
            @if(auth()->user() && in_array(auth()->user()->role, ['yard_owner','individual_owner','super_admin']))
            <div class="sidebar-section">My Listings</div>
            <a href="#" class="sidebar-link"><i class="fas fa-list"></i>All Listings</a>
            <a href="#" class="sidebar-link"><i class="fas fa-plus"></i>Add Listing</a>
            <a href="#" class="sidebar-link"><i class="fas fa-calendar-check"></i>Availability</a>
            @endif

            @if(auth()->user() && auth()->user()->role === 'broker')
            <div class="sidebar-section">Broker Tools</div>
            <a href="#" class="sidebar-link"><i class="fas fa-handshake"></i>My Deals</a>
            <a href="#" class="sidebar-link"><i class="fas fa-percent"></i>Commissions</a>
            @endif

            <div class="sidebar-section">Bookings</div>
            <a href="#" class="sidebar-link"><i class="fas fa-calendar"></i>My Bookings</a>
            <a href="#" class="sidebar-link"><i class="fas fa-history"></i>Booking History</a>
            
            <div class="sidebar-section">Account</div>
            <a href="#" class="sidebar-link"><i class="fas fa-id-card"></i>KYC Verification</a>
            <a href="#" class="sidebar-link"><i class="fas fa-cog"></i>Settings</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sidebar-link w-100 border-0 text-start" style="background:none;">
                    <i class="fas fa-sign-out-alt"></i>Logout
                </button>
            </form>
        </nav>
    </aside>

    <!-- Top Bar -->
    <header class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn p-0 d-lg-none text-amber" @click="sidebarOpen = !sidebarOpen">
                <i class="fas fa-bars fs-5"></i>
            </button>
            <span class="text-muted" style="font-size:0.85rem;">@yield('page-title', 'Dashboard')</span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="wallet-badge"><i class="fas fa-wallet me-1"></i>KES <span id="walletBalance">0.00</span></span>
            <span class="notification-bell position-relative">
                <i class="fas fa-bell"></i>
                <span class="notif-dot"></span>
            </span>
            <div class="dropdown">
                <button class="btn btn-sm text-light d-flex align-items-center gap-2" data-bs-toggle="dropdown" style="background: var(--surface); border: 1px solid var(--border);">
                    <i class="fas fa-user-circle text-amber"></i>
                    <span style="font-size:0.85rem;">{{ auth()->user()->name ?? 'User' }}</span>
                    <i class="fas fa-chevron-down" style="font-size:0.7rem;"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" style="background: var(--surface); border-color: var(--border);">
                    <li><a class="dropdown-item text-light" href="#"><i class="fas fa-user me-2 text-amber"></i>Profile</a></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger"><i class="fas fa-sign-out-alt me-2"></i>Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
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

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
