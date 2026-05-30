@extends('layouts.app')

@section('title', 'EstateYard — The All-in-One Real Estate ERP Platform')

@section('content')

<!-- HERO -->
<section class="hero">
  <div class="hero-bg"></div>
  <div class="hero-bg-img" style="background-image: url('https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=1600&q=80');"></div>
  <div class="hero-overlay"></div>
  <div class="hero-glow"></div>
  <div class="hero-watermark">EstateYard</div>

  <div class="hero-content">
    <div class="animate-fadeinup">
      <div class="hero-badge">Africa's #1 Real Estate ERP Platform</div>
      <h1 class="hero-title">Your Property<br><span class="gold">Empire</span><br>Starts Here</h1>
      <p class="hero-desc">Buy, sell, rent, auction, and manage property — all in one verified platform. Powered by escrow security, automated rent collection, AI insights, and 13 tailored dashboards.</p>
      <div class="hero-cta">
        <a href="{{ url('/marketplace') }}" class="btn btn-gold btn-lg">🔍 Browse Properties</a>
        <a href="{{ url('/register') }}" class="btn btn-outline btn-lg">List Your Property</a>
      </div>
      <div class="hero-stats">
        <div class="hero-stat"><div class="hero-stat-num">24K+</div><div class="hero-stat-label">Active Listings</div></div>
        <div class="hero-stat"><div class="hero-stat-num">$2.4B</div><div class="hero-stat-label">Deals Closed</div></div>
        <div class="hero-stat"><div class="hero-stat-num">13</div><div class="hero-stat-label">Stakeholder Roles</div></div>
        <div class="hero-stat"><div class="hero-stat-num">98%</div><div class="hero-stat-label">Satisfaction</div></div>
      </div>
    </div>

    <div class="hero-visual animate-fadein">
      <div class="hero-card">
        <div class="hero-card-header">
          <div class="hero-card-title">Live Marketplace</div>
          <div class="live-badge">LIVE</div>
        </div>
        <div class="listing-mini">
          <div class="listing-mini-img">🏠</div>
          <div class="listing-mini-info">
            <div class="listing-mini-type">For Sale · Verified</div>
            <div class="listing-mini-title">4-Bed Villa, Karen</div>
            <div class="listing-mini-meta">📍 Karen, Nairobi · 340m²</div>
          </div>
          <div><div class="listing-mini-price">$285K</div><div class="verified-badge">✓ VERIFIED</div></div>
        </div>
        <div class="listing-mini">
          <div class="listing-mini-img">🏢</div>
          <div class="listing-mini-info">
            <div class="listing-mini-type">For Rent · Airbnb</div>
            <div class="listing-mini-title">Luxury Apt, Westlands</div>
            <div class="listing-mini-meta">📍 Westlands · Studio</div>
          </div>
          <div><div class="listing-mini-price">$120/n</div><div class="verified-badge">✓ VERIFIED</div></div>
        </div>
        <div class="listing-mini">
          <div class="listing-mini-img">🌿</div>
          <div class="listing-mini-info">
            <div class="listing-mini-type">Land · For Sale</div>
            <div class="listing-mini-title">Prime Plot, Kitengela</div>
            <div class="listing-mini-meta">📍 Kitengela · 0.5 Acres</div>
          </div>
          <div><div class="listing-mini-price">$18K</div><div class="verified-badge">✓ VERIFIED</div></div>
        </div>
        <div class="listing-mini" style="border-color:rgba(224,82,82,0.3); background:rgba(224,82,82,0.03);">
          <div class="listing-mini-img">🔨</div>
          <div class="listing-mini-info">
            <div class="listing-mini-type" style="color:var(--red);">🔴 Live Auction</div>
            <div class="listing-mini-title">Commercial Block, CBD</div>
            <div class="listing-mini-meta">📍 Nairobi CBD · Ends in 2h 14m</div>
          </div>
          <div><div class="listing-mini-price" style="color:var(--red);">$512K</div><div class="badge badge-red" style="font-size:10px; margin-top:4px;">BIDDING</div></div>
        </div>
        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px; margin-top:16px; padding-top:16px; border-top:1px solid var(--border-dim);">
          <div style="text-align:center;"><div style="font-family:var(--font-serif); font-size:22px; font-weight:700; color:var(--gold);">2,847</div><div style="font-size:11px; color:var(--muted); font-family:var(--font-mono);">New Today</div></div>
          <div style="text-align:center; border-left:1px solid var(--border-dim); border-right:1px solid var(--border-dim);"><div style="font-family:var(--font-serif); font-size:22px; font-weight:700; color:var(--green);">✓ 89%</div><div style="font-size:11px; color:var(--muted); font-family:var(--font-mono);">Verified</div></div>
          <div style="text-align:center;"><div style="font-family:var(--font-serif); font-size:22px; font-weight:700; color:var(--blue);">14</div><div style="font-size:11px; color:var(--muted); font-family:var(--font-mono);">Live Auctions</div></div>
        </div>
      </div>
      <div style="position:absolute; top:-20px; right:-20px; background:var(--surface); border:1px solid var(--border); border-radius:12px; padding:10px 16px; display:flex; align-items:center; gap:8px; box-shadow:var(--shadow);">
        <span style="font-size:18px;">🔒</span>
        <div><div style="font-size:12px; font-weight:600; color:var(--white);">Escrow Protected</div><div style="font-size:10px; color:var(--green); font-family:var(--font-mono);">Every transaction</div></div>
      </div>
      <div style="position:absolute; bottom:-16px; left:-16px; background:var(--surface); border:1px solid var(--border); border-radius:12px; padding:10px 16px; display:flex; align-items:center; gap:8px; box-shadow:var(--shadow);">
        <span style="font-size:18px;">💳</span>
        <div><div style="font-size:12px; font-weight:600; color:var(--white);">M-Pesa + Bank</div><div style="font-size:10px; color:var(--gold); font-family:var(--font-mono);">Payment rails</div></div>
      </div>
    </div>
  </div>
