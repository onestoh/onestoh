<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Dashboard') — EstateYard ERP</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Outfit:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="{{ asset('css/estateyard.css') }}">

  @stack('styles')
</head>
<body>

<div class="dash-layout">

  <!-- SIDEBAR -->
  <aside class="dash-sidebar" id="dashSidebar">
    <div class="dash-sidebar-header">
      <a href="{{ url('/') }}" class="dash-brand">Estate<span>Yard</span></a>
      <div class="dash-role-badge">{{ strtoupper(session('role', 'USER')) }}</div>
    </div>

    <nav class="dash-nav">
      @yield('sidebar-nav')
    </nav>

    <div class="dash-sidebar-footer">
      <div class="dash-user">
        <div class="dash-avatar">👤</div>
        <div>
          <div class="dash-user-name">{{ session('user_name', 'User') }}</div>
          <div class="dash-user-role">{{ session('role', 'Member') }}</div>
        </div>
      </div>
      <div style="margin-top:12px; display:flex; gap:8px;">
        <a href="{{ url('/dashboard/settings') }}" class="btn btn-ghost btn-sm" style="flex:1; justify-content:center;">⚙️ Settings</a>
        <a href="{{ url('/logout') }}" class="btn btn-ghost btn-sm" style="justify-content:center;">↩</a>
      </div>
    </div>
  </aside>

  <!-- MAIN -->
  <div class="dash-main">

    <!-- TOPBAR -->
    <header class="dash-topbar">
      <div style="display:flex; align-items:center; gap:16px;">
        <button onclick="toggleSidebar()" class="icon-btn" style="display:none;" id="sidebarToggle">☰</button>
        <div>
          <div class="dash-topbar-title">@yield('page-title', 'Dashboard')</div>
          <div style="font-size:12px; color:var(--muted); font-family:var(--font-mono);">@yield('page-subtitle', '')</div>
        </div>
      </div>

      <div class="dash-topbar-actions">
        <!-- Search -->
        <div style="position:relative; display:none;" class="show-desktop">
          <input type="text" placeholder="Search..." style="background:var(--navy3); border:1px solid var(--border-dim); border-radius:8px; padding:8px 16px 8px 36px; color:var(--text); font-size:13px; outline:none; width:220px;">
          <span style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--muted); font-size:14px;">🔍</span>
        </div>

        <!-- Notifications -->
        <div style="position:relative;">
          <button class="icon-btn" onclick="toggleNotifs()">🔔</button>
          @if(($unreadNotifCount ?? 0) > 0)
          <span style="position:absolute; top:-4px; right:-4px; background:var(--red); color:white; border-radius:50%; width:18px; height:18px; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:600;">{{ $unreadNotifCount > 9 ? '9+' : $unreadNotifCount }}</span>
          @endif

          <!-- Notification Dropdown -->
          <div id="notifDropdown" style="display:none; position:absolute; right:0; top:50px; width:340px; background:var(--navy2); border:1px solid var(--border); border-radius:var(--radius); box-shadow:var(--shadow); z-index:300;">
            <div style="padding:16px 20px; border-bottom:1px solid var(--border-dim); display:flex; justify-content:space-between; align-items:center;">
              <span style="font-size:14px; font-weight:600; color:var(--white);">Notifications</span>
              <button onclick="document.getElementById('notifDropdown').style.display='none'" style="background:none; border:none; color:var(--muted); cursor:pointer;">✕</button>
            </div>
            <div style="max-height:320px; overflow-y:auto;">
              @php
                $recentNotifs = [];
                if (session('user_id')) {
                    try {
                        $recentNotifs = \App\Models\NotificationLog::where('user_id', session('user_id'))
                            ->latest()->take(3)->get();
                    } catch (\Throwable $e) {}
                }
                $typeIcons = ['payment'=>'💳','inspection'=>'🏠','document'=>'✅','auction'=>'🔨','message'=>'💬','system'=>'🔔'];
              @endphp
              @forelse($recentNotifs as $notif)
              <div style="padding:14px 20px; border-bottom:1px solid var(--border-dim); display:flex; gap:12px; cursor:pointer; transition:background .2s;{{ !$notif->is_read ? 'background:rgba(201,168,76,0.04);' : '' }}" onmouseover="this.style.background='var(--gold-dim)'" onmouseout="this.style.background='{{ !$notif->is_read ? 'rgba(201,168,76,0.04)' : 'transparent' }}'">
                <span style="font-size:20px; flex-shrink:0; margin-top:2px;">{{ $typeIcons[$notif->type] ?? '🔔' }}</span>
                <div>
                  <div style="font-size:13px; color:var(--white); font-weight:{{ $notif->is_read ? '400' : '600' }};">{{ $notif->title }}</div>
                  <div style="font-size:12px; color:var(--muted); margin-top:2px; line-height:1.4;">{{ Str::limit($notif->body, 60) }}</div>
                  <div style="font-size:11px; color:var(--muted); margin-top:2px; font-family:var(--font-mono);">{{ $notif->created_at?->diffForHumans() }}</div>
                </div>
              </div>
              @empty
              <div style="padding:24px 20px; text-align:center; color:var(--muted); font-size:13px;">No notifications yet</div>
              @endforelse
            </div>
            <div style="padding:12px 20px; text-align:center; border-top:1px solid var(--border-dim);">
              <a href="{{ url('/dashboard/notifications') }}" style="font-size:13px; color:var(--gold);">View all notifications</a>
            </div>
          </div>
        </div>

        <!-- Messages -->
        <a href="{{ url('/dashboard/messages') }}" class="icon-btn" style="position:relative;">
          💬
          <span style="position:absolute; top:-4px; right:-4px; background:var(--gold); color:var(--navy); border-radius:50%; width:18px; height:18px; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:700;">5</span>
        </a>

        <!-- User Menu -->
        <div style="position:relative;">
          <button onclick="toggleUserMenu()" style="display:flex; align-items:center; gap:10px; background:var(--navy3); border:1px solid var(--border-dim); border-radius:var(--radius-sm); padding:8px 14px; cursor:pointer; color:var(--text); font-family:var(--font-sans);">
            <div style="width:30px; height:30px; border-radius:50%; background:var(--gold-dim); border:1px solid var(--gold); display:flex; align-items:center; justify-content:center; font-size:14px;">👤</div>
            <span style="font-size:13px; font-weight:500;">{{ session('user_name', 'User') }}</span>
            <span style="color:var(--muted); font-size:10px;">▼</span>
          </button>

          <div id="userMenu" style="display:none; position:absolute; right:0; top:50px; width:200px; background:var(--navy2); border:1px solid var(--border); border-radius:var(--radius); box-shadow:var(--shadow); z-index:300;">
            <a href="{{ url('/dashboard/profile') }}" style="display:flex; gap:10px; align-items:center; padding:12px 16px; color:var(--muted); font-size:13px; border-bottom:1px solid var(--border-dim); transition:all .2s;" onmouseover="this.style.color='var(--gold)'" onmouseout="this.style.color='var(--muted)'">👤 My Profile</a>
            <a href="{{ url('/dashboard/settings') }}" style="display:flex; gap:10px; align-items:center; padding:12px 16px; color:var(--muted); font-size:13px; border-bottom:1px solid var(--border-dim); transition:all .2s;" onmouseover="this.style.color='var(--gold)'" onmouseout="this.style.color='var(--muted)'">⚙️ Settings</a>
            <a href="{{ url('/dashboard/verification') }}" style="display:flex; gap:10px; align-items:center; padding:12px 16px; color:var(--muted); font-size:13px; border-bottom:1px solid var(--border-dim); transition:all .2s;" onmouseover="this.style.color='var(--gold)'" onmouseout="this.style.color='var(--muted)'">✅ Verification</a>
            <a href="{{ url('/logout') }}" style="display:flex; gap:10px; align-items:center; padding:12px 16px; color:var(--red); font-size:13px;" onmouseover="this.style.background='rgba(224,82,82,0.08)'" onmouseout="this.style.background='transparent'">↩ Sign Out</a>
          </div>
        </div>
      </div>
    </header>

    <!-- PAGE CONTENT -->
    <main class="dash-content">
      @if(session('success'))
        <div class="alert alert-green" style="margin-bottom:24px;">
          ✅ {{ session('success') }}
        </div>
      @endif

      @if(session('error'))
        <div class="alert alert-red" style="margin-bottom:24px;">
          ⚠️ {{ session('error') }}
        </div>
      @endif

      @yield('content')
    </main>

  </div>
