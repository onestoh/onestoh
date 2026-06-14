@extends('layouts.app')

@section('title', $yard->name)

@section('content')
<div class="container py-4">
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card p-4 text-center">
                @if($yard->logo)
                <img src="{{ Storage::url($yard->logo) }}" class="rounded-circle mb-3" width="80" height="80" style="object-fit:cover">
                @else
                <div class="mx-auto mb-3" style="width:80px;height:80px;border-radius:50%;background:rgba(232,146,42,.1);display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-warehouse text-amber fa-2x"></i>
                </div>
                @endif
                <h4 style="color:var(--text);font-weight:700">{{ $yard->name }}</h4>
                <p style="color:var(--muted);font-size:.875rem">
                    <i class="fas fa-map-marker-alt me-1"></i>{{ $yard->city }}, {{ $yard->county }}
                </p>
                @if($yard->description)
                <p style="color:var(--muted);font-size:.875rem">{{ $yard->description }}</p>
                @endif
                @if($yard->phone)
                <p style="color:var(--muted);font-size:.875rem"><i class="fas fa-phone me-1"></i>{{ $yard->phone }}</p>
                @endif
            </div>
        </div>
        <div class="col-lg-8">
            <h5 style="color:var(--text);font-weight:700;margin-bottom:1rem">{{ $yard->listings->count() }} Listing(s)</h5>
            <div class="row g-3">
                @foreach($yard->listings as $listing)
                <div class="col-md-6">
                    @include('partials.listing-card', compact('listing'))
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