</section>

<!-- SEARCH BAR -->
<section style="background:var(--navy2); border-bottom:1px solid var(--border); padding:32px 0;">
  <div class="container">
    <div style="display:flex; gap:16px; align-items:center; flex-wrap:wrap;">
      <div style="display:flex; gap:6px; overflow-x:auto; scrollbar-width:none;">
        @foreach(['🏠 Buy', '🔑 Rent', '✈️ Short Stay', '🏨 Hotels', '🌿 Land', '🏗️ Off-Plan', '🔨 Auction'] as $tab)
        <a href="{{ url('/marketplace') }}" style="white-space:nowrap; padding:8px 18px; border-radius:100px; font-size:13px; font-weight:500; background:var(--navy3); border:1px solid var(--border-dim); color:var(--muted); text-decoration:none; transition:all .2s;" onmouseover="this.style.borderColor='var(--gold)';this.style.color='var(--gold)';" onmouseout="this.style.borderColor='var(--border-dim)';this.style.color='var(--muted)';">{{ $tab }}</a>
        @endforeach
      </div>
      <div class="search-bar" style="max-width:500px;">
        <span style="color:var(--muted);">🔍</span>
        <input type="text" class="search-input" placeholder="City, neighbourhood, or property...">
        <div class="search-divider"></div>
        <select class="search-filter"><option>Any Price</option><option>Under $50K</option><option>$50K–$200K</option><option>$200K–$500K</option><option>$500K+</option></select>
        <a href="{{ url('/marketplace') }}" class="btn btn-gold btn-sm">Search</a>
      </div>
    </div>
  </div>
</section>

<!-- CATEGORIES -->
<section class="section" style="padding:64px 0;">
  <div class="container">
    <div style="text-align:center; margin-bottom:48px;">
      <div class="section-tag" style="justify-content:center;">Browse by Category</div>
      <h2 class="section-title" style="margin-top:8px;">Find What You're Looking For</h2>
      <p style="color:var(--muted); font-size:15px; max-width:600px; margin:12px auto 0;">From residential homes to commercial properties, land, hotels, and short-stay units — all verified, all in one place.</p>
    </div>
    <div class="category-grid" style="grid-template-columns:repeat(auto-fill, minmax(160px, 1fr));">
      @foreach([['🏠','Houses','8,241 listings'],['🏢','Apartments','12,084 listings'],['🌿','Land & Plots','3,892 listings'],['🏗️','Off-Plan','1,245 listings'],['✈️','Airbnb / STR','4,517 listings'],['🏨','Hotels','892 listings'],['🏬','Commercial','2,341 listings'],['🔨','Auctions','156 active'],['🏭','Industrial','445 listings'],['🌾','Farmland','678 listings']] as $cat)
      <a href="{{ url('/marketplace') }}" class="category-card" style="text-decoration:none;">
        <span class="category-icon">{{ $cat[0] }}</span>
        <div class="category-name">{{ $cat[1] }}</div>
        <div class="category-count">{{ $cat[2] }}</div>
      </a>
      @endforeach
    </div>
  </div>
</section>

