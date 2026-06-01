@extends('layouts.app')
@section('title', 'Live Auction — EstateYard')
@section('content')
<div style="padding-top:90px; background:var(--navy); min-height:100vh;">
  <div class="container" style="padding:40px 32px;">
    <div style="display:grid; grid-template-columns:1fr 380px; gap:32px; align-items:start;">

      <!-- Left -->
      <div>
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:20px;">
          <a href="{{ url('/auctions') }}" style="color:var(--muted); font-size:13px;">← All Auctions</a>
          <span style="color:var(--muted);">/</span>
          <span style="font-size:13px; color:var(--muted);">Auction #{{ $auction->id }}</span>
        </div>

        <!-- Property Info -->
        <div style="background:var(--navy3); border:1px solid rgba(224,82,82,0.25); border-radius:var(--radius); overflow:hidden; margin-bottom:24px;">
          <div style="height:280px; background:linear-gradient(135deg,rgba(224,82,82,0.08),var(--navy3)); display:flex; align-items:center; justify-content:center; font-size:80px; position:relative;">
            🏢
            <div style="position:absolute; top:16px; left:16px; display:flex; gap:8px;">
              <span class="badge badge-red">🔴 LIVE AUCTION</span>
              <span class="badge badge-green">✓ Verified</span>
            </div>
          </div>
          <div style="padding:24px;">
            <h1 style="font-family:var(--font-serif); font-size:32px; font-weight:700; color:var(--white); margin-bottom:8px;">Commercial Block, Nairobi CBD</h1>
            <div style="font-size:14px; color:var(--muted); margin-bottom:16px;">📍 Upper Hill, Nairobi · 2,500m² · Grade A Office</div>
            <div style="display:flex; gap:20px; flex-wrap:wrap;">
              <div class="property-card-spec">🏢 Commercial</div>
              <div class="property-card-spec">📐 2,500m²</div>
              <div class="property-card-spec">🏗 12 Floors</div>
              <div class="property-card-spec">🚗 200 parking spots</div>
            </div>
          </div>
        </div>

        <!-- Bid Feed -->
        <div class="table-card">
          <div class="table-card-header">
            <div class="table-card-title">📡 Live Bid Feed</div>
            <div class="live-badge">REAL-TIME</div>
          </div>
          <table class="data-table">
            <thead><tr><th>Bidder</th><th>Amount</th><th>Time</th><th>Status</th></tr></thead>
            <tbody>
              @foreach([['Bidder #24','$512,000','Just now','CURRENT HIGH'],['Bidder #11','$508,000','2 min ago','Outbid'],['Bidder #24','$502,000','5 min ago','Outbid'],['Bidder #07','$495,000','8 min ago','Outbid'],['Bidder #31','$490,000','12 min ago','Outbid'],['Bidder #15','$485,000','18 min ago','Outbid']] as $i => $bid)
              <tr>
                <td class="td-name">{{ $bid[0] }}</td>
                <td class="td-price">{{ $bid[1] }}</td>
                <td style="font-family:var(--font-mono); font-size:12px;">{{ $bid[2] }}</td>
                <td><span class="status-pill {{ $i===0 ? 'status-active' : 'status-draft' }}">{{ $bid[3] }}</span></td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <!-- Property Description -->
        <div style="background:var(--navy3); border:1px solid var(--border-dim); border-radius:var(--radius); padding:24px; margin-top:24px;">
          <h3 style="font-size:16px; font-weight:600; color:var(--white); margin-bottom:14px;">Property Description</h3>
          <p style="color:var(--muted); font-size:14px; line-height:1.8;">Prime A-Grade commercial building in the heart of Nairobi's Upper Hill business district. The property features modern open-plan offices, floor-to-ceiling glazing, centralized HVAC, raised access floors for data cabling, and a backup generator. Strategically located with easy access to major roads and public transport.</p>
          <div style="margin-top:20px; padding-top:16px; border-top:1px solid var(--border-dim);">
            <div class="section-tag" style="margin-bottom:12px;">Documents on File</div>
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
              @foreach(['📋 Title Deed', '🔍 Valuation Report', '📐 Survey Report', '📄 Sale Agreement Draft', '🏗 Building Plans'] as $doc)
              <div style="background:var(--surface); border:1px solid var(--border-dim); border-radius:6px; padding:6px 14px; font-size:12px; color:var(--muted);">{{ $doc }}</div>
              @endforeach
            </div>
          </div>
        </div>
      </div>

      <!-- Right — Bid Widget -->
      <div style="position:sticky; top:100px;">
        <div class="auction-live">
          <div style="text-align:center; margin-bottom:20px;">
            <div style="font-size:12px; font-family:var(--font-mono); letter-spacing:2px; color:var(--red); margin-bottom:8px;">⏱ AUCTION ENDS IN</div>
            <div class="countdown-timer" data-countdown="{{ time() + 8040 }}">
              <div class="countdown-unit"><span class="countdown-num">02</span><span class="countdown-label">Hours</span></div>
              <div class="countdown-unit"><span class="countdown-num">14</span><span class="countdown-label">Mins</span></div>
              <div class="countdown-unit"><span class="countdown-num">00</span><span class="countdown-label">Secs</span></div>
            </div>
          </div>

          <div style="background:rgba(0,0,0,0.2); border-radius:10px; padding:16px; margin-bottom:16px; text-align:center;">
            <div style="font-size:12px; color:var(--muted); margin-bottom:4px;">Current Highest Bid</div>
            <div style="font-family:var(--font-serif); font-size:42px; font-weight:700; color:var(--gold); line-height:1;">$512,000</div>
            <div style="font-size:12px; color:var(--muted); margin-top:4px;">Reserve: $480,000 · ✓ Reserve Met</div>
          </div>

          <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:16px;">
            <div style="background:rgba(0,0,0,0.2); border-radius:8px; padding:12px; text-align:center;">
              <div style="font-size:20px; font-weight:700; color:var(--white); font-family:var(--font-serif);">24</div>
              <div style="font-size:11px; color:var(--muted); font-family:var(--font-mono);">BIDDERS</div>
            </div>
            <div style="background:rgba(0,0,0,0.2); border-radius:8px; padding:12px; text-align:center;">
              <div style="font-size:20px; font-weight:700; color:var(--white); font-family:var(--font-serif);">87</div>
              <div style="font-size:11px; color:var(--muted); font-family:var(--font-mono);">TOTAL BIDS</div>
            </div>
          </div>

          <div style="margin-bottom:16px;">
            <div class="form-label" style="margin-bottom:8px;">Your Bid Amount</div>
            <div style="display:flex; gap:8px; margin-bottom:8px;">
              @foreach(['$515,000','$520,000','$525,000'] as $val)
              <button onclick="document.getElementById('bidAmount').value='{{ str_replace(',','',str_replace('$','', $val)) }}'" class="btn btn-sm btn-outline" style="flex:1; justify-content:center; font-size:12px;">{{ $val }}</button>
              @endforeach
            </div>
            <input type="number" id="bidAmount" class="form-control" placeholder="Enter bid amount" value="515000" min="513000" step="1000">
            <div style="font-size:11px; color:var(--muted); margin-top:6px; font-family:var(--font-mono);">Min next bid: $513,000 · Increment: $1,000</div>
          </div>

          <a href="{{ url('/register') }}" class="btn btn-gold" style="width:100%; justify-content:center; font-size:15px; padding:14px; margin-bottom:12px;">🔨 Place Bid</a>
          <a href="{{ url('/register') }}" class="btn btn-outline btn-sm" style="width:100%; justify-content:center;">Register to Bid (KYC Required)</a>

          <div style="margin-top:16px; padding-top:14px; border-top:1px solid rgba(255,255,255,0.1);">
            <div style="font-size:12px; color:var(--muted); text-align:center; line-height:1.6;">
              🔒 Winning bid is held in escrow until all conditions are met.<br>
              Refundable deposit required to register.
            </div>
          </div>
        </div>

        <!-- Auctioneer Info -->
        <div style="background:var(--navy3); border:1px solid var(--border-dim); border-radius:var(--radius); padding:20px; margin-top:16px;">
          <div class="section-tag" style="margin-bottom:12px;">Auctioneer</div>
          <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:48px; height:48px; border-radius:50%; background:var(--gold-dim); border:2px solid var(--gold); display:flex; align-items:center; justify-content:center; font-size:22px;">👨‍⚖️</div>
            <div>
              <div style="font-size:15px; font-weight:600; color:var(--white);">David Kimani</div>
              <div class="verified-badge">✓ LICENSED AUCTIONEER</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
