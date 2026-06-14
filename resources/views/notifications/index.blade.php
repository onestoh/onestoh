@extends('layouts.dashboard')

@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')
<h4 class="mb-4" style="color:var(--text);font-weight:700">Notifications</h4>

@if($notifications->isEmpty())
<div class="text-center py-5" style="color:var(--muted)">
    <i class="fas fa-bell fa-3x mb-3 d-block" style="opacity:.3"></i>
    <h5 style="color:var(--text)">No notifications</h5>
    <p>You're all caught up!</p>
</div>
@else
<div class="p-3 rounded-3" style="background:var(--surface);border:1px solid var(--border)">
    @foreach($notifications as $notif)
    @php $data = $notif->data; $isRead = !is_null($notif->read_at); @endphp
    <div class="d-flex gap-3 py-3" style="border-bottom:1px solid var(--border); {{ !$isRead ? 'background:rgba(232,146,42,.03)' : '' }}">
        <div style="width:36px;height:36px;border-radius:50%;background:rgba(232,146,42,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fas fa-bell text-amber" style="font-size:.85rem"></i>
        </div>
        <div class="flex-grow-1">
            <div style="color:var(--text);font-size:.875rem;font-weight:{{ $isRead ? '400' : '600' }}">
                {{ $data['message'] ?? $data['title'] ?? 'Notification' }}
            </div>
            @if(!empty($data['body']))
            <div style="color:var(--muted);font-size:.8rem;margin-top:.2rem">{{ $data['body'] }}</div>
            @endif
            <div style="color:var(--muted);font-size:.75rem;margin-top:.4rem">
                {{ $notif->created_at->diffForHumans() }}
                @if(!$isRead)<span class="ms-2" style="color:var(--amber);font-size:.7rem">● New</span>@endif
            </div>
        </div>
        @if(!empty($data['url']))
        <a href="{{ $data['url'] }}" style="color:var(--amber);font-size:.8rem;flex-shrink:0">View →</a>
        @endif
    </div>
    @endforeach
</div>
<div class="mt-3">{{ $notifications->links('pagination::bootstrap-5') }}</div>
@endif
@endsection
