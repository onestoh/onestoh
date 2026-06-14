<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TheOnlineYard — Kenya's Premier Vehicle & Machinery Rental Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
        body { background: var(--black); color: var(--text); font-family: 'Segoe UI', sans-serif; margin: 0; }
        a { text-decoration: none; }

        /* Navbar */
        .navbar-brand { color: var(--amber) !important; font-weight: 800; font-size: 1.3rem; letter-spacing: -.5px; }
        .nav-link-custom { color: var(--muted); font-size: .9rem; font-weight: 500; padding: .5rem .9rem; border-radius: 6px; transition: color .2s; }
        .nav-link-custom:hover { color: var(--text); }
        .btn-amber { background: var(--amber); color: #000; font-weight: 700; border: none; padding: .55rem 1.4rem; border-radius: 8px; }
        .btn-amber:hover { background: #d4821f; color: #000; }
        .btn-outline-amber { border: 2px solid var(--amber); color: var(--amber); background: transparent; font-weight: 700; padding: .55rem 1.4rem; border-radius: 8px; }
        .btn-outline-amber:hover { background: var(--amber); color: #000; }
        .top-navbar { background: rgba(8,12,18,.95); border-bottom: 1px solid var(--border); backdrop-filter: blur(10px); position: sticky; top: 0; z-index: 1000; }

        /* Hero */
        .hero-section {
            background: linear-gradient(135deg, #080C12 0%, #0f1a28 40%, #1a1000 100%);
            padding: 7rem 0 5rem;
            position: relative;
            overflow: hidden;
        }
        .hero-section::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background: radial-gradient(ellipse at 60% 50%, rgba(232,146,42,.08) 0%, transparent 70%);
        }
        .hero-title { font-size: clamp(2rem, 5vw, 3.2rem); font-weight: 800; line-height: 1.15; }
        .hero-subtitle { color: var(--muted); font-size: 1.15rem; max-width: 600px; margin: 0 auto; }
        .amber-text { color: var(--amber); }
        .hero-badge { background: rgba(232,146,42,.12); border: 1px solid rgba(232,146,42,.3); color: var(--amber); font-size: .78rem; font-weight: 700; padding: .3rem .9rem; border-radius: 20px; letter-spacing: .5px; text-transform: uppercase; display: inline-block; margin-bottom: 1.5rem; }

        /* Stats bar */
        .stats-bar { background: var(--surface); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); padding: 1.5rem 0; }
        .stat-item .value { font-size: 1.8rem; font-weight: 800; color: var(--amber); display: block; }
        .stat-item .label { font-size: .82rem; color: var(--muted); }

        /* Section titles */
        .section-title { font-size: 2rem; font-weight: 800; margin-bottom: .5rem; }
        .section-sub { color: var(--muted); margin-bottom: 2.5rem; }

        /* Category cards */
        .cat-card { background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 1.8rem 1.2rem; text-align: center; transition: border-color .2s, transform .2s; display: block; color: var(--text); }
        .cat-card:hover { border-color: var(--amber); transform: translateY(-2px); color: var(--text); }
        .cat-card .cat-icon { font-size: 2rem; color: var(--amber); margin-bottom: 1rem; }
        .cat-card .cat-name { font-weight: 700; font-size: 1rem; margin-bottom: .25rem; }
        .cat-card .cat-count { font-size: .8rem; color: var(--muted); }

        /* How it works cards */
        .step-card { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; padding: 2rem 1.5rem; text-align: center; }
        .step-num { width: 48px; height: 48px; background: var(--amber); color: #000; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; font-weight: 800; margin: 0 auto 1.2rem; }
        .step-icon { font-size: 2rem; color: var(--amber); margin-bottom: 1rem; }
        .step-title { font-weight: 700; font-size: 1.05rem; margin-bottom: .5rem; }
        .step-desc { color: var(--muted); font-size: .9rem; }

        /* Listing cards */
        .listing-card { background: var(--surface); border: 1px solid var(--border); border-radius: 12px; overflow: hidden; transition: border-color .2s, transform .2s; height: 100%; display: flex; flex-direction: column; }
        .listing-card:hover { border-color: rgba(232,146,42,.4); transform: translateY(-2px); }
        .listing-photo { height: 180px; background: var(--black); display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .listing-photo img { width: 100%; height: 100%; object-fit: cover; }
        .listing-photo-placeholder { color: var(--muted); font-size: 3rem; }
        .listing-body { padding: 1rem; flex: 1; display: flex; flex-direction: column; }
        .listing-cat { font-size: .72rem; font-weight: 700; background: rgba(232,146,42,.15); color: var(--amber); border: 1px solid rgba(232,146,42,.3); padding: .2rem .6rem; border-radius: 4px; text-transform: uppercase; letter-spacing: .5px; display: inline-block; margin-bottom: .5rem; }
        .listing-title { font-weight: 700; font-size: .95rem; margin-bottom: .5rem; }
        .listing-price { color: var(--amber); font-weight: 800; font-size: 1.05rem; }
        .listing-owner { color: var(--muted); font-size: .8rem; }
        .listing-footer { padding: .75rem 1rem; border-top: 1px solid var(--border); }

        /* CTA section */
        .cta-section { background: linear-gradient(135deg, #1a0e00 0%, #2a1500 50%, #1a0e00 100%); border-top: 1px solid rgba(232,146,42,.2); border-bottom: 1px solid rgba(232,146,42,.2); padding: 5rem 0; }

        /* Footer */
        .site-footer { background: var(--surface); border-top: 1px solid var(--border); padding: 3rem 0 1.5rem; }
        .footer-logo { color: var(--amber); font-weight: 800; font-size: 1.2rem; }
        .footer-tagline { color: var(--muted); font-size: .85rem; margin-top: .25rem; }
        .footer-link { color: var(--muted); font-size: .85rem; display: block; margin-bottom: .4rem; transition: color .2s; }
        .footer-link:hover { color: var(--amber); }
        .footer-copyright { color: var(--muted); font-size: .8rem; }

        [x-cloak] { display: none !important; }
    </style>
</head>
<body>
    <!-- NAVBAR -->
    <nav class="top-navbar py-3">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between">
                <a href="{{ route('home') }}" class="navbar-brand">
                    <i class="fas fa-warehouse me-2"></i>TheOnlineYard
                </a>

                <!-- Desktop Nav -->
                <div class="d-none d-lg-flex align-items-center gap-1">
                    <a href="{{ route('marketplace.index') }}" class="nav-link-custom">Browse</a>
                    <a href="#how-it-works" class="nav-link-custom">How it Works</a>
                    <a href="{{ route('register') }}" class="nav-link-custom">List Your Asset</a>
                </div>

                <div class="d-none d-lg-flex align-items-center gap-2">
                    <a href="{{ route('login') }}" class="btn-outline-amber btn">Login</a>
                    <a href="{{ route('register') }}" class="btn-amber btn">Get Started</a>
                </div>

                <!-- Mobile toggle -->
                <button class="d-lg-none btn" style="color:var(--text);background:rgba(255,255,255,.05);border:1px solid var(--border);"
                        x-data="" @click="$el.nextElementSibling.classList.toggle('d-none')">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div class="d-none d-lg-none mt-3 pb-2" style="border-top:1px solid var(--border);padding-top:.75rem;">
                <a href="{{ route('marketplace.index') }}" class="nav-link-custom d-block mb-1">Browse</a>
                <a href="#how-it-works" class="nav-link-custom d-block mb-1">How it Works</a>
                <a href="{{ route('register') }}" class="nav-link-custom d-block mb-2">List Your Asset</a>
                <div class="d-flex gap-2">
                    <a href="{{ route('login') }}" class="btn-outline-amber btn btn-sm flex-fill">Login</a>
                    <a href="{{ route('register') }}" class="btn-amber btn btn-sm flex-fill">Register</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- HERO -->
    <section class="hero-section text-center">
        <div class="container" style="position:relative;z-index:1;">
            <div class="hero-badge"><i class="fas fa-shield-alt me-1"></i>Verified Owners · M-Pesa Secured</div>
            <h1 class="hero-title mb-4">
                Kenya's Premier <span class="amber-text">Vehicle & Machinery</span><br>Rental Platform
            </h1>
            <p class="hero-subtitle mx-auto mb-5">
                Find trucks, excavators, cranes and more — with secure M-Pesa payments and verified owners. Book in minutes, drive tomorrow.
            </p>
            <div class="d-flex flex-wrap gap-3 justify-content-center">
                <a href="{{ route('marketplace.index') }}" class="btn-amber btn btn-lg px-5">
                    <i class="fas fa-search me-2"></i>Browse Listings
                </a>
                <a href="{{ route('register') }}" class="btn-outline-amber btn btn-lg px-5">
                    <i class="fas fa-plus me-2"></i>List Your Asset
                </a>
            </div>
        </div>
    </section>

    <!-- STATS BAR -->
    <section class="stats-bar">
        <div class="container">
            <div class="row text-center g-3">
                <div class="col-6 col-md-3">
                    <div class="stat-item">
                        <span class="value">500+</span>
                        <span class="label">Vehicles & Machinery</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-item">
                        <span class="value">200+</span>
                        <span class="label">Verified Owners</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-item">
                        <span class="value">1,000+</span>
                        <span class="label">Bookings Completed</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-item">
                        <span class="value">100%</span>
                        <span class="label">Secure Payments</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CATEGORIES -->
    <section class="py-6" style="padding:5rem 0;">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Browse by <span class="amber-text">Category</span></h2>
                <p class="section-sub mb-0">Find exactly what you need from our wide range of equipment</p>
            </div>
            <div class="row g-3">
                @if($categories->isNotEmpty())
                    @foreach($categories as $cat)
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="{{ route('marketplace.index', ['category' => $cat->id]) }}" class="cat-card">
                            <div class="cat-icon">
                                <i class="fas {{ $cat->icon ?? 'fa-box' }}"></i>
                            </div>
                            <div class="cat-name">{{ $cat->name }}</div>
                            <div class="cat-count">{{ $cat->listings_count }} listing{{ $cat->listings_count != 1 ? 's' : '' }}</div>
                        </a>
                    </div>
                    @endforeach
                @else
                    @php
                        $placeholders = [
                            ['icon' => 'fa-truck', 'name' => 'Trucks'],
                            ['icon' => 'fa-hard-hat', 'name' => 'Excavators'],
                            ['icon' => 'fa-cogs', 'name' => 'Cranes'],
                            ['icon' => 'fa-car', 'name' => 'Vehicles'],
                            ['icon' => 'fa-tractor', 'name' => 'Tractors'],
                            ['icon' => 'fa-tools', 'name' => 'Equipment'],
                        ];
                    @endphp
                    @foreach($placeholders as $ph)
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="{{ route('marketplace.index') }}" class="cat-card">
                            <div class="cat-icon"><i class="fas {{ $ph['icon'] }}"></i></div>
                            <div class="cat-name">{{ $ph['name'] }}</div>
                            <div class="cat-count">Coming soon</div>
                        </a>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </section>

    <!-- HOW IT WORKS -->
    <section id="how-it-works" style="padding:5rem 0;background:var(--surface);border-top:1px solid var(--border);border-bottom:1px solid var(--border);">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">How It <span class="amber-text">Works</span></h2>
                <p class="section-sub mb-0">Get started in three simple steps</p>
            </div>
            <div class="row g-4 justify-content-center">
                <div class="col-md-4">
                    <div class="step-card">
                        <div class="step-num">1</div>
                        <div class="step-icon"><i class="fas fa-search"></i></div>
                        <div class="step-title">Browse & Book</div>
                        <p class="step-desc">Search through hundreds of verified vehicles and machinery. Filter by category, location, and availability. Book your chosen equipment in minutes.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="step-card">
                        <div class="step-num">2</div>
                        <div class="step-icon"><i class="fas fa-mobile-alt"></i></div>
                        <div class="step-title">Secure Payment via M-Pesa</div>
                        <p class="step-desc">Pay securely using M-Pesa STK push. Your funds are held in escrow until the rental is complete — protecting both you and the owner.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="step-card">
                        <div class="step-num">3</div>
                        <div class="step-icon"><i class="fas fa-key"></i></div>
                        <div class="step-title">Pick Up & Use</div>
                        <p class="step-desc">Coordinate with the verified owner, pick up your equipment, and get to work. When done, mark the booking complete and the owner gets paid.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FEATURED LISTINGS -->
    @if(isset($featuredListings) && $featuredListings->isNotEmpty())
    <section style="padding:5rem 0;">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Featured <span class="amber-text">Listings</span></h2>
                <p class="section-sub mb-0">Hand-picked equipment from verified owners</p>
            </div>
            <div class="row g-4">
                @foreach($featuredListings as $listing)
                <div class="col-md-6 col-lg-4">
                    <div class="listing-card">
                        <div class="listing-photo">
                            @if($listing->primaryPhoto?->url)
                                <img src="{{ $listing->primaryPhoto->url }}" alt="{{ $listing->title }}">
                            @else
                                <div class="listing-photo-placeholder">
                                    <i class="fas fa-truck"></i>
                                </div>
                            @endif
                        </div>
                        <div class="listing-body">
                            @if($listing->category)
                                <span class="listing-cat">{{ $listing->category->name }}</span>
                            @endif
                            <div class="listing-title">{{ $listing->title }}</div>
                            <div class="d-flex align-items-center justify-content-between mt-auto pt-2">
                                <div class="listing-price">KES {{ number_format($listing->daily_rate ?? 0) }}<small style="font-weight:400;color:var(--muted);font-size:.75rem;">/day</small></div>
                                <div class="listing-owner"><i class="fas fa-user me-1"></i>{{ $listing->user?->name }}</div>
                            </div>
                        </div>
                        <div class="listing-footer">
                            <a href="{{ route('listings.show', $listing->slug ?? $listing->id) }}" class="btn btn-sm w-100"
                               style="background:rgba(232,146,42,.12);color:var(--amber);border:1px solid rgba(232,146,42,.3);font-weight:600;">
                                View Details <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="text-center mt-5">
                <a href="{{ route('marketplace.index') }}" class="btn-amber btn btn-lg px-5">
                    <i class="fas fa-th-large me-2"></i>View All Listings
                </a>
            </div>
        </div>
    </section>
    @endif

    <!-- CTA SECTION -->
    <section class="cta-section text-center">
        <div class="container">
            <h2 class="section-title mb-3">Ready to earn from your <span class="amber-text">assets?</span></h2>
            <p style="color:var(--muted);font-size:1.1rem;max-width:550px;margin:0 auto 2rem;">
                List your vehicle or machinery and reach hundreds of clients across Kenya. Start earning today with zero upfront costs.
            </p>
            <a href="{{ route('register') }}" class="btn-amber btn btn-lg px-5">
                <i class="fas fa-rocket me-2"></i>Get Started Free
            </a>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="site-footer">
        <div class="container">
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="footer-logo"><i class="fas fa-warehouse me-2"></i>TheOnlineYard</div>
                    <div class="footer-tagline">Kenya's premier vehicle & machinery rental marketplace. Connecting owners with clients safely and efficiently.</div>
                </div>
                <div class="col-md-2 col-6">
                    <div style="font-weight:700;font-size:.85rem;margin-bottom:.75rem;color:var(--text);">Platform</div>
                    <a href="{{ route('marketplace.index') }}" class="footer-link">Browse Listings</a>
                    <a href="{{ route('register') }}" class="footer-link">List Your Asset</a>
                    <a href="#how-it-works" class="footer-link">How it Works</a>
                </div>
                <div class="col-md-2 col-6">
                    <div style="font-weight:700;font-size:.85rem;margin-bottom:.75rem;color:var(--text);">Account</div>
                    <a href="{{ route('login') }}" class="footer-link">Login</a>
                    <a href="{{ route('register') }}" class="footer-link">Register</a>
                    <a href="{{ route('dashboard') }}" class="footer-link">Dashboard</a>
                </div>
                <div class="col-md-4">
                    <div style="font-weight:700;font-size:.85rem;margin-bottom:.75rem;color:var(--text);">Why TheOnlineYard?</div>
                    <div style="color:var(--muted);font-size:.85rem;">
                        <div class="mb-1"><i class="fas fa-check-circle me-2" style="color:var(--green);"></i>M-Pesa escrow payments</div>
                        <div class="mb-1"><i class="fas fa-check-circle me-2" style="color:var(--green);"></i>KYC-verified owners</div>
                        <div class="mb-1"><i class="fas fa-check-circle me-2" style="color:var(--green);"></i>Dispute resolution support</div>
                        <div><i class="fas fa-check-circle me-2" style="color:var(--green);"></i>Kenya-wide coverage</div>
                    </div>
                </div>
            </div>
            <div class="border-top pt-3" style="border-color:var(--border) !important;">
                <div class="footer-copyright text-center">
                    &copy; {{ date('Y') }} TheOnlineYard. All rights reserved. &nbsp;|&nbsp; Made for Kenya
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
