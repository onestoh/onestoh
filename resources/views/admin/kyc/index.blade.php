@extends('layouts.dashboard')
@section('title', 'KYC Queue — Admin')
@section('page-title', 'KYC Verification Queue')
@section('page-subtitle', 'Review and action pending identity verifications')

@section('sidebar-nav')
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Overview</div>
  <a href="{{ url('/dashboard/admin') }}" class="dash-nav-item"><span class="dash-nav-icon">🏛️</span> Dashboard</a>
</div>
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Admin Tools</div>
  <a href="{{ url('/admin/kyc') }}" class="dash-nav-item active"><span class="dash-nav-icon">✅</span> KYC Queue</a>
  <a href="{{ url('/admin/properties') }}" class="dash-nav-item"><span class="dash-nav-icon">🏠</span> Property Moderation</a>
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
      <div style="font-size:16px; font-weight:600; color:var(--white);">Pending Verifications</div>
      <div style="font-size:12px; color:var(--muted); margin-top:2px;">{{ $verifications->total() }} total pending</div>
    </div>
  </div>

  @if($verifications->isEmpty())
  <div style="padding:48px; text-align:center; color:var(--muted);">
    <div style="font-size:40px; margin-bottom:12px;">✅</div>
    <div style="font-size:16px;">All verifications are up to date.</div>
  </div>
  @else
  <div style="overflow-x:auto;">
    <table style="width:100%; border-collapse:collapse;">
      <thead>
        <tr style="background:var(--navy3); border-bottom:1px solid var(--border);">
          <th style="padding:12px 16px; text-align:left; font-size:11px; color:var(--muted); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">User</th>
          <th style="padding:12px 16px; text-align:left; font-size:11px; color:var(--muted); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Type</th>
          <th style="padding:12px 16px; text-align:left; font-size:11px; color:var(--muted); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Documents</th>
          <th style="padding:12px 16px; text-align:left; font-size:11px; color:var(--muted); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Submitted</th>
          <th style="padding:12px 16px; text-align:left; font-size:11px; color:var(--muted); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($verifications as $v)
        <tr style="border-bottom:1px solid var(--border-dim);" onmouseover="this.style.background='var(--navy3)'" onmouseout="this.style.background='transparent'">
          <td style="padding:14px 16px;">
            <div style="font-size:13px; font-weight:600; color:var(--white);">{{ optional($v->user)->name ?? 'Unknown' }}</div>
            <div style="font-size:11px; color:var(--muted); margin-top:2px;">{{ optional($v->user)->email }}</div>
            <div style="font-size:11px; color:var(--muted);">{{ optional($v->user)->role }}</div>
          </td>
          <td style="padding:14px 16px;">
            <span style="background:var(--gold-dim); color:var(--gold); padding:3px 10px; border-radius:4px; font-size:11px; font-weight:600;">{{ strtoupper($v->type ?? 'standard') }}</span>
          </td>
          <td style="padding:14px 16px;">
            @if($v->documents && $v->documents->count() > 0)
              @foreach($v->documents as $doc)
              <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" style="display:block; font-size:11px; color:var(--gold); text-decoration:none; margin-bottom:2px;">📄 {{ $doc->document_type ?? 'Document' }}</a>
              @endforeach
            @else
              <span style="color:var(--muted); font-size:11px;">No documents</span>
            @endif
          </td>
          <td style="padding:14px 16px; font-size:12px; color:var(--muted); font-family:var(--font-mono);">
            {{ $v->created_at?->format('d M Y') }}
          </td>
          <td style="padding:14px 16px;">
            <div style="display:flex; gap:8px;">
              <form action="{{ route('admin.kyc.approve', $v->id) }}" method="POST" onsubmit="return confirm('Approve this verification?')">
                @csrf
                <button type="submit" class="btn btn-sm" style="background:var(--green); color:white; border:none; cursor:pointer; padding:6px 14px; border-radius:6px; font-size:12px; font-weight:600;">✅ Approve</button>
              </form>
              <form action="{{ route('admin.kyc.reject', $v->id) }}" method="POST" onsubmit="return confirm('Reject this verification?')">
                @csrf
                <button type="submit" class="btn btn-sm" style="background:var(--red); color:white; border:none; cursor:pointer; padding:6px 14px; border-radius:6px; font-size:12px; font-weight:600;">❌ Reject</button>
              </form>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div style="padding:16px 24px; border-top:1px solid var(--border-dim);">
    {{ $verifications->links() }}
  </div>
  @endif
</div>
@endsection
