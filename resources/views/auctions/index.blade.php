@extends('layouts.app')
@section('title', 'Live Property Auctions — EstateYard')
@section('content')
<div style="padding-top:90px; background:var(--navy); min-height:100vh;">

  <!-- Header -->
  <div style="background:linear-gradient(160deg,var(--navy2),var(--navy3)); border-bottom:1px solid var(--border); padding:48px 0;">
    <div class="container">
      <div class="section-tag">🔨 Property Auctions</div>
      <h1 style="font-family:var(--font-serif); font-size:clamp(36px,5vw,60px); font-weight:700; color:var(--white); margin:12px 0;">Live Property Auctions</h1>
      <p style="color:var(--muted); font-size:16px; max-width:600px; line-height:1.7;">Bid on verified properties in real-time. KYC-verified bidders, reserve prices, anti-sniping protection, and escrow-backed winning bids.</p>
      <div style="display:flex; gap:20px; margin-top:28px; flex-wrap:wrap;">
        <div style="background:var(--surface); border:1px solid rgba(224,82,82,0.3); border-radius:10px; padding:12px 20px; display:flex; gap:10px; align-items:center;">
          <span style="font-size:22px;">🔴</span>
          <div><div style="font-family:var(--font-serif); font-size:22px; font-weight:700; color:var(--red);">14</div><div style="font-size:11px; color:var(--muted); font-family:var(--font-mono);">LIVE NOW</div></div>
        </div>
        <div style="background:var(--surface); border:1px solid var(--border); border-radius:10px; padding:12px 20px; display:flex; gap:10px; align-items:center;">
          <span style="font-size:22px;">⏰</span>
          <div><div style="font-family:var(--font-serif); font-size:22px; font-weight:700; color:var(--gold);">38</div><div style="font-size:11px; color:var(--muted); font-family:var(--font-mono);">UPCOMING</div></div>
        </div>
        <div style="background:var(--surface); border:1px solid var(--border); border-radius:10px; padding:12px 20px; display:flex; gap:10px; align-items:center;">
          <span style="font-size:22px;">👥</span>
          <div><div style="font-family:var(--font-serif); font-size:22px; font-weight:700; color:var(--blue);">1,247</div><div style="font-size:11px; color:var(--muted); font-family:var(--font-mono);">REGISTERED BIDDERS</div></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Live Auctions -->
  <div class="container" style="padding:48px 32px;">
    <div style="display:flex; align-items:center; gap:10px; margin-bottom:28px;">
      <span style="width:10px; height:10px; border-radius:50%; background:var(--red); animation:pulse-dot 1s infinite;"></span>
      <h2 style="font-size:22px; font-weight:600; color:var(--white);">Live Auctions</h2>
    </div>

    <div class="property-grid">
      @foreach([
        ['🏢','Commercial Block','Nairobi CBD','Currently: $512,000','Reserve: $480,000','2h 14m',24,'Auctioneer: David K.'],
        ['🏡','Luxury Home','Muthaiga','Currently: $180,000','Reserve: $165,000','5h 30m',12,'Auctioneer: Sarah W.'],
        ['🏗️','Office Complex','Upper Hill','Currently: $2.1M','Reserve: $1.9M','1d 2h',8,'Auctioneer: James M.'],
        ['🌿','Prime Land','Kiambu Road','Currently: $85,000','Reserve: $75,000','3h 45m',18,'Auctioneer: Alice N.'],
      ] as $i => $au)
      <div class="property-card" style="border-color:rgba(224,82,82,0.25);">
        <div class="property-card-img" style="background:linear-gradient(135deg,rgba(224,82,82,0.08),var(--navy3));">
          <span>{{ $au[0] }}</span>
          <div class="property-card-badges">
            <span class="badge badge-red">🔴 LIVE</span>
            <span class="badge badge-green">✓ Verified</span>
          </div>
          <div style="position:absolute; bottom:12px; left:0; right:0; text-align:center;">
            <div style="display:inline-block; background:rgba(8,17,31,0.9); border:1px solid rgba(224,82,82,0.4); border-radius:8px; padding:8px 20px;">
              <div style="font-size:9px; font-family:var(--font-mono); color:var(--red); letter-spacing:2px; margin-bottom:2px;">ENDS IN</div>
              <div style="font-size:18px; font-weight:700; color:var(--white); font-family:var(--font-serif);" data-countdown="{{ time() + (($i+1)*7200) }}">{{ $au[5] }}</div>
            </div>
          </div>
        </div>
        <div class="property-card-body">
          <div class="property-card-type" style="color:var(--red);">🔴 Live Auction · Escrow Protected</div>
          <div class="property-card-title">{{ $au[1] }}</div>
          <div class="property-card-location">📍 {{ $au[2] }}</div>
          <div class="property-card-specs">
            <span class="property-card-spec">👥 {{ $au[6] }} bidders</span>
            <span class="property-card-spec">{{ $au[7] }}</span>
          </div>
          <div style="background:var(--surface); border-radius:8px; padding:12px; margin-bottom:14px;">
            <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
              <span style="font-size:12px; color:var(--muted);">Current Bid</span>
              <span style="font-size:12px; color:var(--muted);">{{ $au[4] }}</span>
            </div>
            <div style="font-family:var(--font-serif); font-size:24px; font-weight:700; color:var(--red);">{{ $au[3] }}</div>
          </div>
          <div class="property-card-footer">
            <a href="{{ url('/register') }}" class="btn btn-sm" style="background:rgba(224,82,82,0.08); border:1px solid rgba(224,82,82,0.25); color:var(--red);">Register to Bid</a>
            <a href="{{ url('/auctions/'.($i+1)) }}" class="btn btn-gold btn-sm">View Auction →</a>
          </div>
        </div>
      </div>
      @endforeach
    </div>

    <!-- Upcoming Auctions -->
    <div style="margin-top:60px;">
      <h2 style="font-size:22px; font-weight:600; color:var(--white); margin-bottom:24px;">⏰ Upcoming Auctions</h2>
      <div class="table-card">
        <table class="data-table">
          <thead><tr><th>Property</th><th>Location</th><th>Starting Bid</th><th>Reserve</th><th>Auction Date</th><th>Auctioneer</th><th></th></tr></thead>
          <tbody>
            @foreach([
              ['🏠','4-Bed Villa','Karen','$280,000','$250,000','Jun 5, 2026 · 10:00 AM','David K.'],
              ['🏢','Office Suite','Westlands','$450,000','$420,000','Jun 8, 2026 · 2:00 PM','Sarah W.'],
              ['🌿','Agricultural Land','Nakuru','$95,000','$80,000','Jun 10, 2026 · 9:00 AM','James M.'],
              ['🏗️','Off-Plan Units (x5)','Kilimani','$750,000','$700,000','Jun 15, 2026 · 11:00 AM','Alice N.'],
              ['🏭','Warehouse','Syokimau','$1.2M','$1.1M','Jun 20, 2026 · 3:00 PM','Peter O.'],
            ] as $up)
            <tr>
              <td><span class="td-name">{{ $up[0] }} {{ $up[1] }}</span></td>
              <td>📍 {{ $up[2] }}</td>
              <td class="td-price">{{ $up[3] }}</td>
              <td>{{ $up[4] }}</td>
              <td style="color:var(--gold);">{{ $up[5] }}</td>
              <td>{{ $up[6] }}</td>
              <td><a href="{{ url('/register') }}" class="btn btn-sm btn-outline">Register →</a></td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