<!-- FEATURED HOUSES -->
<section class="marketplace-section section-alt">
  <div class="container">
    <div class="marketplace-section-header">
      <div>
        <div class="section-tag">🏠 Houses & Apartments</div>
        <h2 class="section-title" style="font-size:clamp(24px,3vw,38px); margin-top:8px;">Buy or Rent a Home</h2>
        <p style="color:var(--muted); font-size:14px; margin-top:8px;">Verified residential properties with full escrow protection</p>
      </div>
      <a href="{{ url('/marketplace') }}" class="btn btn-outline">View All <span style="color:var(--gold);">→</span></a>
    </div>
    <div class="property-grid">
      @foreach([['🏠','For Sale','4-Bed Villa with Pool','Karen, Nairobi','4','3','340m²','KSh 28.5M',true,'green'],['🏢','For Rent','2-Bed Penthouse','Westlands, Nairobi','2','2','120m²','KSh 95K/mo',true,'blue'],['🏡','For Sale','3-Bed Townhouse','Kilimani, Nairobi','3','2','180m²','KSh 15.8M',true,'green'],['🏠','For Rent','1-Bed Apartment','Lavington','1','1','65m²','KSh 45K/mo',false,'blue'],['🏘️','For Sale','5-Bed Mansion','Runda Estate','5','4','600m²','KSh 85M',true,'gold'],['🏢','For Rent','Studio Apartment','Upper Hill','0','1','42m²','KSh 32K/mo',true,'blue']] as $p)
      <div class="property-card">
        <div class="property-card-img">
          <span>{{ $p[0] }}</span>
          <div class="property-card-badges">
            <span class="badge badge-{{ $p[9] }}">{{ $p[1] }}</span>
            @if($p[8])<span class="badge badge-green">✓ Verified</span>@endif
          </div>
          <div class="property-card-save">♡</div>
        </div>
        <div class="property-card-body">
          <div class="property-card-type">Residential · {{ $p[1] }}</div>
          <div class="property-card-title">{{ $p[2] }}</div>
          <div class="property-card-location">📍 {{ $p[3] }}</div>
          <div class="property-card-specs">
            @if($p[4] !== '0')<span class="property-card-spec">🛏 {{ $p[4] }} Bed</span>@endif
            <span class="property-card-spec">🚿 {{ $p[5] }} Bath</span>
            <span class="property-card-spec">📐 {{ $p[6] }}</span>
          </div>
          <div class="property-card-footer">
            <div><div class="property-card-price">{{ $p[7] }}</div><div class="property-card-price-label">Escrow protected</div></div>
            <a href="{{ url('/marketplace/listing/1') }}" class="btn btn-gold btn-sm">View</a>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

<!-- AIRBNB -->
<section class="marketplace-section">
  <div class="container">
    <div class="marketplace-section-header">
      <div>
        <div class="section-tag">✈️ Short-Term Rentals</div>
        <h2 class="section-title" style="font-size:clamp(24px,3vw,38px); margin-top:8px;">Airbnb & Short Stay</h2>
        <p style="color:var(--muted); font-size:14px; margin-top:8px;">Premium short-term rentals with instant booking and verified hosts</p>
      </div>
      <a href="{{ url('/marketplace') }}" class="btn btn-outline">View All →</a>
    </div>
    <div class="property-grid">
      @foreach([['✈️','Studio Suite','Westlands','★ 4.9','24 reviews','$65/night'],['🏙️','City View Loft','Upper Hill','★ 4.8','41 reviews','$85/night'],['🌊','Beach Villa','Mombasa','★ 5.0','19 reviews','$220/night'],['🌄','Mountain Retreat','Limuru','★ 4.7','33 reviews','$120/night']] as $a)
      <div class="property-card">
        <div class="property-card-img">
          <span>{{ $a[0] }}</span>
          <div class="property-card-badges">
            <span class="badge badge-teal">Short Stay</span>
            <span class="badge badge-green">✓ Verified</span>
          </div>
          <div class="property-card-save">♡</div>
        </div>
        <div class="property-card-body">
          <div class="property-card-type">Airbnb / Short-Term Rental</div>
          <div class="property-card-title">{{ $a[1] }}</div>
          <div class="property-card-location">📍 {{ $a[2] }}</div>
          <div class="property-card-specs">
            <span class="property-card-spec" style="color:var(--gold);">{{ $a[3] }}</span>
            <span class="property-card-spec">{{ $a[4] }}</span>
          </div>
          <div class="property-card-footer">
            <div><div class="property-card-price" style="color:var(--teal);">{{ $a[5] }}</div><div class="property-card-price-label">Instant booking</div></div>
            <a href="{{ url('/marketplace/listing/2') }}" class="btn btn-sm" style="background:rgba(46,196,182,0.15); border:1px solid rgba(46,196,182,0.3); color:var(--teal);">Book Now</a>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

