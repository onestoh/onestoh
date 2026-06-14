@extends('layouts.dashboard')

@section('title', 'My Yards')
@section('page-title', 'My Yards')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 style="color:var(--text);font-weight:700;margin:0">My Yards</h4>
    <a href="{{ route('yards.create') }}" class="btn btn-amber"><i class="fas fa-plus me-2"></i>Register Yard</a>
</div>

@if($yards->isEmpty())
<div class="text-center py-5" style="color:var(--muted)">
    <i class="fas fa-warehouse fa-3x mb-3 d-block" style="opacity:.3"></i>
    <h5 style="color:var(--text)">No yards registered</h5>
    <p>Register your yard to manage your fleet centrally.</p>
    <a href="{{ route('yards.create') }}" class="btn btn-amber mt-2">Register a Yard</a>
</div>
@else
<div class="row g-3">
    @foreach($yards as $yard)
    <div class="col-md-6 col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    @if($yard->logo)
                        <img src="{{ Storage::url($yard->logo) }}" class="rounded-circle" width="48" height="48" style="object-fit:cover">
                    @else
                        <div style="width:48px;height:48px;border-radius:50%;background:rgba(232,146,42,.1);display:flex;align-items:center;justify-content:center;"><i class="fas fa-warehouse text-amber"></i></div>
                    @endif
                    <div>
                        <div class="fw-semibold" style="color:var(--text)">{{ $yard->name }}</div>
                        <div style="font-size:.78rem;color:var(--muted)">{{ $yard->city }}, {{ $yard->county }}</div>
                    </div>
                </div>
                <div class="d-flex gap-3 mb-3" style="font-size:.82rem">
                    <div>
                        <span style="color:var(--muted)">Status</span><br>
                        @if($yard->status === 'active')
                            <span style="color:var(--green)"><i class="fas fa-check-circle me-1"></i>Active</span>
                        @elseif($yard->status === 'pending')
                            <span style="color:var(--amber)"><i class="fas fa-clock me-1"></i>Pending</span>
                        @else
                            <span style="color:var(--danger)">{{ ucfirst($yard->status) }}</span>
                        @endif
                    </div>
                    <div>
                        <span style="color:var(--muted)">Listings</span><br>
                        <span style="color:var(--text);font-weight:600">{{ $yard->listings_count }}</span>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('yards.edit', $yard) }}" class="btn btn-sm flex-grow-1" style="border:1px solid var(--border);color:var(--muted)"><i class="fas fa-edit me-1"></i>Edit</a>
                    <form method="POST" action="{{ route('yards.destroy', $yard) }}" onsubmit="return confirm('Remove this yard?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm" style="border:1px solid rgba(232,64,64,.3);color:var(--danger)"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection
