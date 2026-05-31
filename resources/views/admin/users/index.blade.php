@extends('layouts.dashboard')
@section('title', 'User Management — Admin')
@section('page-title', 'User Management')
@section('page-subtitle', 'Manage all platform users across roles')

@section('sidebar-nav')
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Overview</div>
  <a href="{{ url('/dashboard/admin') }}" class="dash-nav-item"><span class="dash-nav-icon">🏛️</span> Dashboard</a>
</div>
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Admin Tools</div>
  <a href="{{ url('/admin/kyc') }}" class="dash-nav-item"><span class="dash-nav-icon">✅</span> KYC Queue</a>
  <a href="{{ url('/admin/properties') }}" class="dash-nav-item"><span class="dash-nav-icon">🏠</span> Property Moderation</a>
  <a href="{{ url('/admin/users') }}" class="dash-nav-item active"><span class="dash-nav-icon">👥</span> User Management</a>
</div>
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Other</div>
  <a href="{{ url('/dashboard/messages') }}" class="dash-nav-item"><span class="dash-nav-icon">💬</span> Messages</a>
  <a href="{{ url('/dashboard/settings') }}" class="dash-nav-item"><span class="dash-nav-icon">⚙️</span> Settings</a>
</div>
@endsection

@section('content')

<!-- Filters -->
<div class="card" style="padding:16px 20px; margin-bottom:20px;">
  <form method="GET" action="{{ route('admin.users.index') }}" style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end;">
    <div>
      <label style="display:block; font-size:11px; color:var(--muted); margin-bottom:4px; text-transform:uppercase; letter-spacing:0.5px;">Search</label>
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or email..." style="background:var(--navy3); border:1px solid var(--border-dim); border-radius:6px; padding:7px 12px; color:var(--white); font-size:13px; width:220px; outline:none;">
    </div>
    <div>
      <label style="display:block; font-size:11px; color:var(--muted); margin-bottom:4px; text-transform:uppercase; letter-spacing:0.5px;">Role</label>
      <select name="role" style="background:var(--navy3); border:1px solid var(--border-dim); border-radius:6px; padding:7px 12px; color:var(--white); font-size:13px; outline:none;">
        <option value="">All Roles</option>
        @foreach(['admin','landlord','tenant','broker_licensed','broker_unlicensed','developer','valuer','surveyor','auctioneer','investor','corporate','property_manager','finance'] as $r)
        <option value="{{ $r }}" {{ request('role') === $r ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $r)) }}</option>
        @endforeach
      </select>
    </div>
    <button type="submit" class="btn btn-gold btn-sm">🔍 Filter</button>
    <a href="{{ route('admin.users.index') }}" class="btn btn-ghost btn-sm">Reset</a>
  </form>
</div>

<div class="card" style="padding:0; overflow:hidden;">
  <div style="padding:20px 24px; border-bottom:1px solid var(--border-dim);">
    <div style="font-size:16px; font-weight:600; color:var(--white);">All Users <span style="color:var(--muted); font-size:13px; font-weight:400;">— {{ $users->total() }} total</span></div>
  </div>

  <div style="overflow-x:auto;">
    <table style="width:100%; border-collapse:collapse;">
      <thead>
        <tr style="background:var(--navy3); border-bottom:1px solid var(--border);">
          <th style="padding:12px 16px; text-align:left; font-size:11px; color:var(--muted); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">User</th>
          <th style="padding:12px 16px; text-align:left; font-size:11px; color:var(--muted); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Role</th>
          <th style="padding:12px 16px; text-align:left; font-size:11px; color:var(--muted); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Status</th>
          <th style="padding:12px 16px; text-align:left; font-size:11px; color:var(--muted); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Verified</th>
          <th style="padding:12px 16px; text-align:left; font-size:11px; color:var(--muted); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Joined</th>
          <th style="padding:12px 16px; text-align:left; font-size:11px; color:var(--muted); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($users as $user)
        <tr style="border-bottom:1px solid var(--border-dim);" onmouseover="this.style.background='var(--navy3)'" onmouseout="this.style.background='transparent'">
          <td style="padding:14px 16px;">
            <div style="font-size:13px; font-weight:600; color:var(--white);">{{ $user->name }}</div>
            <div style="font-size:11px; color:var(--muted); margin-top:2px;">{{ $user->email }}</div>
            @if($user->phone)<div style="font-size:11px; color:var(--muted);">{{ $user->phone }}</div>@endif
          </td>
          <td style="padding:14px 16px;">
            <span style="background:var(--navy3); color:var(--text); padding:3px 10px; border-radius:4px; font-size:11px; font-weight:600; border:1px solid var(--border-dim);">{{ strtoupper(str_replace('_', ' ', $user->role)) }}</span>
          </td>
          <td style="padding:14px 16px;">
            @if($user->is_active)
              <span style="background:rgba(0,200,100,0.15); color:var(--green); padding:3px 10px; border-radius:4px; font-size:11px; font-weight:600;">ACTIVE</span>
            @else
              <span style="background:rgba(224,82,82,0.15); color:var(--red); padding:3px 10px; border-radius:4px; font-size:11px; font-weight:600;">INACTIVE</span>
            @endif
          </td>
          <td style="padding:14px 16px;">
            @if($user->is_verified)
              <span style="color:var(--gold); font-size:16px;">✅</span>
            @else
              <span style="color:var(--muted); font-size:13px;">—</span>
            @endif
          </td>
          <td style="padding:14px 16px; font-size:12px; color:var(--muted); font-family:var(--font-mono);">
            {{ $user->created_at?->format('d M Y') }}
          </td>
          <td style="padding:14px 16px;">
            <div style="display:flex; gap:6px;">
              <a href="{{ route('admin.users.show', $user->id) }}" style="background:var(--navy3); color:var(--gold); border:1px solid var(--border-dim); padding:5px 12px; border-radius:5px; font-size:11px; font-weight:600; text-decoration:none;">👁 View</a>
              <form action="{{ route('admin.users.toggleActive', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Toggle active status for {{ $user->name }}?')">
                @csrf
                <button type="submit" style="background:{{ $user->is_active ? 'rgba(224,82,82,0.15)' : 'rgba(0,200,100,0.15)' }}; color:{{ $user->is_active ? 'var(--red)' : 'var(--green)' }}; border:1px solid {{ $user->is_active ? 'var(--red)' : 'var(--green)' }}; cursor:pointer; padding:5px 12px; border-radius:5px; font-size:11px; font-weight:600;">
                  {{ $user->is_active ? '🚫 Deactivate' : '✅ Activate' }}
                </button>
              </form>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div style="padding:16px 24px; border-top:1px solid var(--border-dim);">
    {{ $users->links() }}
  </div>
</div>
@endsection
