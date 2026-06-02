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
      <!-- Mobile Search -->
      <form method="GET" action="{{ url('/search') }}" style="margin-bottom:16px;">
        <input type="text" name="q" placeholder="Search properties..." style="width:100%; background:var(--navy3); border:1px solid var(--border-dim); border-radius:8px; padding:10px 16px; color:var(--white); font-size:14px; outline:none; box-sizing:border-box;">
      </form>
      <ul style="list-style:none; display:flex; flex-direction:column; gap:4px;">
        <li><a href="{{ url('/marketplace') }}" style="display:block; padding:10px 14px; color:var(--muted); border-radius:8px;">🏠 Marketplace</a></li>
        <li><a href="{{ url('/marketplace?type=houses') }}" style="display:block; padding:10px 14px; color:var(--muted); border-radius:8px;">🏡 Buy a Home</a></li>
        <li><a href="{{ url('/marketplace?type=rent') }}" style="display:block; padding:10px 14px; color:var(--muted); border-radius:8px;">🏘️ Rent a Home</a></li>
        <li><a href="{{ url('/marketplace?type=airbnb') }}" style="display:block; padding:10px 14px; color:var(--muted); border-radius:8px;">✈️ Short Stay / Airbnb</a></li>
        <li><a href="{{ url('/marketplace?type=hotels') }}" style="display:block; padding:10px 14px; color:var(--muted); border-radius:8px;">🏨 Hotels</a></li>
        <li><a href="{{ url('/marketplace?type=land') }}" style="display:block; padding:10px 14px; color:var(--muted); border-radius:8px;">🌿 Buy Land</a></li>
        <li><a href="{{ url('/commercial') }}" style="display:block; padding:10px 14px; color:var(--muted); border-radius:8px;">🏢 Commercial</a></li>
        <li><a href="{{ url('/auctions') }}" style="display:block; padding:10px 14px; color:var(--muted); border-radius:8px;">🔨 Auctions</a></li>
        <li><a href="{{ url('/financing') }}" style="display:block; padding:10px 14px; color:var(--muted); border-radius:8px;">💰 Property Finance</a></li>
        <li><a href="{{ url('/about') }}" style="display:block; padding:10px 14px; color:var(--muted); border-radius:8px;">ℹ️ About Us</a></li>
      </ul>
      <div style="display:flex; gap:10px; margin-top:16px; padding-top:16px; border-top:1px solid var(--border);">
        <a href="{{ url('/login') }}" class="btn btn-outline" style="flex:1; justify-content:center; padding:12px;">Sign In</a>
        <a href="{{ url('/register') }}" class="btn btn-gold" style="flex:1; justify-content:center; padding:12px;">Join Free</a>
      </div>
    </div>
  </nav>

  <!-- MAIN CONTENT -->
  @yield('content')

  <!-- FOOTER -->
  <footer class="footer">
    <div class="container">
      <!-- Newsletter Signup -->
      <div style="background:var(--navy3);border-radius:var(--radius);padding:40px;margin-bottom:48px;display:flex;align-items:center;justify-content:space-between;gap:24px;flex-wrap:wrap;">
        <div>
          <div style="font-family:var(--font-serif);font-size:24px;color:var(--white);margin-bottom:6px;">Stay ahead of the market</div>
          <div style="font-size:14px;color:var(--muted);">Weekly property deals, market insights and investment tips — delivered free.</div>
        </div>
        <form style="display:flex;gap:10px;flex-wrap:wrap;" onsubmit="subscribeNewsletter(event)">
          <input type="email" placeholder="your@email.com" style="background:var(--navy2);border:1px solid var(--border);border-radius:8px;padding:12px 18px;color:var(--text);font-size:14px;outline:none;min-width:240px;" required>
          <button type="submit" class="btn btn-gold">Subscribe Free</button>
        </form>
      </div>
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

  <!-- WhatsApp Support -->
  <a href="https://wa.me/{{ config('estateyard.whatsapp_support') }}?text=Hello%20EstateYard%20Support%2C%20I%20need%20help%20with..."
     target="_blank" rel="noopener noreferrer"
     style="position:fixed;bottom:28px;right:28px;z-index:9998;width:56px;height:56px;border-radius:50%;background:#25D366;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 20px rgba(37,211,102,0.4);text-decoration:none;transition:transform .2s,box-shadow .2s;"
     onmouseover="this.style.transform='scale(1.1)';this.style.boxShadow='0 6px 28px rgba(37,211,102,0.6)'"
     onmouseout="this.style.transform='scale(1)';this.style.boxShadow='0 4px 20px rgba(37,211,102,0.4)'"
     title="Chat with EstateYard on WhatsApp">
    <svg width="28" height="28" viewBox="0 0 24 24" fill="white"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
    <span style="position:absolute;width:56px;height:56px;border-radius:50%;background:rgba(37,211,102,0.3);animation:waPulse 2s infinite;"></span>
  </a>
  <style>@keyframes waPulse{0%{transform:scale(1);opacity:1}100%{transform:scale(1.7);opacity:0}}</style>

  <script>
    function toggleMobileNav() {
      const nav = document.getElementById('mobileNav');
      nav.style.display = nav.style.display === 'none' ? 'block' : 'none';
    }

    // Close mobile nav on outside click
    document.addEventListener('click', function(e) {
      const nav = document.getElementById('mobileNav');
      const navbar = document.getElementById('navbar');
      if (nav && nav.style.display !== 'none' && !navbar.contains(e.target)) {
        nav.style.display = 'none';
      }
    });

    // Newsletter subscription
    function subscribeNewsletter(e) {
      e.preventDefault();
      const email = e.target.querySelector('input').value;
      // Show toast if available, else alert
      if (typeof showToast === 'function') {
        showToast('Subscribed! Watch your inbox for market updates.', 'green');
      } else {
        alert('Subscribed! Watch your inbox for market updates.');
      }
      e.target.reset();
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
