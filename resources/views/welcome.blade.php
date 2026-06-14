<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TheOnlineYard — Kenya's Premier Vehicle & Machinery Rental Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --black: #080C12;
            --surface: #141D2B;
            --amber: #E8922A;
            --text: #DCE5F2;
            --muted: #7088A8;
            --green: #2ECC8A;
            --border: #1E2D42;
            --danger: #E84040;
        }
        * { box-sizing: border-box; }
        body {
            background: var(--black);
            color: var(--text);
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
        }

        /* ---- Navbar ---- */
        .navbar-custom {
            background: rgba(8,12,18,.96);
            border-bottom: 1px solid var(--border);
            backdrop-filter: blur(10px);
            padding: .75rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .navbar-brand-name {
            font-weight: 800;
            font-size: 1.3rem;
            color: var(--amber) !important;
            text-decoration: none;
        }
        .nav-lnk {
            color: var(--muted);
            font-size: .875rem;
            font-weight: 500;
            text-decoration: none;
            transition: color .2s;
        }
        .nav-lnk:hover { color: var(--text); }
        .btn-amber {
            background: var(--amber);
            color: #000;
            font-weight: 700;
            border: none;
            transition: opacity .2s;
        }
        .btn-amber:hover { opacity: .9; color: #000; }
        .btn-outline-amber {
            border: 1.5px solid var(--amber);
            color: var(--amber);
            background: transparent;
            font-weight: 600;
            transition: all .2s;
        }
        .btn-outline-amber:hover { background: var(--amber); color: #000; }

        /* ---- Hero ---- */
        .hero-section {
            min-height: 88vh;
            background: linear-gradient(135deg, var(--black) 0%, #0d1520 50%, #0a1118 100%);
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            padding: 80px 0 60px;
        }
        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(232,146,42,.08) 0%, transparent 70%);
            pointer-events: none;
        }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            background: rgba(232,146,42,.1);
            border: 1px solid rgba(232,146,42,.25);
            color: var(--amber);
            border-radius: 20px;
            padding: .3rem .9rem;
            font-size: .78rem;
            font-weight: 700;
            letter-spacing: .5px;
            text-transform: uppercase;
            margin-bottom: 1.25rem;
        }
        .hero-title {
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 800;
            line-height: 1.1;
            color: var(--text);
            margin-bottom: 1.25rem;
        }
        .hero-title span { color: var(--amber); }
        .hero-subtitle {
            font-size: 1.1rem;
            color: var(--muted);
            line-height: 1.7;
            max-width: 550px;
            margin-bottom: 2.5rem;
        }
        .hero-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: 1fr 1fr;
            gap: .75rem;
            height: 420px;
        }
        .hero-grid-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: .5rem;
            color: var(--muted);
            font-size: .85rem;
        }
        .hero-grid-card i { font-size: 2.5rem; color: var(--amber); opacity: .6; }
        .hero-grid-card:first-child { grid-row: span 2; }

        /* ---- Stats ---- */
        .stats-bar {
            background: var(--surface);
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            padding: 1.5rem 0;
        }
        .stat-number { font-size: 1.8rem; font-weight: 800; color: var(--amber); display: block; line-height: 1; }
        .stat-label { font-size: .8rem; color: var(--muted); text-transform: uppercase; letter-spacing: .5px; margin-top: .3rem; }

        /* ---- Sections ---- */
        .section-eyebrow { font-size: .75rem; text-transform: uppercase; letter-spacing: 2px; color: var(--amber); font-weight: 700; margin-bottom: .5rem; }
        .section-title { font-size: clamp(1.5rem, 3vw, 2.2rem); font-weight: 800; color: var(--text); }
        .section-subtitle { color: var(--muted); font-size: 1rem; margin-top: .5rem; }

        /* ---- Category Cards ---- */
        .category-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.75rem 1.25rem;
            text-align: center;
            text-decoration: none;
            color: var(--text);
            display: block;
            transition: all .25s;
        }
        .category-card:hover {
            border-color: var(--amber);
            background: rgba(232,146,42,.06);
            transform: translateY(-3px);
            color: var(--text);
        }
        .cat-icon {
            width: 56px;
            height: 56px;
            background: rgba(232,146,42,.12);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: var(--amber);
            font-size: 1.4rem;
        }
        .cat-name { font-weight: 700; font-size: .95rem; margin-bottom: .25rem; }
        .cat-count { font-size: .8rem; color: var(--muted); }

        /* ---- How It Works ---- */
        .how-section {
            background: var(--surface);
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
        }
        .step-number {
            width: 48px;
            height: 48px;
            background: var(--amber);
            color: #000;
            font-weight: 900;
            font-size: 1.2rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
        }
        .step-title { font-weight: 700; font-size: 1.05rem; margin-bottom: .5rem; color: var(--text); }
        .step-desc { color: var(--muted); font-size: .9rem; line-height: 1.6; }

        /* ---- Listing Cards ---- */
        .listing-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
            transition: all .25s;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .listing-card:hover { border-color: rgba(232,146,42,.4); transform: translateY(-2px); }
        .listing-photo {
            height: 180px;
            background: linear-gradient(135deg, #141D2B, #1E2D42);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--muted);
            font-size: 2.5rem;
            overflow: hidden;
        }
        .listing-photo img { width: 100%; height: 100%; object-fit: cover; }
        .listing-body { padding: 1rem; flex: 1; display: flex; flex-direction: column; }
        .listing-badge {
            display: inline-block;
            padding: .2rem .6rem;
            background: rgba(232,146,42,.1);
            border: 1px solid rgba(232,146,42,.2);
            color: var(--amber);
            border-radius: 4px;
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: .5rem;
        }
        .listing-title { font-weight: 700; font-size: .95rem; color: var(--text); margin-bottom: .5rem; line-height: 1.3; }
        .listing-price { font-size: 1.05rem; font-weight: 800; color: var(--green); }
        .listing-owner { font-size: .8rem; color: var(--muted); margin-top: .5rem; }

        /* ---- CTA ---- */
        .cta-section {
            background: linear-gradient(135deg, rgba(232,146,42,.12) 0%, rgba(232,146,42,.04) 100%);
            border-top: 1px solid rgba(232,146,42,.2);
            border-bottom: 1px solid rgba(232,146,42,.2);
            text-align: center;
            padding: 80px 0;
        }
        .cta-section h2 { font-size: clamp(1.5rem,3vw,2.5rem); font-weight: 800; color: var(--text); }
        .cta-section p { color: var(--muted); font-size: 1.05rem; max-width: 500px; margin: 1rem auto 2rem; }

        /* ---- Footer ---- */
        footer {
            background: var(--black);
            border-top: 1px solid var(--border);
            padding: 2.5rem 0;
            color: var(--muted);
            font-size: .875rem;
        }
        footer a { color: var(--muted); text-decoration: none; }
        footer a:hover { color: var(--amber); }
        .footer-brand { color: var(--amber); font-weight: 800; font-size: 1.1rem; }

        [x-cloak] { display: none !important; }
        .text-amber { color: var(--amber) !important; }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar-custom" x-data="{ open: false }">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between">
            <a href="{{ route('home') }}" class="navbar-brand-name">
                <i class="fas fa-warehouse me-2"></i>TheOnlineYard
            </a>

            <!-- Desktop links -->
            <div class="d-none d-md-flex align-items-center gap-4">
                <a href="{{ route('marketplace') }}" class="nav-lnk">Browse</a>
                <a href="#how-it-works" class="nav-lnk">How it Works</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="nav-lnk">Dashboard</a>
                    <a href="{{ route('listings.create') }}" class="btn btn-sm btn-amber rounded-pill px-3">List Your Asset</a>
                @else
                    <a href="{{ route('login') }}" class="nav-lnk">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-sm btn-amber rounded-pill px-3">Register</a>
                @endauth
            </div>

            <!-- Mobile toggle -->
            <button class="d-md-none btn p-1 px-2" style="color:var(--muted);border:1px solid var(--border)" @click="open=!open">
                <i class="fas" :class="open ? 'fa-times' : 'fa-bars'"></i>
            </button>
        </div>

        <!-- Mobile menu -->
        <div class="d-md-none mt-3 pb-2" x-show="open" x-cloak x-transition>
            <div class="d-flex flex-column gap-2">
                <a href="{{ route('marketplace') }}" class="nav-lnk py-1">Browse</a>
                <a href="#how-it-works" class="nav-lnk py-1">How it Works</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="nav-lnk py-1">Dashboard</a>
                    <a href="{{ route('listings.create') }}" class="btn btn-amber w-100 mt-1">List Your Asset</a>
                @else
                    <a href="{{ route('login') }}" class="nav-lnk py-1">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-amber w-100 mt-1">Register Free</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<!-- HERO -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="hero-badge">
                    <i class="fas fa-map-marker-alt"></i> Kenya's #1 Rental Platform
                </div>
                <h1 class="hero-title">
                    Kenya's Premier<br>
                    <span>Vehicle & Machinery</span><br>
                    Rental Platform
                </h1>
                <p class="hero-subtitle">
                    Find trucks, excavators, cranes and more — with secure M-Pesa payments and verified owners. Book in minutes, work tomorrow.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('marketplace') }}" class="btn btn-amber btn-lg px-4">
                        <i class="fas fa-search me-2"></i>Browse Listings
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-outline-amber btn-lg px-4">
                        <i class="fas fa-plus me-2"></i>List Your Asset
                    </a>
                </div>
                <div class="d-flex flex-wrap gap-3 mt-4">
                    <span style="font-size:.8rem;color:var(--muted)"><i class="fas fa-shield-alt text-amber me-1"></i> Verified owners</span>
                    <span style="font-size:.8rem;color:var(--muted)"><i class="fas fa-mobile-alt text-amber me-1"></i> M-Pesa payments</span>
                    <span style="font-size:.8rem;color:var(--muted)"><i class="fas fa-headset text-amber me-1"></i> 24/7 support</span>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-block">
                <div class="hero-grid">
                    <div class="hero-grid-card">
                        <i class="fas fa-truck-moving"></i>
                        <span>Trucks & Lorries</span>
                    </div>
                    <div class="hero-grid-card">
                        <i class="fas fa-tractor"></i>
                        <span>Heavy Machinery</span>
                    </div>
                    <div class="hero-grid-card">
                        <i class="fas fa-car"></i>
                        <span>Cars & SUVs</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- STATS BAR -->
<section class="stats-bar">
    <div class="container">
        <div class="row g-3 text-center">
            <div class="col-6 col-md-3">
                <span class="stat-number">{{ isset($stats) ? number_format($stats['total_listings']) . '+' : '500+' }}</span>
                <div class="stat-label">Vehicles & Machines</div>
            </div>
            <div class="col-6 col-md-3">
                <span class="stat-number">{{ isset($stats) ? number_format($stats['total_yards']) . '+' : '200+' }}</span>
                <div class="stat-label">Verified Owners</div>
            </div>
            <div class="col-6 col-md-3">
                <span class="stat-number">{{ isset($stats) ? number_format($stats['total_bookings']) . '+' : '1,000+' }}</span>
                <div class="stat-label">Bookings Completed</div>
            </div>
            <div class="col-6 col-md-3">
                <span class="stat-number">100%</span>
                <div class="stat-label">Secure Payments</div>
            </div>
        </div>
    </div>
</section>

<!-- CATEGORIES -->
<section style="padding: 5rem 0">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-eyebrow">Browse by Category</div>
            <h2 class="section-title">Find What You Need</h2>
            <p class="section-subtitle">From light vehicles to heavy construction equipment</p>
        </div>

        <div class="row g-3">
            @forelse($categories as $category)
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('marketplace') }}?category={{ $category->id }}" class="category-card">
                    <div class="cat-icon">
                        <i class="fas {{ $category->icon ?: 'fa-tag' }}"></i>
                    </div>
                    <div class="cat-name">{{ $category->name }}</div>
                    <div class="cat-count">{{ $category->listings_count }} listing{{ $category->listings_count != 1 ? 's' : '' }}</div>
                </a>
            </div>
            @empty
            @foreach([['fa-truck','Trucks & Lorries'],['fa-tractor','Heavy Machinery'],['fa-car','Cars & SUVs'],['fa-hard-hat','Construction'],['fa-anchor','Marine'],['fa-tools','Equipment']] as $ph)
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('marketplace') }}" class="category-card">
                    <div class="cat-icon"><i class="fas {{ $ph[0] }}"></i></div>
                    <div class="cat-name">{{ $ph[1] }}</div>
                    <div class="cat-count">Browse all</div>
                </a>
            </div>
            @endforeach
            @endforelse
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('marketplace') }}" class="btn btn-outline-amber">
                View All Listings <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- HOW IT WORKS -->
