@extends('layouts.app')

@section('title', 'Property Finance')
@section('meta_description', 'Access mortgage financing, construction loans, and property investment funds through EstateYard.')

@section('content')
<section style="padding:120px 0 80px; background:var(--navy);">
  <div class="container">
    <div style="text-align:center; margin-bottom:60px;">
      <span class="section-tag">FINANCING</span>
      <h1 class="section-title">Property Finance Solutions</h1>
      <p style="color:var(--muted); font-size:16px; max-width:600px; margin:0 auto;">Access mortgage financing, construction loans, and property investment funds — all integrated into the EstateYard ecosystem.</p>
    </div>

    <div class="grid-3" style="gap:24px; margin-bottom:60px;">
      @foreach([
        ['🏠','Mortgage Loans','Buy your dream home with competitive mortgage rates starting from 9.5% p.a. through our banking partners.','Apply Now'],
        ['🏗️','Construction Finance','Finance your development project from groundbreaking to handover with flexible draw schedules.','Get Quote'],
        ['📈','Investment Loans','Leverage your existing portfolio to acquire more properties with LTV up to 70%.','Learn More'],
      ] as $f)
      <div class="feature-card" style="text-align:center;">
        <div style="font-size:48px; margin-bottom:16px;">{{ $f[0] }}</div>
        <h3 style="color:var(--white); font-family:var(--font-serif); font-size:22px; margin-bottom:12px;">{{ $f[1] }}</h3>
        <p style="color:var(--muted); font-size:14px; line-height:1.7; margin-bottom:20px;">{{ $f[2] }}</p>
        <a href="{{ url('/register') }}" class="btn btn-outline btn-sm">{{ $f[3] }}</a>
      </div>
      @endforeach
    </div>

    <div class="form-card" style="max-width:600px; margin:0 auto;">
      <h2 style="color:var(--gold); font-family:var(--font-serif); font-size:24px; margin-bottom:24px;">Quick Eligibility Check</h2>
      <form data-validate>
        <div class="grid-2" style="gap:16px; margin-bottom:16px;">
          <div>
            <label class="form-label">Monthly Income (KES)</label>
            <input type="number" class="form-input" placeholder="e.g. 150,000" required>
          </div>
          <div>
            <label class="form-label">Loan Amount Needed (KES)</label>
            <input type="number" class="form-input" placeholder="e.g. 5,000,000" required>
          </div>
        </div>
        <div style="margin-bottom:16px;">
          <label class="form-label">Loan Type</label>
          <select class="form-input">
            <option>Mortgage</option>
            <option>Construction</option>
            <option>Investment</option>
          </select>
        </div>
        <button type="submit" class="btn btn-gold" style="width:100%;">Check Eligibility</button>
      </form>
    </div>
  </div>
</section>
@endsection
