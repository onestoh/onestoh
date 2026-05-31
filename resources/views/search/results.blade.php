@extends('layouts.app')
@section('title', 'Search Results — EstateYard')

@section('content')
<section style="background:var(--navy1); padding:40px 0; border-bottom:1px solid var(--border-dim);">
  <div class="container">
    <div style="max-width:640px; margin:0 auto;">
      <h1 style="font-size:24px; font-weight:700; color:var(--white); margin-bottom:16px;">
        Search Properties
        @if($query)<span style="color:var(--muted); font-size:16px; font-weight:400;"> — "{{ $query }}"</span>@endif
      </h1>
      <form method="GET" action="{{ route('search') }}" style="display:flex; gap:8px;">
        <input type="text" name="q" value="{{ $query }}" placeholder="Search by title, location, county..." id="searchInput"
          style="flex:1; background:var(--navy2); border:1px solid var(--border); border-radius:8px; padding:12px 16px; color:var(--white); font-size:14px; outline:none;">
        <button type="submit" class="btn btn-gold">Search</button>
      </form>
      <div id="suggestions" style="background:var(--navy2); border:1px solid var(--border); border-radius:8px; margin-top:4px; display:none; position:absolute; z-index:200; width:calc(100% - 120px);"></div>
    </div>
  </div>
</section>

