@extends('layouts.app')

@section('title', 'TheOnlineYard — Vehicle & Machinery Rental Marketplace')

@push('styles')
<style>
.hero-section{background:linear-gradient(160deg,#080C12 0%,#0B1018 55%,#080C12 100%);border-bottom:1px solid var(--border);padding:90px 0 70px;position:relative;overflow:hidden;}
.hero-section::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 70% 50% at 65% 35%,rgba(232,146,42,0.07) 0%,transparent 70%);}
.hero-tag{font-family:'JetBrains Mono',monospace;font-size:10px;letter-spacing:3px;color:var(--amber);text-transform:uppercase;margin-bottom:16px;display:flex;align-items:center;gap:8px;}
.hero-tag::before{content:'';width:28px;height:1px;background:var(--amber);}
.hero-title{font-family:'Cormorant Garamond',serif;font-size:clamp(46px,6vw,82px);font-weight:700;line-height:1.0;color:#fff;margin-bottom:18px;}
.hero-title span{color:var(--amber);}
.hero-sub{font-size:16px;color:var(--muted);max-width:640px;margin-bottom:40px;font-weight:300;line-height:1.9;}
.search-box{background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:24px;margin-bottom:40px;}
.search-box .form-select,.search-box .form-control{height:48px;font-size:14px;}
.btn-search{background:var(--amber);color:var(--black);border:none;border-radius:10px;padding:0 28px;height:48px;font-weight:700;font-size:14px;white-space:nowrap;}
.btn-search:hover{background:var(--amber2);color:var(--black);}
.stat-strip{background:var(--surface2);border:1px solid var(--border);border-radius:12px;padding:20px 30px;}
.stat-item .val{font-family:'Cormorant Garamond',serif;font-size:36px;font-weight:700;color:var(--amber);}
.stat-item .label{font-size:11px;color:var(--muted);font-family:'JetBrains Mono',monospace;letter-spacing:2px;text-transform:uppercase;}
.cat-pill{background:var(--surface2);border:1px solid rgba(255,255,255,0.06);border-radius:50px;padding:10px 20px;font-size:13px;color:var(--muted);cursor:pointer;transition:all .2s;display:flex;align-items:center;gap:8px;white-space:nowrap;text-decoration:none;}
.cat-pill:hover,.cat-pill.active{background:var(--amber-dim);border-color:var(--amber);color:var(--amber);}
</style>
@endpush

@section('content')

<div class="hero-section">
  <div class="container">
    <div class="hero-tag">Kenya's #1 Vehicle & Machinery Rental Platform</div>
    <h1 class="hero-title">Rent or Buy Any<br><span>Vehicle or Machine</span></h1>
    <p class="hero-sub">Browse verified yards and private owners. Book instantly with M-Pesa. Full escrow protection on every rental.</p>

    <div class="search-box">
      <form action="{{ route('marketplace') }}" method="GET">
        <div class="row g-3 align-items-end">
          <div class="col-md-3">
            <label class="form-label">What are you looking for?</label>
            <select name="type" class="form-select">
              <option value="">All Categories</option>
              <option value="vehicle">Vehicles</option>
              <option value="machinery">Machinery</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Location</label>
            <input type="text" name="county" class="form-control" placeholder="County or City">
          </div>
          <div class="col-md-2">
            <label class="form-label">Duration</label>
            <select name="duration" class="form-select">
              <option value="">Any</option>
              <option value="hourly">Hourly</option>
              <option value="daily">Daily</option>
              <option value="weekly">Weekly</option>
              <option value="monthly">Monthly</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Type</label>
            <select name="mode" class="form-select">
              <option value="">Rent or Buy</option>
              <option value="rental">For Rent</option>
              <option value="sale">For Sale</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">&nbsp;</label>
            <button type="submit" class="btn btn-search w-100"><i class="fas fa-search me-2"></i> Search</button>
          </div>
        </div>
      </form>
    </div>

    <div class="stat-strip">
      <div class="row g-3 text-center">
        <div class="col-4 stat-item">
          <div class="val">{{ number_format($stats['total_listings']) }}+</div>
          <div class="label">Active Listings</div>
        </div>
        <div class="col-4 stat-item">
          <div class="val">{{ number_format($stats['total_yards']) }}+</div>
          <div class="label">Verified Yards</div>
        </div>
        <div class="col-4 stat-item">
          <div class="val">{{ number_format($stats['total_bookings']) }}+</div>
          <div class="label">Completed Rentals</div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="py-4" style="background:var(--dark2);border-bottom:1px solid var(--border);">
  <div class="container">
    <div class="d-flex gap-3 flex-wrap">
      <a href="{{ route('marketplace') }}" class="cat-pill {{ !request('category') ? 'active' : '' }}">
        <i class="fas fa-th-large"></i> All
      </a>
      @foreach($categories as $cat)
        <a href="{{ route('marketplace') }}?category={{ $cat->slug }}" class="cat-pill">
          {{ $cat->name }}
        </a>
      @endforeach
    </div>
  </div>
</div>

@if($featuredListings->isNotEmpty())
<div class="py-5">
  <div class="container">
    <div class="section-tag mb-1">Hand-Picked</div>
    <h2 class="section-title mb-4">Featured Listings</h2>
    <div class="row g-4">
      @foreach($featuredListings as $listing)
        <div class="col-md-6 col-lg-3">
          @include('partials.listing-card', ['listing' => $listing])
        </div>
      @endforeach
    </div>
  </div>
</div>
@endif

<div class="py-5" style="background:var(--dark2);">
  <div class="container">
    <div class="d-flex justify-content-between align-items-end mb-4">
      <div>
        <div class="section-tag mb-1">Recently Added</div>
        <h2 class="section-title mb-0">Latest Listings</h2>
      </div>
      <a href="{{ route('marketplace') }}" class="btn btn-outline-amber">View All <i class="fas fa-arrow-right ms-1"></i></a>
    </div>
    <div class="row g-4">
      @forelse($latestListings as $listing)
        <div class="col-md-6 col-lg-3">
          @include('partials.listing-card', ['listing' => $listing])
        </div>
      @empty
        <div class="col-12 text-center py-5">
          <div style="font-size:50px;margin-bottom:16px;">🚗</div>
          <p style="color:var(--muted);">No listings yet. <a href="{{ route('listings.create') }}">Be the first to list!</a></p>
        </div>
      @endforelse
    </div>
  </div>
</div>

<div class="py-5">
  <div class="container">
    <div class="text-center mb-5">
      <div class="section-tag" style="justify-content:center;display:flex;">How It Works</div>
      <h2 class="section-title">Rent in 3 Simple Steps</h2>
    </div>
    <div class="row g-4 text-center">
      <div class="col-md-4">
        <div class="toy-card h-100">
          <div style="font-size:48px;margin-bottom:16px;">🔍</div>
          <h4 style="color:#fff;font-weight:600;margin-bottom:10px;">1. Find & Book</h4>
          <p style="color:var(--muted);font-size:13px;">Browse verified listings. Check live availability. Select your dates and duration type.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="toy-card h-100">
          <div style="font-size:48px;margin-bottom:16px;">💚</div>
          <h4 style="color:#fff;font-weight:600;margin-bottom:10px;">2. Pay with M-Pesa</h4>
          <p style="color:var(--muted);font-size:13px;">Pay instantly via M-Pesa STK Push. Funds held in escrow until rental is complete.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="toy-card h-100">
          <div style="font-size:48px;margin-bottom:16px;">🚗</div>
          <h4 style="color:#fff;font-weight:600;margin-bottom:10px;">3. Drive & Return</h4>
          <p style="color:var(--muted);font-size:13px;">Pickup location revealed after payment. Complete rental and rate your experience.</p>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="py-5" style="background:var(--dark2);border-top:1px solid var(--border);">
  <div class="container">
    <div class="row g-4">
      <div class="col-md-6">
        <div class="toy-card h-100" style="border-color:rgba(46,204,138,0.3);">
          <div style="font-size:36px;margin-bottom:12px;">🏭</div>
          <h3 style="color:#fff;font-family:'Cormorant Garamond',serif;font-size:26px;font-weight:700;">Own a Vehicle or Yard?</h3>
          <p style="color:var(--muted);">List your fleet and earn passive income. Our platform handles bookings, payments, and escrow automatically.</p>
          <a href="{{ route('register') }}" class="btn btn-amber mt-2">List Your Vehicle <i class="fas fa-arrow-right ms-1"></i></a>
        </div>
      </div>
      <div class="col-md-6">
        <div class="toy-card h-100" style="border-color:rgba(74,159,224,0.3);">
          <div style="font-size:36px;margin-bottom:12px;">🤝</div>
          <h3 style="color:#fff;font-family:'Cormorant Garamond',serif;font-size:26px;font-weight:700;">Become a Broker</h3>
          <p style="color:var(--muted);">Share your referral link and earn commissions on every booking you bring in. No extra investment required.</p>
          <a href="{{ route('broker.register') }}" class="btn btn-outline-amber mt-2">Start Earning <i class="fas fa-arrow-right ms-1"></i></a>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
