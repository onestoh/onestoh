<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'EstateYard') — Real Estate ERP Platform</title>
  <meta name="description" content="@yield('meta_description', 'EstateYard — The all-in-one Real Estate Enterprise Resource Platform for Africa and beyond.')">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Outfit:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

  <!-- EstateYard CSS -->
  <link rel="stylesheet" href="{{ asset('css/estateyard.css') }}">

  @stack('styles')
</head>
<body>

  <!-- NAVBAR -->
  <nav class="navbar" id="navbar">
    <div class="navbar-inner">
      <a href="{{ url('/') }}" class="navbar-brand">Estate<span>Yard</span></a>

      <ul class="navbar-nav">
        <li><a href="{{ url('/marketplace') }}" class="{{ request()->is('marketplace*') ? 'active' : '' }}">Marketplace</a></li>
        <li><a href="{{ url('/marketplace?type=houses') }}">Buy</a></li>
        <li><a href="{{ url('/marketplace?type=rent') }}">Rent</a></li>
        <li><a href="{{ url('/marketplace?type=airbnb') }}">Short Stay</a></li>
        <li><a href="{{ url('/marketplace?type=hotels') }}">Hotels</a></li>
        <li><a href="{{ url('/marketplace?type=land') }}">Land</a></li>
        <li><a href="{{ url('/auctions') }}">Auctions</a></li>
        <li><a href="{{ url('/commercial') }}">Commercial</a></li>
      </ul>

      <div class="navbar-actions">
        <a href="{{ url('/login') }}" class="btn btn-outline btn-sm">Sign In</a>
        <a href="{{ url('/register') }}" class="btn btn-gold btn-sm">Join Free</a>
        <div class="navbar-toggle" onclick="toggleMobileNav()">
          <span></span><span></span><span></span>
        </div>
      </div>
    </div>

    <!-- Mobile Nav -->
    <div class="mobile-nav" id="mobileNav" style="display:none; background:var(--navy2); border-top:1px solid var(--border); padding:16px 20px;">
      <ul style="list-style:none; display:flex; flex-direction:column; gap:4px;">
        <li><a href="{{ url('/marketplace') }}" style="display:block; padding:10px 14px; color:var(--muted); border-radius:8px;" onmouseover="this.style.background='var(--gold-dim)'" onmouseout="this.style.background='transparent'">Marketplace</a></li>
        <li><a href="{{ url('/marketplace?type=houses') }}" style="display:block; padding:10px 14px; color:var(--muted); border-radius:8px;">Buy</a></li>
        <li><a href="{{ url('/marketplace?type=rent') }}" style="display:block; padding:10px 14px; color:var(--muted); border-radius:8px;">Rent</a></li>
        <li><a href="{{ url('/marketplace?type=airbnb') }}" style="display:block; padding:10px 14px; color:var(--muted); border-radius:8px;">Short Stay</a></li>
        <li><a href="{{ url('/marketplace?type=hotels') }}" style="display:block; padding:10px 14px; color:var(--muted); border-radius:8px;">Hotels</a></li>
        <li><a href="{{ url('/marketplace?type=land') }}" style="display:block; padding:10px 14px; color:var(--muted); border-radius:8px;">Land</a></li>
        <li><a href="{{ url('/auctions') }}" style="display:block; padding:10px 14px; color:var(--muted); border-radius:8px;">Auctions</a></li>
      </ul>
      <div style="display:flex; gap:10px; margin-top:16px; padding-top:16px; border-top:1px solid var(--border);">
        <a href="{{ url('/login') }}" class="btn btn-outline btn-sm" style="flex:1; justify-content:center;">Sign In</a>
        <a href="{{ url('/register') }}" class="btn btn-gold btn-sm" style="flex:1; justify-content:center;">Join Free</a>
      </div>
    </div>
  </nav>

  <!-- MAIN CONTENT -->
  @yield('content')

  <!-- FOOTER -->
  <footer class="footer">
    <div class="container">
      <div class="footer-grid">
        <div>
          <div class="footer-brand">Estate<span>Yard</span></div>
          <p class="footer-desc">The all-in-one Real Estate Enterprise Resource Platform — marketplace, escrow, rent automation, auctions, hospitality PMS, and more.</p>
          <div class="footer-socials">
            <div class="social-btn">📘</div>
            <div class="social-btn">📷</div>
            <div class="social-btn">🐦</div>
            <div class="social-btn">💼</div>
            <div class="social-btn">🎵</div>
          </div>
        </div>
        <div>
          <div class="footer-heading">Marketplace</div>
          <ul class="footer-links">
            <li><a href="{{ url('/marketplace?type=houses') }}">Buy a Home</a></li>
            <li><a href="{{ url('/marketplace?type=rent') }}">Rent a Home</a></li>
            <li><a href="{{ url('/marketplace?type=land') }}">Buy Land</a></li>
            <li><a href="{{ url('/marketplace?type=commercial') }}">Commercial</a></li>
            <li><a href="{{ url('/marketplace?type=airbnb') }}">Short Stay</a></li>
            <li><a href="{{ url('/auctions') }}">Auctions</a></li>
          </ul>
        </div>
        <div>
          <div class="footer-heading">Platform</div>
          <ul class="footer-links">
            <li><a href="{{ url('/register?role=landlord') }}">List a Property</a></li>
            <li><a href="{{ url('/register?role=broker') }}">Become a Broker</a></li>
            <li><a href="{{ url('/register?role=promoter') }}">Earn Referrals</a></li>
            <li><a href="{{ url('/financing') }}">Property Finance</a></li>
            <li><a href="{{ url('/verification') }}">Get Verified</a></li>
            <li><a href="{{ url('/estateyard-sites') }}">EstateYard Sites</a></li>
          </ul>
        </div>
        <div>
          <div class="footer-heading">Professionals</div>
          <ul class="footer-links">
            <li><a href="{{ url('/register?role=valuer') }}">Valuers</a></li>
            <li><a href="{{ url('/register?role=surveyor') }}">Surveyors</a></li>
            <li><a href="{{ url('/register?role=auctioneer') }}">Auctioneers</a></li>
            <li><a href="{{ url('/register?role=developer') }}">Developers</a></li>
            <li><a href="{{ url('/register?role=corporate') }}">Corporate</a></li>
            <li><a href="{{ url('/register?role=investor') }}">Investors</a></li>
          </ul>
        </div>
        <div>
          <div class="footer-heading">Company</div>
          <ul class="footer-links">
            <li><a href="{{ url('/about') }}">About Us</a></li>
            <li><a href="{{ url('/careers') }}">Careers</a></li>
            <li><a href="{{ url('/press') }}">Press</a></li>
            <li><a href="{{ url('/blog') }}">Blog</a></li>
            <li><a href="{{ url('/contact') }}">Contact</a></li>
            <li><a href="{{ url('/help') }}">Help Center</a></li>
          </ul>
        </div>
      </div>

      <div class="footer-bottom">
        <div class="footer-copy">© {{ date('Y') }} EstateYard. All rights reserved. · Built for Africa & beyond.</div>
        <div style="display:flex; gap:20px; font-size:13px;">
          <a href="{{ url('/privacy') }}" style="color:var(--muted);">Privacy</a>
          <a href="{{ url('/terms') }}" style="color:var(--muted);">Terms</a>
          <a href="{{ url('/cookies') }}" style="color:var(--muted);">Cookies</a>
        </div>
      </div>
    </div>
  </footer>

  <script src="{{ asset('js/app.js') }}"></script>
  @stack('scripts')

  <script>
    function toggleMobileNav() {
      const nav = document.getElementById('mobileNav');
      nav.style.display = nav.style.display === 'none' ? 'block' : 'none';
    }

    // Navbar scroll effect
    window.addEventListener('scroll', () => {
      const navbar = document.getElementById('navbar');
      if (window.scrollY > 50) {
        navbar.style.background = 'rgba(8,17,31,0.98)';
      } else {
        navbar.style.background = 'rgba(8,17,31,0.92)';
      }
    });
  </script>
</body>
</html>
