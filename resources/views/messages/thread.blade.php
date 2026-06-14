@extends('layouts.dashboard')

@section('title', 'Chat — ' . ($otherUser?->name ?? 'Unknown'))
@section('page-title', 'Messages')

@push('styles')
<style>
.chat-wrap { display: flex; flex-direction: column; height: calc(100vh - 200px); min-height: 400px; }
.chat-messages { flex: 1; overflow-y: auto; padding: 1rem; display: flex; flex-direction: column; gap: .75rem; }
.chat-bubble { max-width: 70%; padding: .65rem 1rem; border-radius: 14px; font-size: .875rem; line-height: 1.5; word-break: break-word; }
.bubble-sent { background: var(--amber); color: #000; border-bottom-right-radius: 4px; align-self: flex-end; }
.bubble-recv { background: var(--surface); border: 1px solid var(--border); color: var(--text); border-bottom-left-radius: 4px; align-self: flex-start; }
.bubble-time { font-size: .7rem; margin-top: .25rem; opacity: .65; }
.chat-footer { border-top: 1px solid var(--border); padding: 1rem; background: var(--black); }
.chat-input { background: var(--surface); border: 1px solid var(--border); color: var(--text); resize: none; }
.chat-input:focus { background: var(--surface); border-color: var(--amber); color: var(--text); box-shadow: none; }
</style>
@endpush

@section('content')
<div class="mb-3">
    <a href="{{ route('messages.index') }}" style="color:var(--muted);font-size:.85rem;text-decoration:none">
        <i class="fas fa-arrow-left me-1"></i>Back to Messages
    </a>
</div>

<div class="card" style="border-radius:12px;overflow:hidden">
    <div class="card-header d-flex align-items-center gap-3" style="background:var(--black);border-bottom:1px solid var(--border);padding:.85rem 1.25rem">
        <div style="width:40px;height:40px;border-radius:50%;background:rgba(232,146,42,.15);border:1px solid rgba(232,146,42,.3);display:flex;align-items:center;justify-content:center;font-weight:700;color:var(--amber)">
            {{ strtoupper(substr($otherUser?->name ?? '?', 0, 2)) }}
        </div>
        <div>
            <div style="font-weight:600">{{ $otherUser?->name ?? 'Unknown User' }}</div>
            <div style="color:var(--muted);font-size:.75rem">
                Re: <span style="color:var(--amber);font-family:monospace">{{ $booking->booking_ref }}</span>
                &nbsp;·&nbsp; {{ $booking->listing?->title }}
            </div>
        </div>
    </div>

    <div class="chat-wrap">
        <div class="chat-messages" id="chatMessages">
            @forelse($messages as $msg)
            @php $isMine = $msg->from_user_id === auth()->id(); @endphp
            <div class="d-flex flex-column {{ $isMine ? 'align-items-end' : 'align-items-start' }}">
                <div class="chat-bubble {{ $isMine ? 'bubble-sent' : 'bubble-recv' }}">
                    {{ $msg->message }}
                    <div class="bubble-time {{ $isMine ? 'text-end' : '' }}">
                        {{ $msg->created_at->format('H:i · d M') }}
                        @if($isMine)
                        <i class="fas fa-check ms-1" style="{{ $msg->is_read ? 'color:var(--green)' : '' }}"></i>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-4" style="color:var(--muted)">
                <i class="fas fa-comment fa-2x mb-2" style="opacity:.3"></i>
                <p class="mb-0 font-size:.85rem">No messages yet. Start the conversation.</p>
            </div>
            @endforelse
        </div>

        <div class="chat-footer">
            <form method="POST" action="{{ route('messages.send', $booking) }}" class="d-flex gap-2 align-items-end">
                @csrf
                <textarea name="message" class="form-control chat-input flex-grow-1"
                    rows="2" placeholder="Type your message..." required maxlength="2000"></textarea>
                <button type="submit" class="btn btn-amber px-4" style="height:fit-content">
                    <i class="fas fa-paper-plane me-1"></i>Send
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Scroll to bottom of chat
    const chatEl = document.getElementById('chatMessages');
    if (chatEl) chatEl.scrollTop = chatEl.scrollHeight;
</script>
@endpush