<section style="padding:40px 0; min-height:60vh;">
  <div class="container">
    <div style="display:grid; grid-template-columns:240px 1fr; gap:32px; align-items:start;">

      <!-- FILTER SIDEBAR -->
      <aside>
        <form method="GET" action="{{ route('search') }}">
          <input type="hidden" name="q" value="{{ $query }}">
          <div class="card" style="padding:20px;">
            <div style="font-size:13px; font-weight:700; color:var(--white); margin-bottom:16px; text-transform:uppercase; letter-spacing:0.5px;">Filters</div>

            <div style="margin-bottom:16px;">
              <label style="font-size:11px; color:var(--muted); display:block; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.5px;">County</label>
              <select name="county" style="width:100%; background:var(--navy3); border:1px solid var(--border-dim); border-radius:6px; padding:7px 10px; color:var(--white); font-size:13px; outline:none;">
                <option value="">All Counties</option>
                @foreach($counties as $c)
                <option value="{{ $c }}" {{ request('county') === $c ? 'selected' : '' }}>{{ $c }}</option>
                @endforeach
              </select>
            </div>

            <div style="margin-bottom:16px;">
              <label style="font-size:11px; color:var(--muted); display:block; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.5px;">Listing Type</label>
              <select name="listing_type" style="width:100%; background:var(--navy3); border:1px solid var(--border-dim); border-radius:6px; padding:7px 10px; color:var(--white); font-size:13px; outline:none;">
                <option value="">All</option>
                <option value="sale" {{ request('listing_type') === 'sale' ? 'selected' : '' }}>For Sale</option>
                <option value="rent" {{ request('listing_type') === 'rent' ? 'selected' : '' }}>For Rent</option>
                <option value="auction" {{ request('listing_type') === 'auction' ? 'selected' : '' }}>Auction</option>
              </select>
            </div>

            <div style="margin-bottom:16px;">
              <label style="font-size:11px; color:var(--muted); display:block; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.5px;">Min Price (KES)</label>
              <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="0"
                style="width:100%; background:var(--navy3); border:1px solid var(--border-dim); border-radius:6px; padding:7px 10px; color:var(--white); font-size:13px; outline:none; box-sizing:border-box;">
            </div>

            <div style="margin-bottom:16px;">
              <label style="font-size:11px; color:var(--muted); display:block; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.5px;">Max Price (KES)</label>
              <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="No limit"
                style="width:100%; background:var(--navy3); border:1px solid var(--border-dim); border-radius:6px; padding:7px 10px; color:var(--white); font-size:13px; outline:none; box-sizing:border-box;">
            </div>

            <div style="margin-bottom:20px;">
              <label style="font-size:11px; color:var(--muted); display:block; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.5px;">Bedrooms</label>
              <select name="bedrooms" style="width:100%; background:var(--navy3); border:1px solid var(--border-dim); border-radius:6px; padding:7px 10px; color:var(--white); font-size:13px; outline:none;">
                <option value="">Any</option>
                @foreach([1,2,3,4,5] as $b)
                <option value="{{ $b }}" {{ request('bedrooms') == $b ? 'selected' : '' }}>{{ $b }}+ Beds</option>
                @endforeach
              </select>
            </div>

            <button type="submit" class="btn btn-gold" style="width:100%;">Apply Filters</button>
            <a href="{{ route('search') }}" style="display:block; text-align:center; margin-top:8px; font-size:12px; color:var(--muted);">Clear all</a>
          </div>
        </form>
      </aside>

      <!-- RESULTS -->
      <div>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
          <div style="font-size:14px; color:var(--muted);">
            {{ $properties->total() }} propert{{ $properties->total() === 1 ? 'y' : 'ies' }} found
            @if($query) for "<strong style="color:var(--white);">{{ $query }}</strong>"@endif
          </div>
        </div>

        @if($properties->isEmpty())
        <div class="card" style="padding:48px; text-align:center;">
          <div style="font-size:40px; margin-bottom:12px;">🔍</div>
          <div style="font-size:18px; font-weight:600; color:var(--white); margin-bottom:8px;">No properties found</div>
          <div style="color:var(--muted); font-size:14px;">Try adjusting your search terms or removing some filters.</div>
          <a href="{{ route('marketplace') }}" class="btn btn-gold" style="margin-top:20px; display:inline-block;">Browse All Listings</a>
        </div>
        @else

        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:20px;">
          @foreach($properties as $property)
          <a href="{{ route('listing.show', $property->id) }}" style="text-decoration:none;" class="card property-card" style="overflow:hidden; transition:transform .2s;">
            <div style="height:180px; background:var(--navy3); border-radius:8px 8px 0 0; overflow:hidden; margin:-1px -1px 0;">
              @if($property->images && count($property->images) > 0)
              <img src="{{ asset('storage/' . $property->images[0]) }}" alt="{{ $property->title }}" style="width:100%; height:100%; object-fit:cover;">
              @else
              <div style="height:100%; display:flex; align-items:center; justify-content:center; font-size:40px; color:var(--border);">🏠</div>
              @endif
            </div>
            <div style="padding:16px;">
              @if($property->is_featured)
              <span style="background:var(--gold-dim); color:var(--gold); font-size:10px; padding:2px 8px; border-radius:3px; font-weight:600; margin-bottom:8px; display:inline-block;">⭐ FEATURED</span>
              @endif
              <div style="font-size:14px; font-weight:600; color:var(--white); margin-bottom:4px;">{{ $property->title }}</div>
              <div style="font-size:12px; color:var(--muted); margin-bottom:8px;">📍 {{ $property->location }}, {{ $property->county }}</div>
              <div style="font-size:18px; font-weight:700; color:var(--gold);">KES {{ number_format($property->price, 0) }}</div>
              <div style="display:flex; gap:12px; margin-top:8px; font-size:11px; color:var(--muted);">
                @if($property->bedrooms)<span>🛏 {{ $property->bedrooms }} beds</span>@endif
                @if($property->bathrooms)<span>🚿 {{ $property->bathrooms }} baths</span>@endif
                <span style="margin-left:auto; background:var(--navy3); padding:2px 8px; border-radius:3px;">{{ ucfirst($property->listing_type) }}</span>
              </div>
            </div>
          </a>
          @endforeach
        </div>

        <div style="margin-top:32px;">
          {{ $properties->links() }}
        </div>
        @endif
      </div>
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script>
const input = document.getElementById('searchInput');
const sugBox = document.getElementById('suggestions');

if (input) {
  input.addEventListener('input', async function() {
    const q = this.value.trim();
    if (q.length < 2) { sugBox.style.display = 'none'; return; }
    const res = await fetch('/api/search/suggestions?q=' + encodeURIComponent(q));
    const data = await res.json();
    if (!data.length) { sugBox.style.display = 'none'; return; }
    sugBox.innerHTML = data.map(s => `
      <div onclick="document.getElementById('searchInput').value='${s.label}'; this.closest('form').submit();"
        style="padding:10px 16px; cursor:pointer; border-bottom:1px solid var(--border-dim);"
        onmouseover="this.style.background='var(--navy3)'" onmouseout="this.style.background='transparent'">
        <div style="font-size:13px; color:var(--white);">${s.label}</div>
        <div style="font-size:11px; color:var(--muted);">${s.sub}</div>
      </div>
    `).join('');
    sugBox.style.display = 'block';
  });
  document.addEventListener('click', e => { if (!input.contains(e.target)) sugBox.style.display = 'none'; });
}
</script>
@endpush
