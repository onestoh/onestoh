@extends('layouts.dashboard')
@section('title', 'Settings — EstateYard')
@section('page-title', 'Account Settings')
@section('page-subtitle', 'Manage your account preferences and security')
@section('sidebar-nav')
<div class="dash-nav-section">
  <a href="{{ url('/dashboard') }}" class="dash-nav-item"><span class="dash-nav-icon">←</span> Back to Dashboard</a>
  <a href="{{ url('/dashboard/settings') }}" class="dash-nav-item active"><span class="dash-nav-icon">⚙️</span> General Settings</a>
  <a href="{{ url('/dashboard/profile') }}" class="dash-nav-item"><span class="dash-nav-icon">👤</span> Profile</a>
  <a href="{{ url('/dashboard/verification') }}" class="dash-nav-item"><span class="dash-nav-icon">✅</span> Verification</a>
</div>
@endsection
@section('content')
<div style="max-width:700px;">
  <div class="tabs" style="margin-bottom:28px;">
    <a href="#" class="tab-item active">General</a>
    <a href="#" class="tab-item">Security</a>
    <a href="#" class="tab-item">Notifications</a>
    <a href="#" class="tab-item">Payments</a>
    <a href="#" class="tab-item">API Keys</a>
  </div>

  <div class="form-card" style="margin-bottom:20px;">
    <div class="form-card-title">Personal Information</div>
    <div class="form-row"><div class="form-group"><label class="form-label">First Name</label><input type="text" class="form-control" value="John"></div><div class="form-group"><label class="form-label">Last Name</label><input type="text" class="form-control" value="Kamau"></div></div>
    <div class="form-group"><label class="form-label">Email Address</label><input type="email" class="form-control" value="john.kamau@example.com"></div>
    <div class="form-group"><label class="form-label">Phone Number</label><input type="tel" class="form-control" value="+254 712 345 678"></div>
    <div class="form-group"><label class="form-label">Location</label><input type="text" class="form-control" value="Nairobi, Kenya"></div>
    <button onclick="showToast('Profile updated!', 'green')" class="btn btn-gold">Save Changes</button>
  </div>

  <div class="form-card" style="margin-bottom:20px;">
    <div class="form-card-title">Payment Methods</div>
    <div style="display:flex; flex-direction:column; gap:10px; margin-bottom:16px;">
      <div style="background:var(--surface); border:1px solid var(--border-dim); border-radius:var(--radius-sm); padding:14px 16px; display:flex; align-items:center; gap:12px;"><span style="font-size:20px;">💳</span><div><div style="font-size:13px; font-weight:600; color:var(--white);">M-Pesa</div><div style="font-size:12px; color:var(--muted);">+254 712 XXX XXX · Default</div></div><span class="verified-badge" style="margin-left:auto;">DEFAULT</span></div>
      <div style="background:var(--surface); border:1px solid var(--border-dim); border-radius:var(--radius-sm); padding:14px 16px; display:flex; align-items:center; gap:12px;"><span style="font-size:20px;">🏦</span><div><div style="font-size:13px; font-weight:600; color:var(--white);">Equity Bank</div><div style="font-size:12px; color:var(--muted);">Account ending **** 4821</div></div></div>
    </div>
    <button class="btn btn-outline btn-sm">+ Add Payment Method</button>
  </div>

  <div class="form-card">
    <div class="form-card-title">Notification Preferences</div>
    @foreach(['Rent payment reminders','New messages','Deal status updates','Referral commission earned','Listing enquiries','Auction bid notifications','System announcements'] as $notif)
    <div style="display:flex; justify-content:space-between; align-items:center; padding:12px 0; border-bottom:1px solid var(--border-dim);">
      <span style="font-size:13px; color:var(--text);">{{ $notif }}</span>
      <label style="position:relative; display:inline-block; width:44px; height:24px; cursor:pointer;">
        <input type="checkbox" checked style="opacity:0; width:0; height:0;">
        <span style="position:absolute; inset:0; background:var(--gold); border-radius:12px; transition:.3s;"></span>
      </label>
    </div>
    @endforeach
    <button onclick="showToast('Notification preferences saved!', 'green')" class="btn btn-gold" style="margin-top:16px;">Save Preferences</button>
  </div>
</div>
@endsection
