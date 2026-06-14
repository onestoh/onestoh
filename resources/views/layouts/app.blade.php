<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TheOnlineYard') — Vehicle & Machinery Rental</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
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
        }
        body { background-color: var(--dark); color: var(--text); font-family: 'Segoe UI', sans-serif; }
        .navbar-brand { color: var(--amber) !important; font-weight: 800; font-size: 1.4rem; letter-spacing: -0.5px; }
        .navbar { background-color: var(--black) !important; border-bottom: 1px solid var(--border); }
        .nav-link { color: var(--muted) !important; font-weight: 500; transition: color 0.2s; }
        .nav-link:hover, .nav-link.active { color: var(--amber) !important; }
        .btn-amber { background-color: var(--amber); color: #000; font-weight: 600; border: none; }
        .btn-amber:hover { background-color: #d4811f; color: #000; }
        .btn-outline-amber { border: 1px solid var(--amber); color: var(--amber); background: transparent; font-weight: 600; }
        .btn-outline-amber:hover { background-color: var(--amber); color: #000; }
        .card { background-color: var(--surface); border: 1px solid var(--border); color: var(--text); }
        footer { background-color: var(--black); border-top: 1px solid var(--border); color: var(--muted); }
        .text-amber { color: var(--amber) !important; }
        .text-muted-custom { color: var(--muted) !important; }
        .bg-surface { background-color: var(--surface); }
        .border-surface { border-color: var(--border) !important; }
        .alert { border-radius: 8px; }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="fas fa-warehouse me-2"></i>{{ \App\Models\PlatformSetting::get('company_name', 'TheOnlineYard') }}
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <i class="fas fa-bars text-amber"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-4">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">
                            <i class="fas fa-store me-1"></i>Marketplace
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-plus-circle me-1"></i>List Vehicle
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-handshake me-1"></i>Become Broker
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-tag me-1"></i>Sell Vehicle
                        </a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-2">
                    @guest
                        <a href="{{ route('login') }}" class="btn btn-outline-amber btn-sm px-3">Login</a>
                        <a href="{{ route('register') }}" class="btn btn-amber btn-sm px-3">Register</a>
                    @else
                        <div class="dropdown">
                            <button class="btn btn-outline-amber btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                @if(auth()->user()->avatar)
                                    <img src="{{ auth()->user()->avatar }}" class="rounded-circle me-1" width="20" height="20">
                                @else
                                    <i class="fas fa-user-circle me-1"></i>
                                @endif
                                {{ auth()->user()->name }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" style="background: var(--surface); border-color: var(--border);">
                                <li><a class="dropdown-item text-light" href="#"><i class="fas fa-tachometer-alt me-2 text-amber"></i>Dashboard</a></li>
                                <li><a class="dropdown-item text-light" href="#"><i class="fas fa-user me-2 text-amber"></i>Profile</a></li>
                                <li><a class="dropdown-item text-light" href="#"><i class="fas fa-wallet me-2 text-amber"></i>Wallet</a></li>
                                <li><hr class="dropdown-divider" style="border-color: var(--border);"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger"><i class="fas fa-sign-out-alt me-2"></i>Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <div class="container mt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-times-circle me-2"></i>
                <ul class="mb-0 mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="py-5 mt-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <h5 class="text-amber fw-bold mb-3"><i class="fas fa-warehouse me-2"></i>TheOnlineYard</h5>
                    <p class="small">Kenya's premier marketplace for vehicle and heavy machinery rental. Connecting owners, operators, and clients across the country.</p>
                </div>
                <div class="col-lg-2">
                    <h6 class="text-light fw-semibold mb-3">Marketplace</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Browse Vehicles</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Heavy Machinery</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Sell Your Asset</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Become a Broker</a></li>
                    </ul>
                </div>
                <div class="col-lg-2">
                    <h6 class="text-light fw-semibold mb-3">Support</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Help Center</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Contact Us</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Terms of Service</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h6 class="text-light fw-semibold mb-3">Stay Connected</h6>
                    <div class="d-flex gap-3 mb-3">
                        <a href="#" class="text-amber fs-5"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-amber fs-5"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-amber fs-5"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-amber fs-5"><i class="fab fa-whatsapp"></i></a>
                    </div>
                    <p class="small text-muted">support@theonlineyard.com</p>
                </div>
            </div>
            <hr style="border-color: var(--border);">
            <div class="text-center small text-muted">
                &copy; {{ date('Y') }} TheOnlineYard. All rights reserved. Built in Kenya
            </div>
        </div>
    </footer>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
