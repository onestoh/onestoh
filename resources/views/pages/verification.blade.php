@extends('layouts.app')

@section('title', 'Get Verified')
@section('meta_description', 'Get your EstateYard verification badge and build trust with buyers, tenants and partners.')

@section('content')
<section style="padding:120px 0 80px; background:var(--navy);">
  <div class="container">
    <div style="text-align:center; margin-bottom:60px;">
      <span class="section-tag">TRUST & SAFETY</span>
      <h1 class="section-title">Get EstateYard Verified</h1>
      <p style="color:var(--muted); font-size:16px; max-width:600px; margin:0 auto;">A verified badge signals professionalism, builds buyer confidence, and unlocks premium platform features.</p>
    </div>

    <div class="grid-3" style="gap:24px; margin-bottom:60px;">
      @foreach([
        ['⭐','Basic','Free','ID verification + phone number confirmation','Identity Check,Phone Verified,Basic Listing Badge'],
        ['✅','Professional','KES 4,500/yr','Full KYC + business registration + 3 reference checks','All Basic Features,Business Verified Badge,Priority Search Ranking,Escrow Access'],
        ['👑','Elite','KES 12,000/yr','Everything in Professional + site visit + premium placement','All Pro Features,Elite Gold Badge,Featured Listings,Dedicated Account Manager,API Access'],
      ] as $p)
      <div class="pricing-card {{ $loop->index === 2 ? 'popular' : '' }}">
        @if($loop->index === 2)<div class="pricing-popular-tag">MOST POPULAR</div>@endif
        <div style="font-size:36px; margin-bottom:8px;">{{ $p[0] }}</div>
        <div class="pricing-tier">{{ $p[1] }}</div>
        <div class="pricing-price">{{ $p[2] }}</div>
        <ul class="pricing-features">
          @foreach(explode(',', $p[4]) as $feat)
          <li>✓ {{ $feat }}</li>
          @endforeach
        </ul>
        <a href="{{ url('/register') }}" class="btn {{ $loop->index === 2 ? 'btn-gold' : 'btn-outline' }}" style="width:100%; justify-content:center;">Get {{ $p[1] }}</a>
      </div>
      @endforeach
    </div>
  </div>
</section>
@endsection
