@extends('layouts.app')

@section('title', 'Compare Properties — EstateYard')
@section('meta_description', 'Compare up to 3 properties side by side on EstateYard.')

@section('content')
<section style="padding:100px 0 80px; background:var(--navy); min-height:100vh;">
  <div class="container">

    <div style="text-align:center; margin-bottom:48px;">
      <span class="section-tag">COMPARE</span>
      <h1 class="section-title">Property Comparison</h1>
      <p style="color:var(--muted); max-width:500px; margin:0 auto;">Evaluate up to 3 properties side by side to make the best decision.</p>
    </div>

    @if(count($properties) < 1)
    <div style="text-align:center; padding:80px 20px;">
      <div style="font-size:60px; margin-bottom:16px;">🏡</div>
      <h3 style="color:var(--white); font-family:var(--font-serif); margin-bottom:12px;">No Properties Selected</h3>
      <p style="color:var(--muted); margin-bottom:24px;">Browse the marketplace and click "Compare" on properties you want to evaluate.</p>
      <a href="{{ url('/marketplace') }}" class="btn btn-gold">Browse Properties</a>
    </div>
    @else
    <div style="overflow-x:auto;">
      <table style="width:100%; border-collapse:collapse; min-width:600px;">
        {{-- Header row with property cards --}}
        <thead>
          <tr>
            <td style="padding:16px; color:var(--muted); font-size:13px; font-weight:600; width:180px; vertical-align:bottom;">PROPERTY</td>
            @foreach($properties as $prop)
            <td style="padding:16px; vertical-align:top;">
              <div style="background:var(--navy2); border:1px solid var(--border); border-radius:var(--radius); overflow:hidden;">
                <div style="height:160px; background:linear-gradient(135deg,rgba(212,168,67,0.1),var(--navy3)); display:flex; align-items:center; justify-content:center; font-size:60px;">
                  {{ $prop->type === 'land' ? '🌿' : ($prop->listing_type === 'hotel' ? '🏨' : '🏠') }}
                </div>
                <div style="padding:16px;">
                  <h3 style="color:var(--white); font-family:var(--font-serif); font-size:16px; margin-bottom:6px;">{{ Str::limit($prop->title, 40) }}</h3>
                  <div style="color:var(--muted); font-size:12px; margin-bottom:10px;">📍 {{ $prop->location }}</div>
                  <div style="font-size:20px; font-weight:700; color:var(--gold); font-family:var(--font-serif);">
                    KES {{ number_format($prop->price) }}
                    @if($prop->listing_type === 'rent')<span style="font-size:12px; color:var(--muted);">/mo</span>@endif
                  </div>
                  <a href="{{ url('/marketplace/'.$prop->id) }}" class="btn btn-outline btn-sm" style="width:100%; justify-content:center; margin-top:12px;">View Listing</a>
                </div>
              </div>
            </td>
            @endforeach
            @for($i = count($properties); $i < 3; $i++)
            <td style="padding:16px; vertical-align:top;">
              <div style="background:var(--navy2); border:2px dashed var(--border); border-radius:var(--radius); height:280px; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:12px;">
                <div style="font-size:40px; opacity:0.3;">➕</div>
                <p style="color:var(--muted); font-size:13px; text-align:center;">Add another property from the marketplace</p>
                <a href="{{ url('/marketplace') }}" class="btn btn-outline btn-sm">Browse</a>
              </div>
            </td>
            @endfor
          </tr>
        </thead>
        <tbody>
          @php
          $rows = [
            ['label'=>'Type','key'=>'type','format'=>fn($v)=>ucfirst($v)],
            ['label'=>'Listing','key'=>'listing_type','format'=>fn($v)=>ucfirst($v)],
            ['label'=>'Bedrooms','key'=>'bedrooms','format'=>fn($v)=>$v??'—'],
            ['label'=>'Bathrooms','key'=>'bathrooms','format'=>fn($v)=>$v??'—'],
            ['label'=>'Size (sqft)','key'=>'size_sqft','format'=>fn($v)=>$v?number_format($v):'—'],
            ['label'=>'County','key'=>'county','format'=>fn($v)=>$v??'—'],
            ['label'=>'Verified','key'=>'is_verified','format'=>fn($v)=>$v?'✅ Yes':'❌ No'],
            ['label'=>'Featured','key'=>'is_featured','format'=>fn($v)=>$v?'⭐ Yes':'No'],
            ['label'=>'Year Built','key'=>'year_built','format'=>fn($v)=>$v??'—'],
          ];
          @endphp
          @foreach($rows as $idx => $row)
          <tr style="background:{{ $idx % 2 === 0 ? 'var(--navy2)' : 'var(--navy3)' }};">
            <td style="padding:14px 16px; color:var(--muted); font-size:13px; font-weight:600; border-right:1px solid var(--border);">{{ $row['label'] }}</td>
            @foreach($properties as $prop)
            <td style="padding:14px 16px; color:var(--white); font-size:14px; border-right:1px solid var(--border);">
              {{ $row['format']($prop->{$row['key']}) }}
            </td>
            @endforeach
            @for($i = count($properties); $i < 3; $i++)
            <td style="padding:14px 16px; color:var(--border); font-size:14px;">—</td>
            @endfor
          </tr>
          @endforeach
          {{-- Amenities --}}
          <tr style="background:var(--navy2);">
            <td style="padding:14px 16px; color:var(--muted); font-size:13px; font-weight:600; border-right:1px solid var(--border);">Amenities</td>
            @foreach($properties as $prop)
            <td style="padding:14px 16px; color:var(--white); font-size:13px; border-right:1px solid var(--border);">
              @if($prop->amenities)
                @foreach(array_slice(json_decode($prop->amenities, true) ?? [], 0, 5) as $a)
                <span style="display:inline-block; background:var(--gold-dim); color:var(--gold); padding:2px 8px; border-radius:20px; font-size:11px; margin:2px;">{{ $a }}</span>
                @endforeach
              @else —
              @endif
            </td>
            @endforeach
            @for($i = count($properties); $i < 3; $i++)
            <td style="padding:14px 16px; color:var(--border);">—</td>
            @endfor
          </tr>
          {{-- Price action --}}
          <tr style="background:var(--navy3);">
            <td style="padding:16px; color:var(--muted); font-size:13px; font-weight:600; border-right:1px solid var(--border);"></td>
            @foreach($properties as $prop)
            <td style="padding:16px; border-right:1px solid var(--border);">
              <a href="{{ url('/marketplace/'.$prop->id) }}" class="btn btn-gold btn-sm" style="width:100%; justify-content:center;">Select This Property</a>
            </td>
            @endforeach
            @for($i = count($properties); $i < 3; $i++)
            <td style="padding:16px;"></td>
            @endfor
          </tr>
        </tbody>
      </table>
    </div>

    <div style="text-align:center; margin-top:40px;">
      <a href="{{ url('/marketplace') }}" style="color:var(--muted); font-size:14px;">← Back to Marketplace</a>
    </div>
    @endif

  </div>
</section>
@endsection
