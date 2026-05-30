@extends('layouts.app')
@section('title', 'Property Marketplace — EstateYard')
@section('content')
<div style="padding-top:70px; background:var(--navy); min-height:100vh;">

  <!-- Search Header -->
  <div style="background:var(--navy2); border-bottom:1px solid var(--border); padding:20px 0;">
    <div class="container">
      <div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
        <div class="search-bar" style="max-width:450px;">
          <span style="color:var(--muted);">🔍</span>
          <input type="text" class="search-input" placeholder="Search properties..." value="{{ request('q') }}">
        </div>
        <div style="display:flex; gap:6px; overflow-x:auto;">
          @foreach([['All',''],['Buy','buy'],['Rent','rent'],['Short Stay','airbnb'],['Hotels','hotels'],['Land','land'],['Commercial','commercial'],['Auctions','auction']] as $t)
          <a href="{{ url('/marketplace?type=' . $t[1]) }}" style="white-space:nowrap; padding:8px 16px; border-radius:100px; font-size:13px; font-weight:500; background:{{ request('type')===$t[1] ? 'var(--gold)' : 'var(--navy3)' }}; border:1px solid {{ request('type')===$t[1] ? 'var(--gold)' : 'var(--border-dim)' }}; color:{{ request('type')===$t[1] ? 'var(--navy)' : 'var(--muted)' }}; text-decoration:none;">{{ $t[0] }}</a>
          @endforeach
        </div>
        <button id="filterToggle" class="btn btn-outline btn-sm">⚙ Filters</button>
        <select class="form-control" style="width:auto; padding:8px 16px; font-size:13px;">
          <option>Newest First</option><option>Price: Low to High</option><option>Price: High to Low</option><option>Most Viewed</option>
        </select>
      </div>
    </div>
  </div>

  <div class="container" style="padding:32px;">
    <div style="display:grid; grid-template-columns:270px 1fr; gap:28px;">

      <!-- Filters Sidebar -->
      <div id="filterDrawer" style="display:block;">
        <div style="position:sticky; top:90px;">
          <div style="background:var(--navy2); border:1px solid var(--border-dim); border-radius:var(--radius); padding:24px;">
            <h3 style="font-size:14px; font-weight:600; color:var(--white); margin-bottom:20px; display:flex; justify-content:space-between;">Filters <span style="color:var(--gold); font-size:12px; cursor:pointer;">Clear All</span></h3>

            <div class="form-group">
              <div class="form-label">Property Type</div>
              @foreach(['House','Apartment','Land/Plot','Off-Plan','Hotel/Airbnb','Commercial','Industrial'] as $type)
              <label style="display:flex; align-items:center; gap:8px; padding:6px 0; cursor:pointer; font-size:13px; color:var(--muted);">
                <input type="checkbox" style="accent-color:var(--gold);"> {{ $type }}
              </label>
              @endforeach
            </div>

            <div class="form-group" style="margin-top:20px;">
              <div class="form-label">Price Range</div>
              <div style="display:flex; gap:8px;">
                <input type="number" class="form-control" placeholder="Min" style="flex:1;">
                <input type="number" class="form-control" placeholder="Max" style="flex:1;">
              </div>
              <input type="range" min="0" max="5000000" step="50000" value="2000000" style="width:100%; accent-color:var(--gold); margin-top:10px;" data-output="priceOutput">
              <div style="font-size:12px; color:var(--gold); margin-top:4px; font-family:var(--font-mono);" id="priceOutput">$2,000,000</div>
            </div>

            <div class="form-group" style="margin-top:20px;">
              <div class="form-label">Bedrooms</div>
              <div style="display:flex; gap:6px; flex-wrap:wrap;">
                @foreach(['Any','1','2','3','4','5+'] as $beds)
                <button onclick="toggleFilter(this)" style="padding:6px 14px; border-radius:6px; font-size:12px; background:var(--navy3); border:1px solid var(--border-dim); color:var(--muted); cursor:pointer;">{{ $beds }}</button>
                @endforeach
              </div>
            </div>

            <div class="form-group" style="margin-top:20px;">
              <div class="form-label">Bathrooms</div>
              <div style="display:flex; gap:6px; flex-wrap:wrap;">
                @foreach(['Any','1','2','3','4+'] as $baths)
                <button onclick="toggleFilter(this)" style="padding:6px 14px; border-radius:6px; font-size:12px; background:var(--navy3); border:1px solid var(--border-dim); color:var(--muted); cursor:pointer;">{{ $baths }}</button>
                @endforeach
              </div>
            </div>

            <div class="form-group" style="margin-top:20px;">
              <div class="form-label">Amenities</div>
              @foreach(['Swimming Pool','Gym','Parking','Security','Generator','CCTV','Garden'] as $am)
              <label style="display:flex; align-items:center; gap:8px; padding:5px 0; cursor:pointer; font-size:13px; color:var(--muted);">
                <input type="checkbox" style="accent-color:var(--gold);"> {{ $am }}
              </label>
              @endforeach
            </div>

            <div class="form-group" style="margin-top:20px;">
              <div class="form-label">Listing Status</div>
              @foreach(['Verified Only','Featured','New (Last 7 days)','Price Reduced','With Virtual Tour'] as $st)
              <label style="display:flex; align-items:center; gap:8px; padding:5px 0; cursor:pointer; font-size:13px; color:var(--muted);">
                <input type="checkbox" style="accent-color:var(--gold);"> {{ $st }}
              </label>
              @endforeach
            </div>

            <button class="btn btn-gold" style="width:100%; justify-content:center; margin-top:20px;">Apply Filters</button>
          </div>
        </div>
      </div>

      <!-- Results -->
      <div>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
          <div style="font-size:14px; color:var(--muted);">Showing <span style="color:var(--white); font-weight:600;">24,183</span> properties</div>
          <div style="display:flex; gap:8px;">
            <button onclick="setView('grid')" class="icon-btn" title="Grid View">⊞</button>
            <button onclick="setView('list')" class="icon-btn" title="List View">☰</button>
            <button onclick="setView('map')" class="icon-btn" title="Map View">🗺</button>
          </div>
        </div>

        <div class="property-grid" id="listingsGrid">
          @php
          $listings = [
            ['🏠','For Sale','4-Bed Villa with Pool','Karen, Nairobi','4','3','340m²','KSh 28.5M',true,'green'],
            ['🏢','For Rent','2-Bed Penthouse','Westlands','2','2','120m²','KSh 95K/mo',true,'blue'],
            ['🏡','For Sale','3-Bed Townhouse','Kilimani','3','2','180m²','KSh 15.8M',true,'green'],
            ['🏠','For Rent','1-Bed Apartment','Lavington','1','1','65m²','KSh 45K/mo',false,'blue'],
            ['🏘️','For Sale','5-Bed Mansion','Runda Estate','5','4','600m²','KSh 85M',true,'gold'],
            ['🏢','For Rent','Studio Apartment','Upper Hill','0','1','42m²','KSh 32K/mo',true,'blue'],
            ['🌿','Land','Residential Plot','Kitengela','—','—','0.5 acres','KSh 2.1M',true,'green'],
            ['✈️','Short Stay','Beach House','Mombasa','3','2','200m²','$180/night',true,'teal'],
            ['🏨','Hotel','Grand Serena','Nairobi CBD','—','—','200 rooms','$150/night',true,'gold'],
            ['🏢','For Sale','Office Block','Upper Hill','—','—','2,500m²','KSh 180M',true,'purple'],
            ['🏗️','Off-Plan','New Apartments','Kilimani','2','2','90m²','KSh 8.5M',true,'blue'],
            ['🌾','Land','Farmland','Nakuru','—','—','5 acres','KSh 4.2M',true,'green'],
          ];
          @endphp

          @foreach($listings as $i => $p)
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
              <div class="property-card-type">{{ $p[2] !== '—' ? 'Residential' : 'Land/Commercial' }} · {{ $p[1] }}</div>
              <div class="property-card-title">{{ $p[2] }}</div>
              <div class="property-card-location">📍 {{ $p[3] }}</div>
              <div class="property-card-specs">
                @if($p[4] !== '—')<span class="property-card-spec">🛏 {{ $p[4] }}</span>@endif
                @if($p[5] !== '—')<span class="property-card-spec">🚿 {{ $p[5] }}</span>@endif
                <span class="property-card-spec">📐 {{ $p[6] }}</span>
              </div>
              <div class="property-card-footer">
                <div><div class="property-card-price">{{ $p[7] }}</div><div class="property-card-price-label">Escrow protected</div></div>
                <a href="{{ url('/marketplace/listing/'.($i+1)) }}" class="btn btn-gold btn-sm">View</a>
              </div>
            </div>
          </div>
          @endforeach
        </div>

        <!-- Pagination -->
        <div style="display:flex; justify-content:center; gap:8px; margin-top:40px; flex-wrap:wrap;">
          @foreach(range(1,8) as $pg)
          <a href="#" style="width:40px; height:40px; display:flex; align-items:center; justify-content:center; background:{{ $pg===1 ? 'var(--gold)' : 'var(--navy3)' }}; border:1px solid {{ $pg===1 ? 'var(--gold)' : 'var(--border-dim)' }}; color:{{ $pg===1 ? 'var(--navy)' : 'var(--muted)' }}; border-radius:8px; font-size:14px; font-weight:{{ $pg===1 ? '700' : '400' }}; text-decoration:none;">{{ $pg }}</a>
          @endforeach
          <a href="#" style="padding:0 16px; height:40px; display:flex; align-items:center; background:var(--navy3); border:1px solid var(--border-dim); color:var(--muted); border-radius:8px; font-size:14px; text-decoration:none;">Next →</a>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
function toggleFilter(btn) {
  const active = btn.getAttribute('data-active') === 'true';
  btn.setAttribute('data-active', !active);
  btn.style.background = active ? 'var(--navy3)' : 'var(--gold)';
  btn.style.color = active ? 'var(--muted)' : 'var(--navy)';
  btn.style.borderColor = active ? 'var(--border-dim)' : 'var(--gold)';
}
function setView(v) { console.log('View:', v); }
</script>
@endpush
@endsection