<!-- HOTELS -->
<section class="marketplace-section section-alt">
  <div class="container">
    <div class="marketplace-section-header">
      <div>
        <div class="section-tag">🏨 Hotels & Serviced Apartments</div>
        <h2 class="section-title" style="font-size:clamp(24px,3vw,38px); margin-top:8px;">Hotels on EstateYard</h2>
        <p style="color:var(--muted); font-size:14px; margin-top:8px;">Book directly with verified hotels — no hidden fees, best rate guaranteed</p>
      </div>
      <a href="{{ url('/marketplace') }}" class="btn btn-outline">View All →</a>
    </div>
    <div class="property-grid">
      @foreach([['🏨','5 Stars','Grand Serena Hotel','Nairobi CBD','★ 4.9','Pool · Spa · Restaurant','$180/night'],['🏩','4 Stars','Eka Hotel','Upper Hill','★ 4.7','Gym · Conference · Bar','$120/night'],['🌴','5 Stars','Sarova Whitesands','Mombasa Beach','★ 5.0','Beachfront · Spa','$280/night'],['🏬','3 Stars','Nairobi Serena','Westlands','★ 4.5','Restaurant · Parking','$85/night']] as $h)
      <div class="property-card">
        <div class="property-card-img">
          <span>{{ $h[0] }}</span>
          <div class="property-card-badges"><span class="badge badge-gold">{{ $h[1] }}</span><span class="badge badge-green">✓ Verified</span></div>
          <div class="property-card-save">♡</div>
        </div>
        <div class="property-card-body">
          <div class="property-card-type">Hotel · {{ $h[1] }}</div>
          <div class="property-card-title">{{ $h[2] }}</div>
          <div class="property-card-location">📍 {{ $h[3] }}</div>
          <div class="property-card-specs"><span class="property-card-spec" style="color:var(--gold);">{{ $h[4] }}</span></div>
          <div style="font-size:12px; color:var(--muted); margin-bottom:14px;">{{ $h[5] }}</div>
          <div class="property-card-footer">
            <div><div class="property-card-price">{{ $h[6] }}</div><div class="property-card-price-label">Best rate guaranteed</div></div>
            <a href="{{ url('/marketplace/listing/3') }}" class="btn btn-gold btn-sm">Book</a>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

<!-- LAND -->
<section class="marketplace-section">
  <div class="container">
    <div class="marketplace-section-header">
      <div>
        <div class="section-tag">🌿 Land & Plots</div>
        <h2 class="section-title" style="font-size:clamp(24px,3vw,38px); margin-top:8px;">Land for Sale</h2>
        <p style="color:var(--muted); font-size:14px; margin-top:8px;">Verified land parcels with clean title deeds and GPS-tagged boundaries</p>
      </div>
      <a href="{{ url('/marketplace') }}" class="btn btn-outline">View All →</a>
    </div>
    <div class="property-grid">
      @foreach([['🌿','Residential Plot','Kitengela','0.5 acres','Clean Title','KSh 2.1M'],['🌳','Prime Land','Thika Road','1 acre','Freehold','KSh 8.5M'],['🌾','Agricultural Land','Nakuru','5 acres','Freehold','KSh 4.2M'],['🏖️','Beach Plot','Malindi','0.25 acres','Leasehold 99yr','KSh 12M']] as $l)
      <div class="property-card">
        <div class="property-card-img">
          <span>{{ $l[0] }}</span>
          <div class="property-card-badges"><span class="badge badge-green">✓ Title Verified</span></div>
          <div class="property-card-save">♡</div>
        </div>
        <div class="property-card-body">
          <div class="property-card-type">Land · For Sale</div>
          <div class="property-card-title">{{ $l[1] }}</div>
          <div class="property-card-location">📍 {{ $l[2] }}</div>
          <div class="property-card-specs"><span class="property-card-spec">📐 {{ $l[3] }}</span><span class="property-card-spec">📋 {{ $l[4] }}</span></div>
          <div class="property-card-footer">
            <div><div class="property-card-price">{{ $l[5] }}</div><div class="property-card-price-label">GPS tagged · Title deed on file</div></div>
            <a href="{{ url('/marketplace/listing/4') }}" class="btn btn-gold btn-sm">View</a>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

