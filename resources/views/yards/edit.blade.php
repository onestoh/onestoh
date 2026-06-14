@extends('layouts.dashboard')

@section('title', 'Edit Yard')
@section('page-title', 'Edit Yard')

@push('styles')
<style>
.form-section { background: var(--black); border: 1px solid var(--border); border-radius: 10px; padding: 1.5rem; margin-bottom: 1.25rem; }
.form-section h6 { color: var(--amber); font-size: .75rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 1rem; padding-bottom: .5rem; border-bottom: 1px solid var(--border); }
.form-control, .form-select, textarea.form-control { background: var(--surface); border: 1px solid var(--border); color: var(--text); }
.form-control:focus, .form-select:focus { background: var(--surface); border-color: var(--amber); color: var(--text); box-shadow: 0 0 0 2px rgba(232,146,42,.15); }
.form-label { color: var(--muted); font-size: .82rem; margin-bottom: .3rem; }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="{{ route('yards.index') }}" style="color:var(--muted)"><i class="fas fa-arrow-left"></i></a>
            <h4 style="color:var(--text);font-weight:700;margin:0">Edit Yard — {{ $yard->name }}</h4>
        </div>

        <form method="POST" action="{{ route('yards.update', $yard) }}" enctype="multipart/form-data">
            @csrf @method('PUT')

            <div class="form-section">
                <h6><i class="fas fa-warehouse me-1"></i>Yard Information</h6>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Yard / Business Name *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $yard->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $yard->phone) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $yard->email) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $yard->description) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Logo</label>
                        @if($yard->logo)
                        <div class="mb-2">
                            <img src="{{ Storage::url($yard->logo) }}" height="48" class="rounded">
                        </div>
                        @endif
                        <input type="file" name="logo" class="form-control" accept="image/*">
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h6><i class="fas fa-map-marker-alt me-1"></i>Location</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">County *</label>
                        <input type="text" name="county" class="form-control" value="{{ old('county', $yard->county) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">City / Town</label>
                        <input type="text" name="city" class="form-control" value="{{ old('city', $yard->city) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Physical Address</label>
                        <input type="text" name="address" class="form-control" value="{{ old('address', $yard->address) }}">
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3 justify-content-end">
                <a href="{{ route('yards.index') }}" class="btn px-4" style="border:1px solid var(--border);color:var(--muted)">Cancel</a>
                <button type="submit" class="btn btn-amber px-5"><i class="fas fa-save me-2"></i>Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
