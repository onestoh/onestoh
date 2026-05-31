@extends('layouts.dashboard')
@section('title', 'Property Moderation — Admin')
@section('page-title', 'Property Moderation')
@section('page-subtitle', 'Approve, suspend, or feature property listings')

@section('sidebar-nav')
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Overview</div>
  <a href="{{ url('/dashboard/admin') }}" class="dash-nav-item"><span class="dash-nav-icon">🏛️</span> Dashboard</a>
</div>
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Admin Tools</div>
  <a href="{{ url('/admin/kyc') }}" class="dash-nav-item"><span class="dash-nav-icon">✅</span> KYC Queue</a>
  <a href="{{ url('/admin/properties') }}" class="dash-nav-item active"><span class="dash-nav-icon">🏠</span> Property Moderation</a>
  <a href="{{ url('/admin/users') }}" class="dash-nav-item"><span class="dash-nav-icon">👥</span> User Management</a>
</div>
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Other</div>
  <a href="{{ url('/dashboard/messages') }}" class="dash-nav-item"><span class="dash-nav-icon">💬</span> Messages</a>
  <a href="{{ url('/dashboard/settings') }}" class="dash-nav-item"><span class="dash-nav-icon">⚙️</span> Settings</a>
</div>
@endsection

@section('content')
<div class="card" style="padding:0; overflow:hidden;">
  <div style="padding:20px 24px; border-bottom:1px solid var(--border-dim); display:flex; justify-content:space-between; align-items:center;">
    <div>
      <div style="font-size:16px; font-weight:600; color:var(--white);">Pending & Suspended Properties</div>
      <div style="font-size:12px; color:var(--muted); margin-top:2px;">{{ $properties->total() }} listings require attention</div>
    </div>
  </div>

  @if($properties->isEmpty())
  <div style="padding:48px; text-align:center; color:var(--muted);">
    <div style="font-size:40px; margin-bottom:12px;">🏠</div>
    <div style="font-size:16px;">No properties pending moderation.</div>
  </div>
  @else
  <div style="overflow-x:auto;">
    <table style="width:100%; border-collapse:collapse;">
      <thead>
        <tr style="background:var(--navy3); border-bottom:1px solid var(--border);">
          <th style="padding:12px 16px; text-align:left; font-size:11px; color:var(--muted); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Property</th>
          <th style="padding:12px 16px; text-align:left; font-size:11px; color:var(--muted); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Owner</th>
          <th style="padding:12px 16px; text-align:left; font-size:11px; color:var(--muted); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Price</th>
          <th style="padding:12px 16px; text-align:left; font-size:11px; color:var(--muted); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Status</th>
          <th style="padding:12px 16px; text-align:left; font-size:11px; color:var(--muted); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($properties as $property)
        <tr style="border-bottom:1px solid var(--border-dim);" onmouseover="this.style.background='var(--navy3)'" onmouseout="this.style.background='transparent'">
          <td style="padding:14px 16px;">
            <div style="font-size:13px; font-weight:600; color:var(--white);">{{ $property->title }}</div>
            <div style="font-size:11px; color:var(--muted); margin-top:2px;">{{ $property->location }}, {{ $property->county }}</div>
            <div style="font-size:11px; color:var(--muted);">{{ $property->property_type }} · {{ $property->listing_type }}</div>
          </td>
          <td style="padding:14px 16px;">
            <div style="font-size:13px; color:var(--white);">{{ optional($property->owner)->name }}</div>
            <div style="font-size:11px; color:var(--muted);">{{ optional($property->owner)->email }}</div>
          </td>
          <td style="padding:14px 16px; font-size:13px; font-weight:600; color:var(--gold);">KES {{ number_format($property->price, 0) }}</td>
          <td style="padding:14px 16px;">
            @if($property->status === 'pending')
              <span style="background:rgba(255,165,0,0.15); color:orange; padding:3px 10px; border-radius:4px; font-size:11px; font-weight:600;">PENDING</span>
            @else
              <span style="background:rgba(224,82,82,0.15); color:var(--red); padding:3px 10px; border-radius:4px; font-size:11px; font-weight:600;">SUSPENDED</span>
            @endif
            @if($property->is_featured)
              <span style="background:var(--gold-dim); color:var(--gold); padding:3px 8px; border-radius:4px; font-size:10px; margin-left:4px;">⭐ FEATURED</span>
            @endif
          </td>
          <td style="padding:14px 16px;">
            <div style="display:flex; gap:6px; flex-wrap:wrap;">
              @if($property->status !== 'active')
              <form action="{{ route('admin.properties.approve', $property->id) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" style="background:var(--green); color:white; border:none; cursor:pointer; padding:5px 12px; border-radius:5px; font-size:11px; font-weight:600;">✅ Approve</button>
              </form>
              @endif
              @if($property->status !== 'suspended')
              <form action="{{ route('admin.properties.suspend', $property->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Suspend this listing?')">
                @csrf
                <button type="submit" style="background:var(--red); color:white; border:none; cursor:pointer; padding:5px 12px; border-radius:5px; font-size:11px; font-weight:600;">🚫 Suspend</button>
              </form>
              @endif
              <form action="{{ route('admin.properties.feature', $property->id) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" style="background:var(--gold-dim); color:var(--gold); border:1px solid var(--gold); cursor:pointer; padding:5px 12px; border-radius:5px; font-size:11px; font-weight:600;">⭐ {{ $property->is_featured ? 'Unfeature' : 'Feature' }}</button>
              </form>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div style="padding:16px 24px; border-top:1px solid var(--border-dim);">
    {{ $properties->links() }}
  </div>
  @endif
</div>
@endsection