<!-- LIVE AUCTIONS -->
<section class="marketplace-section section-alt">
  <div class="container">
    <div class="marketplace-section-header">
      <div>
        <div class="section-tag">🔨 Property Auctions</div>
        <h2 class="section-title" style="font-size:clamp(24px,3vw,38px); margin-top:8px;">Live Auction Properties</h2>
        <p style="color:var(--muted); font-size:14px; margin-top:8px;">Bid on verified properties — live auctions with escrow-backed winning bids</p>
      </div>
      <a href="{{ url('/auctions') }}" class="btn btn-outline" style="border-color:rgba(224,82,82,0.3); color:var(--red);">🔴 View Live Auctions →</a>
    </div>
    <div class="property-grid">
      @foreach([['🏢','Commercial Block','Nairobi CBD','$512,000','$480,000','2h 14m',24],['🏡','Residential Home','Muthaiga','$180,000','$165,000','5h 30m',12],['🏗️','Office Complex','Upperhill','$2.1M','$1.9M','1d 2h',8]] as $au)
      <div class="property-card" style="border-color:rgba(224,82,82,0.2);">
        <div class="property-card-img" style="background:linear-gradient(135deg,rgba(224,82,82,0.1),var(--navy3));">
          <span>{{ $au[0] }}</span>
          <div class="property-card-badges"><span class="badge badge-red">🔴 LIVE</span><span class="badge badge-green">✓ Verified</span></div>
          <div style="position:absolute; bottom:12px; right:12px; background:rgba(8,17,31,0.85); border:1px solid rgba(224,82,82,0.3); border-radius:8px; padding:6px 12px; text-align:center;">
            <div style="font-size:10px; font-family:var(--font-mono); color:var(--red); letter-spacing:1px;">ENDS IN</div>
            <div style="font-size:14px; font-weight:700; color:var(--white); font-family:var(--font-serif);">{{ $au[5] }}</div>
          </div>
        </div>
        <div class="property-card-body">
          <div class="property-card-type" style="color:var(--red);">🔴 Live Auction · Escrow Ready</div>
          <div class="property-card-title">{{ $au[1] }}</div>
          <div class="property-card-location">📍 {{ $au[2] }}</div>
          <div class="property-card-specs"><span class="property-card-spec">👥 {{ $au[6] }} bidders</span><span class="property-card-spec">Reserve: {{ $au[4] }}</span></div>
          <div class="property-card-footer">
            <div><div class="property-card-price" style="color:var(--red);">{{ $au[3] }}</div><div class="property-card-price-label">Current bid</div></div>
            <a href="{{ url('/auctions/1') }}" class="btn btn-sm" style="background:rgba(224,82,82,0.15); border:1px solid rgba(224,82,82,0.3); color:var(--red);">Place Bid</a>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

<!-- COMMERCIAL -->
<section class="marketplace-section">
  <div class="container">
    <div class="marketplace-section-header">
      <div>
        <div class="section-tag">🏬 Commercial Properties</div>
        <h2 class="section-title" style="font-size:clamp(24px,3vw,38px); margin-top:8px;">Office, Retail & Warehouse</h2>
        <p style="color:var(--muted); font-size:14px; margin-top:8px;">Prime commercial spaces for your growing business</p>
      </div>
      <a href="{{ url('/commercial') }}" class="btn btn-outline">View All →</a>
    </div>
    <div class="property-grid">
      @foreach([['🏢','Office Space','Upperhill','500m²','A-Grade','$18/m²/mo'],['🏪','Retail Unit','Westgate Mall','120m²','Ground Floor','$55/m²/mo'],['🏭','Warehouse','Syokimau','2,000m²','Grade B','$8/m²/mo'],['☕','Restaurant Space','Kilimani','200m²','Street Level','$30/m²/mo']] as $c)
      <div class="property-card">
        <div class="property-card-img">
          <span>{{ $c[0] }}</span>
          <div class="property-card-badges"><span class="badge badge-purple">Commercial</span><span class="badge badge-green">✓ Verified</span></div>
          <div class="property-card-save">♡</div>
        </div>
        <div class="property-card-body">
          <div class="property-card-type">Commercial · For Lease</div>
          <div class="property-card-title">{{ $c[1] }}</div>
          <div class="property-card-location">📍 {{ $c[2] }}</div>
          <div class="property-card-specs"><span class="property-card-spec">📐 {{ $c[3] }}</span><span class="property-card-spec">🏗 {{ $c[4] }}</span></div>
          <div class="property-card-footer">
            <div><div class="property-card-price" style="color:var(--purple);">{{ $c[5] }}</div><div class="property-card-price-label">Net rentable area</div></div>
            <a href="{{ url('/marketplace/listing/5') }}" class="btn btn-sm" style="background:rgba(155,114,207,0.15); border:1px solid rgba(155,114,207,0.3); color:var(--purple);">Enquire</a>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

