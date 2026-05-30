@extends('layouts.dashboard')
@section('title', 'Profile — EstateYard')
@section('page-title', 'My Profile')
@section('page-subtitle', 'Your public EstateYard profile')
@section('sidebar-nav')
<div class="dash-nav-section">
  <a href="{{ url('/dashboard') }}" class="dash-nav-item"><span class="dash-nav-icon">←</span> Back to Dashboard</a>
</div>
@endsection
@section('content')
<div style="max-width:800px;">
  <!-- Profile Header -->
  <div style="background:var(--navy3); border:1px solid var(--border-dim); border-radius:var(--radius); padding:32px; margin-bottom:24px;">
    <div style="display:flex; gap:24px; align-items:flex-start; flex-wrap:wrap;">
      <div style="position:relative;">
        <div style="width:100px; height:100px; border-radius:50%; background:var(--gold-dim); border:3px solid var(--gold); display:flex; align-items:center; justify-content:center; font-size:42px;">👤</div>
        <button style="position:absolute; bottom:0; right:0; width:30px; height:30px; border-radius:50%; background:var(--gold); border:none; cursor:pointer; font-size:12px;">📷</button>
      </div>
      <div style="flex:1;">
        <div style="font-family:var(--font-serif); font-size:28px; font-weight:700; color:var(--white); margin-bottom:4px;">John Kamau</div>
        <div class="verified-badge" style="margin-bottom:10px;">✓ VERIFIED LANDLORD</div>
        <div style="font-size:14px; color:var(--muted);">📍 Nairobi, Kenya · Member since Jan 2023</div>
        <div style="display:flex; gap:16px; margin-top:12px; flex-wrap:wrap;">
          <div style="text-align:center;"><div style="font-family:var(--font-serif); font-size:22px; font-weight:700; color:var(--gold);">12</div><div style="font-size:11px; font-family:var(--font-mono); color:var(--muted);">PROPERTIES</div></div>
          <div style="text-align:center; border-left:1px solid var(--border-dim); border-right:1px solid var(--border-dim); padding:0 16px;"><div style="font-family:var(--font-serif); font-size:22px; font-weight:700; color:var(--green);">★ 4.9</div><div style="font-size:11px; font-family:var(--font-mono); color:var(--muted);">RATING</div></div>
          <div style="text-align:center;"><div style="font-family:var(--font-serif); font-size:22px; font-weight:700; color:var(--blue);">28</div><div style="font-size:11px; font-family:var(--font-mono); color:var(--muted);">DEALS</div></div>
        </div>
      </div>
      <button class="btn btn-outline btn-sm">Edit Profile</button>
    </div>
  </div>
  <div class="form-card"><div class="form-card-title">About Me</div><textarea class="form-control" rows="3">Experienced landlord with a diversified portfolio across Nairobi. I believe in transparent, fair, and professional property management. All my properties are fully verified on EstateYard.</textarea><button onclick="showToast('Bio updated!', 'green')" class="btn btn-gold" style="margin-top:16px;">Save Bio</button></div>
</div>
@endsection
