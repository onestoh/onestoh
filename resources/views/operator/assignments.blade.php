@extends('layouts.dashboard')

@section('title', 'My Assignments')
@section('page-title', 'Operator Assignments')

@section('content')
<h4 class="mb-4" style="color:var(--text);font-weight:700">My Assignments</h4>

<div class="row g-3 mb-4">
    <div class="col-4">
        <div class="p-3 rounded-3" style="background:var(--surface);border:1px solid var(--border)">
            <div style="color:var(--muted);font-size:.75rem;text-transform:uppercase">Active</div>
            <div style="color:var(--green);font-size:1.4rem;font-weight:700">{{ $stats['active'] }}</div>
        </div>
    </div>
    <div class="col-4">
        <div class="p-3 rounded-3" style="background:var(--surface);border:1px solid var(--border)">
            <div style="color:var(--muted);font-size:.75rem;text-transform:uppercase">Upcoming</div>
            <div style="color:var(--amber);font-size:1.4rem;font-weight:700">{{ $stats['upcoming'] }}</div>
        </div>
    </div>
    <div class="col-4">
        <div class="p-3 rounded-3" style="background:var(--surface);border:1px solid var(--border)">
            <div style="color:var(--muted);font-size:.75rem;text-transform:uppercase">Completed</div>
            <div style="color:var(--text);font-size:1.4rem;font-weight:700">{{ $stats['completed'] }}</div>
        </div>
    </div>
</div>

@if($assignments->isEmpty())
<div class="text-center py-5" style="color:var(--muted)">
    <i class="fas fa-steering-wheel fa-3x mb-3 d-block" style="opacity:.3"></i>
    <h5 style="color:var(--text)">No assignments yet</h5>
    <p>Owners will assign you to bookings. Make sure your profile is complete.</p>
</div>
@else
@foreach($assignments as $assignment)
<div class="p-3 rounded-3 mb-3" style="background:var(--surface);border:1px solid var(--border)">
    <div class="d-flex gap-3 align-items-start">
        @if($assignment->listing?->primaryPhoto)
        <img src="{{ Storage::url($assignment->listing->primaryPhoto->file_path) }}"
            style="width:72px;height:56px;object-fit:cover;border-radius:7px;flex-shrink:0" alt="">
        @else
        <div style="width:72px;height:56px;background:var(--black);border-radius:7px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fas fa-truck text-amber"></i>
        </div>
        @endif

        <div class="flex-grow-1">
            <div class="fw-semibold mb-1" style="color:var(--text)">{{ $assignment->listing?->title }}</div>
            <div style="color:var(--muted);font-size:.8rem">
                <i class="fas fa-hashtag me-1"></i>{{ $assignment->booking_ref }}
                &nbsp;·&nbsp;
                @if($assignment->listing?->yard)
                <i class="fas fa-warehouse me-1"></i>{{ $assignment->listing->yard->name }}
                &nbsp;·&nbsp;
                @endif
                <i class="fas fa-user me-1"></i>Client: {{ $assignment->client?->name }}
            </div>
            <div style="color:var(--muted);font-size:.8rem;margin-top:.25rem">
                <i class="fas fa-calendar me-1"></i>
                {{ \Carbon\Carbon::parse($assignment->start_datetime)->format('D, d M Y · H:i') }}
                → {{ \Carbon\Carbon::parse($assignment->end_datetime)->format('d M Y') }}
            </div>
        </div>

        <div class="text-end flex-shrink-0">
            @php
            $statusColors = ['active'=>'var(--green)','confirmed'=>'#63b3ed','completed'=>'var(--muted)','cancelled'=>'var(--danger)'];
            $color = $statusColors[$assignment->status] ?? 'var(--muted)';
            @endphp
            <span style="color:{{ $color }};font-size:.78rem;font-weight:600">{{ str_replace('_',' ',ucfirst($assignment->status)) }}</span>
            @if($assignment->status === 'active')
            <form method="POST" action="{{ route('operator.status', $assignment) }}" class="mt-2">
                @csrf
                <input type="hidden" name="status" value="completed">
                <button type="submit" class="btn btn-sm" style="background:var(--green);color:#000;font-size:.78rem">Mark Done</button>
            </form>
            @endif
        </div>
    </div>
</div>
@endforeach
<div class="mt-3">{{ $assignments->links('pagination::bootstrap-5') }}</div>
@endif
@endsection