<!-- WHY ESTATEYARD FEATURES -->
<section class="section section-alt">
  <div class="container">
    <div style="text-align:center; margin-bottom:56px;">
      <div class="section-tag" style="justify-content:center;">Why EstateYard</div>
      <h2 class="section-title" style="margin-top:8px;">The Complete Real Estate OS</h2>
      <p style="color:var(--muted); font-size:15px; max-width:640px; margin:12px auto 0;">From the moment a property is listed to when the keys are handed over — EstateYard powers every step with technology, trust, and transparency.</p>
    </div>
    <div class="features-grid">
      @foreach([['🔒','Escrow Security','Every transaction is protected by a regulated escrow engine. Funds only release when all conditions are verified.'],['✅','Verified Listings','Every property is verified with title deed reference, GPS coordinates, and admin approval before going live.'],['💳','M-Pesa + Bank Payments','Pay rent, deposits, and purchases via M-Pesa STK Push or direct bank transfer — all within the platform.'],['🔨','Live Property Auctions','Bid on properties in real-time with live bid feeds, KYC-approved bidders, and escrow-backed winning bids.'],['🤝','Affiliate Referral Engine','Every listing has a referral link. Earn commissions by sharing — even if the buyer buys a different property.'],['📅','Rent Automation','Automated rent invoicing, 3-day reminders, M-Pesa collection, and instant landlord payouts — zero manual work.'],['🏨','Hotel & Airbnb PMS','Full Property Management System for hotels, Airbnb hosts, and short-stay operators with channel manager.'],['🏦','Property Financing','Apply for mortgages and construction loans through EstateYard using your verified listing as collateral.'],['📊','13 Tailored Dashboards','Every stakeholder gets a purpose-built dashboard — from landlords to auctioneers, valuers to investors.']] as $f)
      <div class="feature-card">
        <div class="feature-icon">{{ $f[0] }}</div>
        <div class="feature-title">{{ $f[1] }}</div>
        <div class="feature-desc">{{ $f[2] }}</div>
      </div>
      @endforeach
    </div>
  </div>
</section>

<!-- STAKEHOLDER ROLES -->
<section class="section">
  <div class="container">
    <div style="text-align:center; margin-bottom:56px;">
      <div class="section-tag" style="justify-content:center;">Built for Everyone</div>
      <h2 class="section-title" style="margin-top:8px;">13 Tailored Stakeholder Roles</h2>
      <p style="color:var(--muted); font-size:15px; max-width:640px; margin:12px auto 0;">EstateYard serves every participant in the property ecosystem with purpose-built dashboards and role-based access control.</p>
    </div>
    <div class="stakeholder-grid">
      @foreach([['🏛️','Super Admin','Platform Operator','Full GMV, KYC queue, escrow oversight, platform administration'],['🏠','Landlord','Property Owner','Portfolio management, rent collection, tenants, leases'],['📋','Licensed Broker','Certified Agent','Listings, CRM pipeline, commissions, market comparables'],['📲','Promoter','Unlicensed Affiliate','Social media referrals, commission tracking, earnings dashboard'],['🔑','Tenant / Buyer','End Consumer','Browse, book inspections, pay rent, sign digital leases'],['🗂️','Property Manager','3rd Party Manager','Multi-property ops, vendor dispatch, CAM reconciliation'],['🏗️','Developer','Construction & Dev','Project pipeline, off-plan sales, Gantt, financing'],['📐','Valuer','Appraiser Professional','Digital valuation reports, job queue, comparable analysis'],['🗺️','Surveyor','Land & Building Surveys','GPS site inspections, boundary reports, structural checks'],['🔨','Auctioneer','Auction Specialist','Live auctions, bid management, escrow trigger, legal coordination'],['📊','Investor / REIT','Portfolio Manager','ROI dashboard, cap rate, yield, forecasting, financing'],['🏢','Corporate Partner','Agency / Company','Sub-accounts, bulk listings, white-label tools, API access'],['💰','Finance Controller','Financial Accountant','General ledger, escrow reconciliation, tax reporting']] as $s)
      <div class="stakeholder-card">
        <div class="stakeholder-head">
          <div class="stakeholder-icon">{{ $s[0] }}</div>
          <div><div class="stakeholder-name">{{ $s[1] }}</div><div class="stakeholder-role">{{ $s[2] }}</div></div>
        </div>
        <p style="font-size:13px; color:var(--muted); line-height:1.7; margin-bottom:16px;">{{ $s[3] }}</p>
        <a href="{{ url('/register') }}" class="btn btn-sm" style="background:rgba(212,168,67,0.08); border:1px solid var(--border); color:var(--gold);">Get Started →</a>
      </div>
      @endforeach
    </div>
  </div>
