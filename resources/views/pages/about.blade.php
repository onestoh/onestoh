@extends('layouts.app')

@section('title', 'About EstateYard')
@section('meta_description', 'EstateYard — The all-in-one Real Estate ERP Platform built for Africa and beyond.')

@section('content')
<section style="padding:120px 0 80px; background:var(--navy);">
  <div class="container">

    <div style="max-width:800px; margin:0 auto 80px; text-align:center;">
      <span class="section-tag">OUR STORY</span>
      <h1 class="section-title">Built for Africa's Real Estate Future</h1>
      <p style="color:var(--muted); font-size:16px; line-height:1.8;">EstateYard was born from a simple observation: Africa's real estate ecosystem was fragmented, opaque, and inaccessible. Buyers couldn't find verified listings. Landlords struggled to collect rent. Brokers lacked tools to scale. We built EstateYard to change all of that — one transaction at a time.</p>
    </div>

    <div class="grid-4" style="gap:24px; margin-bottom:80px;">
      @foreach([['24K+','Active Listings'],['$2.4B','Deals Facilitated'],['13','Stakeholder Roles'],['98%','Satisfaction Rate']] as $stat)
      <div class="kpi-card" style="text-align:center;">
        <div style="font-size:36px; font-weight:700; color:var(--gold); font-family:var(--font-serif);">{{ $stat[0] }}</div>
        <div style="color:var(--muted); font-size:13px; margin-top:4px;">{{ $stat[1] }}</div>
      </div>
      @endforeach
    </div>

    <div class="grid-2" style="gap:40px; align-items:center; margin-bottom:80px;">
      <div>
        <h2 style="color:var(--white); font-family:var(--font-serif); font-size:32px; margin-bottom:16px;">Our Mission</h2>
        <p style="color:var(--muted); line-height:1.8; margin-bottom:16px;">To democratize access to real estate in Africa by providing a unified platform that connects every stakeholder — from first-time buyers to institutional investors — with the tools, trust, and transparency they need to transact confidently.</p>
        <p style="color:var(--muted); line-height:1.8;">We integrate marketplace, escrow, rent automation, auctions, hospitality management, CRM, and referral systems into a single platform designed for the African context — including M-Pesa native payments.</p>
      </div>
      <div style="background:var(--navy2); border:1px solid var(--border); border-radius:var(--radius); padding:40px; text-align:center;">
        <div style="font-size:80px; margin-bottom:16px;">🏙️</div>
        <div style="font-size:28px; font-family:var(--font-serif); color:var(--gold);">Estate<span style="color:var(--white);">Yard</span></div>
        <div style="color:var(--muted); font-size:14px; margin-top:8px;">Nairobi, Kenya · Est. 2024</div>
      </div>
    </div>

    <div style="text-align:center; background:var(--navy2); border:1px solid var(--border); border-radius:var(--radius); padding:60px;">
      <h2 style="color:var(--white); font-family:var(--font-serif); font-size:32px; margin-bottom:16px;">Join the EstateYard Ecosystem</h2>
      <p style="color:var(--muted); margin-bottom:32px; max-width:500px; margin-left:auto; margin-right:auto;">Whether you're buying, selling, renting, investing, or building — there's a role for you on EstateYard.</p>
      <div style="display:flex; gap:16px; justify-content:center; flex-wrap:wrap;">
        <a href="{{ url('/register') }}" class="btn btn-gold">Get Started Free</a>
        <a href="{{ url('/contact') }}" class="btn btn-outline">Contact Us</a>
      </div>
    </div>

  </div>
</section>
@endsection