<section id="how-it-works" class="how-section" style="padding: 5rem 0">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-eyebrow">Simple Process</div>
            <h2 class="section-title">How It Works</h2>
            <p class="section-subtitle">Get your equipment in 3 easy steps</p>
        </div>

        <div class="row g-4 justify-content-center">
            <div class="col-md-4 text-center">
                <div class="step-number">1</div>
                <h5 class="step-title">Browse & Book</h5>
                <p class="step-desc">Search thousands of verified listings. Filter by type, location, price and availability. Choose your dates and confirm instantly.</p>
            </div>
            <div class="col-md-4 text-center">
                <div class="step-number">2</div>
                <h5 class="step-title">Secure M-Pesa Payment</h5>
                <p class="step-desc">Pay safely with M-Pesa directly from your phone. Funds are held in escrow until your rental is complete — you're always protected.</p>
            </div>
            <div class="col-md-4 text-center">
                <div class="step-number">3</div>
                <h5 class="step-title">Pick Up & Use</h5>
                <p class="step-desc">Coordinate pickup with the owner. Use your equipment, then return it. Funds are released to the owner automatically on completion.</p>
            </div>
        </div>
    </div>
</section>

<!-- FEATURED LISTINGS -->
@if(isset($featuredListings) && $featuredListings->isNotEmpty())
<section style="padding: 5rem 0">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-eyebrow">Featured</div>
            <h2 class="section-title">Top Listings</h2>
            <p class="section-subtitle">Hand-picked assets from verified owners</p>
        </div>

        <div class="row g-4">
            @foreach($featuredListings as $listing)
            <div class="col-md-6 col-lg-4">
                <div class="listing-card">
                    <div class="listing-photo">
                        @if($listing->photos->isNotEmpty())
                            <img src="{{ asset('storage/' . $listing->photos->first()->path) }}" alt="{{ $listing->title }}">
                        @else
                            <i class="fas fa-image"></i>
                        @endif
                    </div>
                    <div class="listing-body">
                        @if($listing->category)
                            <span class="listing-badge">{{ $listing->category->name }}</span>
                        @endif
                        <div class="listing-title">{{ $listing->title }}</div>
                        <div class="listing-price">
                            KES {{ number_format($listing->daily_rate) }}<small style="font-size:.75rem;font-weight:500;color:var(--muted)">/day</small>
                        </div>
                        <div class="listing-owner">
                            <i class="fas fa-user-circle me-1"></i>{{ $listing->user?->name ?? 'Verified Owner' }}
                        </div>
                        <div class="mt-auto pt-3">
                            <a href="{{ route('listings.show', $listing->slug ?? $listing->id) }}" class="btn btn-amber btn-sm w-100">
                                View Details <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('marketplace') }}" class="btn btn-outline-amber">
                Browse All Listings <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>