</section>

<!-- VERIFICATION PLANS -->
<section class="section section-alt">
  <div class="container">
    <div style="text-align:center; margin-bottom:48px;">
      <div class="section-tag" style="justify-content:center;">Verification Badge</div>
      <h2 class="section-title" style="margin-top:8px;">Get Verified on EstateYard</h2>
      <p style="color:var(--muted); font-size:15px; max-width:580px; margin:12px auto 0;">A verified badge signals to buyers, sellers, and partners that you have been KYC-verified. Required to list, transact, and earn on the platform.</p>
    </div>
    <div class="pricing-grid">
      @foreach([['1 Month','$29','/mo',false,['Full verification badge','Listings remain live','Basic analytics','Renew every 30 days']],['3 Months','$79','/qtr',true,['Full verification badge','Save 9% vs monthly','Priority listing visibility','Standard analytics']],['6 Months','$149','/6mo',false,['Full verification badge','Save 14% vs monthly','Enhanced listing boost','Full analytics suite']],['1 Year','$249','/yr',false,['Full verification badge','Save 28% vs monthly','Top listing priority','Advanced analytics + API']]] as $plan)
      <div class="pricing-card {{ $plan[3] ? 'popular' : '' }}">
        @if($plan[3])<div class="pricing-popular-tag">MOST POPULAR</div>@endif
        <div class="pricing-title">{{ $plan[0] }}</div>
        <div class="pricing-price">{{ $plan[1] }}<sub>{{ $plan[2] }}</sub></div>
        <ul class="pricing-features">@foreach($plan[4] as $pf)<li>{{ $pf }}</li>@endforeach</ul>
        <a href="{{ url('/register') }}" class="btn btn-{{ $plan[3] ? 'gold' : 'outline' }}" style="width:100%; justify-content:center; margin-top:20px;">Get Verified</a>
      </div>
      @endforeach
    </div>
  </div>
</section>

<!-- REFERRAL CTA -->
<section class="section" style="background:linear-gradient(135deg,var(--navy2),var(--navy3));">
  <div class="container">
    <div style="max-width:800px; margin:0 auto; text-align:center;">
      <div class="section-tag" style="justify-content:center;">💰 Affiliate Program</div>
      <h2 class="section-title" style="margin-top:8px;">Earn by Sharing Properties</h2>
      <p style="color:var(--muted); font-size:16px; max-width:580px; margin:16px auto 28px; line-height:1.8;">Get a unique referral link for any property. Share on social media. Earn a commission when anyone in your network closes a deal — even if they buy a <em style="color:var(--gold);">different</em> property.</p>
      <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:20px; margin-bottom:40px;">
        @foreach([['🔗','Get Referral Link','Unique link per property or your entire portfolio'],['📲','Share on Social','Auto-post to Instagram, TikTok, Facebook, LinkedIn'],['👁️','Track Clicks','Real-time analytics — clicks, views, conversions'],['💰','Earn Commission','Paid on deal close — even for a different property']] as $r)
        <div style="background:var(--navy3); border:1px solid var(--border-dim); border-radius:var(--radius); padding:20px; text-align:center;">
          <div style="font-size:32px; margin-bottom:10px;">{{ $r[0] }}</div>
          <div style="font-size:14px; font-weight:600; color:var(--white); margin-bottom:6px;">{{ $r[1] }}</div>
          <div style="font-size:12px; color:var(--muted);">{{ $r[2] }}</div>
        </div>
        @endforeach
      </div>
      <div style="display:flex; gap:14px; justify-content:center; flex-wrap:wrap;">
        <a href="{{ url('/register') }}" class="btn btn-gold btn-lg">Start Earning Today</a>
        <a href="{{ url('/register') }}" class="btn btn-outline btn-lg">Licensed Broker Program</a>
      </div>
    </div>
  </div>
</section>

