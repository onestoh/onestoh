@extends('layouts.dashboard')

@section('title', 'Messages')
@section('page-title', 'Messages')

@push('styles')
<style>
.conv-card { display: flex; align-items: center; gap: 1rem; padding: 1rem; border-bottom: 1px solid var(--border); text-decoration: none; color: var(--text); transition: background 0.2s; }
.conv-card:hover { background: rgba(255,255,255,.03); color: var(--text); }
.conv-avatar { width: 46px; height: 46px; border-radius: 50%; background: rgba(232,146,42,.15); border: 1px solid rgba(232,146,42,.3); display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--amber); font-size: 1.1rem; flex-shrink: 0; }
.conv-preview { color: var(--muted); font-size: .8rem; margin-top: .15rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 340px; }
.conv-time { color: var(--muted); font-size: .75rem; white-space: nowrap; }
.unread-dot { width: 8px; height: 8px; background: var(--amber); border-radius: 50%; flex-shrink: 0; }
</style>
@endpush

@section('content')
<div class="card" style="border-radius:12px;overflow:hidden">
    <div class="card-header d-flex align-items-center justify-content-between" style="background:var(--black);border-bottom:1px solid var(--border);padding:1rem 1.25rem">
        <h6 class="mb-0 fw-bold"><i class="fas fa-comments me-2 text-amber"></i>Conversations</h6>
    </div>

    @if($conversations->isEmpty())
    <div class="text-center py-5" style="color:var(--muted)">
        <i class="fas fa-comment-slash fa-3x mb-3" style="opacity:.4"></i>
        <p class="mb-0">No messages yet.</p>
        <p class="mt-1" style="font-size:.85rem">Messages from bookings will appear here.</p>
    </div>
    @else
    @foreach($conversations as $otherId => $lastMsg)
    @php $otherUser = $users[$otherId] ?? null; @endphp
    @if($otherUser)
    <a href="{{ route('messages.thread', $lastMsg->booking_id ?? 0) }}" class="conv-card">
        <div class="conv-avatar">{{ strtoupper(substr($otherUser->name, 0, 2)) }}</div>
        <div class="flex-grow-1 min-width-0">
            <div class="d-flex align-items-center gap-2">
                <span style="font-weight:600;font-size:.9rem">{{ $otherUser->name }}</span>
                <span style="font-size:.72rem;color:var(--muted);background:rgba(112,136,168,.1);padding:1px 7px;border-radius:10px">{{ ucfirst(str_replace('_',' ',$otherUser->role)) }}</span>
            </div>
            <div class="conv-preview">{{ $lastMsg->message }}</div>
        </div>
        <div class="d-flex flex-column align-items-end gap-1">
            <span class="conv-time">{{ $lastMsg->created_at->diffForHumans() }}</span>
            @if(!$lastMsg->is_read && $lastMsg->to_user_id === auth()->id())
            <span class="unread-dot"></span>
            @endif
        </div>
    </a>
    @endif
    @endforeach
    @endif
</div>
@endsection
