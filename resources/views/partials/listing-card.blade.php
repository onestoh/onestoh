@php
  $photo = $listing->photos->first();
  $photoUrl = $photo ? asset('storage/' . $photo->file_path) : null;
@endphp
<div class="listing-card h-100">
  @if($photoUrl)
    <img src="{{ $photoUrl }}" alt="{{ $listing->title }}" loading="lazy">
  @else
    <div style="background:var(--surface2);height:200px;display:flex;align-items:center;justify-content:center;color:var(--muted);font-size:40px;">
      {{ $listing->category->icon ?? '🚗' }}
    </div>
  @endif

  <div class="card-body">
    <div class="d-flex justify-content-between align-items-start mb-1">
      <span class="badge-amber" style="font-size:10px;">{{ $listing->category->name ?? $listing->asset_type }}</span>
      @if($listing->listing_mode !== 'rental')
        <span class="badge-green" style="font-size:10px;">FOR SALE</span>
      @endif
    </div>

    <h6 style="color:#fff;font-weight:600;margin:8px 0 4px;font-size:14px;">{{ Str::limit($listing->title, 40) }}</h6>

    <div style="font-size:12px;color:var(--muted);margin-bottom:8px;">
      <i class="fas fa-map-marker-alt me-1" style="color:var(--amber)"></i>
      {{ $listing->city ?? $listing->county ?? 'Kenya' }}
    </div>

    @if($listing->average_rating > 0)
      <div style="font-size:12px;color:var(--amber);margin-bottom:8px;">
        @for($i=1;$i<=5;$i++)
          <i class="fas fa-star{{ $i <= round($listing->average_rating) ? '' : '-half-alt' }}" style="font-size:10px;"></i>
        @endfor
        <span style="color:var(--muted);margin-left:4px;">({{ $listing->rating_count }})</span>
      </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mt-2">
      <div>
        @if($listing->daily_rate)
          <div class="price">KES {{ number_format($listing->daily_rate, 0) }}</div>
          <div style="font-size:10px;color:var(--muted);font-family:'JetBrains Mono',monospace;">per day</div>
        @elseif($listing->hourly_rate)
          <div class="price">KES {{ number_format($listing->hourly_rate, 0) }}</div>
          <div style="font-size:10px;color:var(--muted);font-family:'JetBrains Mono',monospace;">per hour</div>
        @elseif($listing->sale_price)
          <div class="price">KES {{ number_format($listing->sale_price, 0) }}</div>
          <div style="font-size:10px;color:var(--muted);font-family:'JetBrains Mono',monospace;">sale price</div>
        @endif
      </div>
      <a href="{{ route('listings.show', $listing->slug) }}" class="btn btn-amber btn-sm" style="font-size:12px;padding:5px 12px;">
        Book Now
      </a>
    </div>
  </div>
</div>