<!-- TESTIMONIALS -->
<section class="section section-alt">
  <div class="container">
    <div style="text-align:center; margin-bottom:48px;">
      <div class="section-tag" style="justify-content:center;">Trusted by Thousands</div>
      <h2 class="section-title" style="margin-top:8px;">What Our Users Say</h2>
    </div>
    <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:20px;">
      @foreach([['James Kamau','Landlord · Nairobi','⭐⭐⭐⭐⭐','EstateYard\'s rent automation changed my life. Fully automated — the system reminds tenants, collects via M-Pesa, and credits my account same day.'],['Sarah Odhiambo','Licensed Broker','⭐⭐⭐⭐⭐','The CRM pipeline is incredible. I closed 3 deals last month that I would have lost track of without the Kanban board. The referral engine brings passive income too.'],['David Waweru','Property Developer','⭐⭐⭐⭐⭐','Applying for construction financing using my verified EstateYard listing as collateral was a game-changer. Got pre-approval in 48 hours from their bank partner.'],['Amina Hassan','Promoter / Influencer','⭐⭐⭐⭐⭐','I earn referral commissions by sharing property links on Instagram. Last quarter I earned KSh 180,000 just from referrals — without being a licensed agent.'],['Peter Njoroge','Tenant / Buyer','⭐⭐⭐⭐⭐','Paying rent via M-Pesa with one tap is so convenient. I can see my lease agreement, payment history, and maintenance requests all in one place.'],['Fatuma Ali','Investor / REIT','⭐⭐⭐⭐⭐','The investor dashboard gives me IRR, cap rate, and yield calculations in real time. I manage 12 properties across 3 cities and EstateYard keeps everything consolidated.']] as $t)
      <div style="background:var(--navy3); border:1px solid var(--border-dim); border-radius:var(--radius); padding:24px;">
        <div style="font-size:22px; margin-bottom:12px;">{{ $t[2] }}</div>
        <p style="font-size:13px; color:var(--muted); line-height:1.7; margin-bottom:18px; font-style:italic;">"{{ $t[3] }}"</p>
        <div style="display:flex; align-items:center; gap:12px; padding-top:16px; border-top:1px solid var(--border-dim);">
          <div style="width:40px; height:40px; border-radius:50%; background:var(--gold-dim); border:2px solid var(--gold); display:flex; align-items:center; justify-content:center; font-size:18px;">👤</div>
          <div><div style="font-size:14px; font-weight:600; color:var(--white);">{{ $t[0] }}</div><div style="font-size:12px; color:var(--gold); font-family:var(--font-mono);">{{ $t[1] }}</div></div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

<!-- FINAL CTA -->
<section style="background:linear-gradient(160deg,var(--navy2) 0%,var(--navy3) 100%); border-top:1px solid var(--border); padding:100px 0; text-align:center; position:relative; overflow:hidden;">
  <div style="position:absolute; inset:0; background:radial-gradient(ellipse 60% 70% at 50% 50%,rgba(212,168,67,0.05) 0%,transparent 70%);"></div>
  <div style="position:absolute; right:-80px; top:-40px; font-family:var(--font-serif); font-size:280px; font-weight:700; color:rgba(212,168,67,0.025); line-height:1; pointer-events:none;">EY</div>
  <div class="container" style="position:relative; z-index:1;">
    <div class="section-tag" style="justify-content:center;">Ready to Start?</div>
    <h2 style="font-family:var(--font-serif); font-size:clamp(40px,6vw,72px); font-weight:700; color:var(--white); line-height:1.05; margin:16px 0;">Africa's Most Powerful<br><span style="color:var(--gold);">Real Estate Platform</span></h2>
    <p style="font-size:17px; color:var(--muted); max-width:600px; margin:0 auto 40px; line-height:1.8; font-weight:300;">Join 50,000+ landlords, brokers, developers, and investors already using EstateYard to transact, manage, and grow their property portfolios.</p>
    <div style="display:flex; gap:16px; justify-content:center; flex-wrap:wrap;">
      <a href="{{ url('/marketplace') }}" class="btn btn-gold btn-lg" style="font-size:17px; padding:18px 48px;">🔍 Explore Marketplace</a>
      <a href="{{ url('/register') }}" class="btn btn-outline btn-lg" style="font-size:17px; padding:18px 48px;">Create Free Account</a>
    </div>
    <div style="margin-top:32px; display:flex; gap:24px; justify-content:center; flex-wrap:wrap;">
      @foreach(['✓ Free to browse','✓ Verified listings','✓ Escrow protected','✓ M-Pesa payments','✓ No hidden fees'] as $trust)
      <span style="font-size:13px; color:var(--muted);">{{ $trust }}</span>
      @endforeach
    </div>
  </div>
</section>

@endsection