</div>

<!-- Overlay for sidebar on mobile -->
<div class="nav-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')

<script>
  function toggleSidebar() {
    const s = document.getElementById('dashSidebar');
    const o = document.getElementById('sidebarOverlay');
    s.classList.toggle('open');
    o.classList.toggle('open');
  }

  function closeSidebar() {
    document.getElementById('dashSidebar').classList.remove('open');
    document.getElementById('sidebarOverlay').classList.remove('open');
  }

  function toggleNotifs() {
    const d = document.getElementById('notifDropdown');
    const u = document.getElementById('userMenu');
    u.style.display = 'none';
    d.style.display = d.style.display === 'none' ? 'block' : 'none';
  }

  function toggleUserMenu() {
    const u = document.getElementById('userMenu');
    const d = document.getElementById('notifDropdown');
    d.style.display = 'none';
    u.style.display = u.style.display === 'none' ? 'block' : 'none';
  }

  // Show toggle on mobile
  if (window.innerWidth <= 1024) {
    document.getElementById('sidebarToggle').style.display = 'flex';
  }

  // Close dropdowns on outside click
  document.addEventListener('click', (e) => {
    if (!e.target.closest('#notifDropdown') && !e.target.closest('[onclick="toggleNotifs()"]')) {
      const d = document.getElementById('notifDropdown');
      if (d) d.style.display = 'none';
    }
    if (!e.target.closest('#userMenu') && !e.target.closest('[onclick="toggleUserMenu()"]')) {
      const u = document.getElementById('userMenu');
      if (u) u.style.display = 'none';
    }
  });
</script>

</body>
</html>