@endif

<!-- CTA -->
<section class="cta-section">
    <div class="container">
        <div class="d-inline-flex align-items-center gap-2 mb-3" style="background:rgba(232,146,42,.1);border:1px solid rgba(232,146,42,.25);color:var(--amber);border-radius:20px;padding:.3rem .9rem;font-size:.78rem;font-weight:700;letter-spacing:.5px;text-transform:uppercase">
            <i class="fas fa-rocket"></i> Get Started Today
        </div>
        <h2>Ready to List Your Asset?</h2>
        <p>Join 200+ verified owners earning passive income by renting out their vehicles and machinery on TheOnlineYard.</p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="{{ route('register') }}" class="btn btn-amber btn-lg px-5">
                <i class="fas fa-user-plus me-2"></i>Register Free
            </a>
            <a href="{{ route('marketplace') }}" class="btn btn-outline-amber btn-lg px-5">
                <i class="fas fa-search me-2"></i>Browse First
            </a>
        </div>
        <p class="mt-3" style="font-size:.8rem;color:var(--muted)">No listing fees. Pay only when you earn.</p>
    </div>
</section>

<!-- FOOTER -->
<footer>
    <div class="container">
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="footer-brand mb-2"><i class="fas fa-warehouse me-2"></i>TheOnlineYard</div>
                <p style="font-size:.85rem;line-height:1.7;color:var(--muted)">
                    Kenya's premier marketplace for vehicle and machinery rentals. Connecting equipment owners with businesses that need them.
                </p>
            </div>
            <div class="col-md-2">
                <div style="font-weight:700;color:var(--text);margin-bottom:.75rem;font-size:.85rem">Platform</div>
                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('marketplace') }}">Browse Listings</a>
                    <a href="{{ route('register') }}">List an Asset</a>
                    @auth
                        <a href="{{ route('dashboard') }}">My Dashboard</a>
                    @else
                        <a href="{{ route('login') }}">Sign In</a>
                    @endauth
                </div>
            </div>
            <div class="col-md-2">
                <div style="font-weight:700;color:var(--text);margin-bottom:.75rem;font-size:.85rem">Company</div>
                <div class="d-flex flex-column gap-2">
                    <a href="#how-it-works">How it Works</a>
                    <a href="#">About Us</a>
                    <a href="#">Contact</a>
                </div>
            </div>
            <div class="col-md-4">
                <div style="font-weight:700;color:var(--text);margin-bottom:.75rem;font-size:.85rem">Contact</div>
                <div class="d-flex flex-column gap-2">
                    <span><i class="fas fa-envelope me-2 text-amber"></i>info@theonlineyard.co.ke</span>
                    <span><i class="fas fa-phone me-2 text-amber"></i>+254 700 000 000</span>
                    <span><i class="fas fa-map-marker-alt me-2 text-amber"></i>Nairobi, Kenya</span>
                </div>
            </div>
        </div>
        <hr style="border-color:var(--border);margin:1.5rem 0">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <span>&copy; {{ date('Y') }} TheOnlineYard. All rights reserved.</span>
            <div class="d-flex gap-3">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
