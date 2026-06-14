@extends('layouts.dashboard')

@section('title', 'Edit Listing')
@section('page-title', 'Edit Listing')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-9">
        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="{{ route('listings.index') }}" style="color:var(--muted)"><i class="fas fa-arrow-left"></i></a>
            <div>
                <h4 style="color:var(--text);font-weight:700;margin:0">Edit Listing</h4>
                <span style="color:var(--muted);font-size:.82rem">{{ $listing->title }}</span>
            </div>
        </div>

        @if($errors->any())
        <div class="alert mb-4" style="background:rgba(232,64,64,.1);border:1px solid rgba(232,64,64,.3);color:#f87171">
            <i class="fas fa-exclamation-triangle me-2"></i>Please fix the errors below.
        </div>
        @endif

        <form method="POST" action="{{ route('listings.update', $listing) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('listings.partials.form')
            <div class="d-flex gap-3 justify-content-end">
                <a href="{{ route('listings.index') }}" class="btn px-4" style="border:1px solid var(--border);color:var(--muted)">Cancel</a>
                <button type="submit" class="btn btn-amber px-5">
                    <i class="fas fa-save me-2"></i>Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
