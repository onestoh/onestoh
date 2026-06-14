@php
$counties = ['Baringo','Bomet','Bungoma','Busia','Elgeyo-Marakwet','Embu','Garissa','Homa Bay','Isiolo','Kajiado','Kakamega','Kericho','Kiambu','Kilifi','Kirinyaga','Kisii','Kisumu','Kitui','Kwale','Laikipia','Lamu','Machakos','Makueni','Mandera','Marsabit','Meru','Migori','Mombasa','Murang\'a','Nairobi','Nakuru','Nandi','Narok','Nyamira','Nyandarua','Nyeri','Samburu','Siaya','Taita-Taveta','Tana River','Tharaka-Nithi','Trans Nzoia','Turkana','Uasin Gishu','Vihiga','Wajir','West Pokot'];
$l = $listing ?? null;
@endphp

<style>
.form-section { background: var(--black); border: 1px solid var(--border); border-radius: 10px; padding: 1.5rem; margin-bottom: 1.25rem; }
.form-section h6 { color: var(--amber); font-size: .75rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; margin-bottom: 1rem; padding-bottom: .5rem; border-bottom: 1px solid var(--border); }
.form-control, .form-select, textarea.form-control { background: var(--surface); border: 1px solid var(--border); color: var(--text); }
.form-control:focus, .form-select:focus { background: var(--surface); border-color: var(--amber); color: var(--text); box-shadow: 0 0 0 2px rgba(232,146,42,.15); }
.form-label { color: var(--muted); font-size: .82rem; margin-bottom: .3rem; }
.photo-preview { display: flex; gap: .5rem; flex-wrap: wrap; margin-top: .5rem; }
.photo-thumb { position: relative; }
.photo-thumb img { width: 80px; height: 64px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border); }
.photo-thumb .remove-btn { position: absolute; top: -6px; right: -6px; background: var(--danger); color: #fff; border: none; border-radius: 50%; width: 18px; height: 18px; font-size: .65rem; cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 0; }
</style>

{{-- Basic Info --}}
<div class="form-section">
    <h6><i class="fas fa-info-circle me-1"></i>Basic Information</h6>
    <div class="row g-3">
        <div class="col-12">
            <label class="form-label">Listing Title *</label>
            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                value="{{ old('title', $l?->title) }}" placeholder="e.g. 2020 Toyota Hilux Double Cab — Nairobi" required>
            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Category *</label>
            <select name="asset_category_id" class="form-select @error('asset_category_id') is-invalid @enderror" required>
                <option value="">Select category…</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ old('asset_category_id', $l?->asset_category_id) == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
                @endforeach
            </select>
            @error('asset_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Listing Type *</label>
            <select name="listing_mode" class="form-select" required>
                <option value="rental" {{ old('listing_mode', $l?->listing_mode) === 'rental' ? 'selected' : '' }}>For Rent</option>
                <option value="sale" {{ old('listing_mode', $l?->listing_mode) === 'sale' ? 'selected' : '' }}>For Sale</option>
                <option value="both" {{ old('listing_mode', $l?->listing_mode) === 'both' ? 'selected' : '' }}>Rent & Sale</option>
            </select>
        </div>
        @if($yards->count())
        <div class="col-md-6">
            <label class="form-label">Assign to Yard</label>
            <select name="yard_id" class="form-select">
                <option value="">No yard / Individual listing</option>
                @foreach($yards as $yard)
                <option value="{{ $yard->id }}" {{ old('yard_id', $l?->yard_id) == $yard->id ? 'selected' : '' }}>{{ $yard->name }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <div class="col-12">
            <label class="form-label">Description *</label>
            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4"
                placeholder="Describe the vehicle/machinery, its condition, special features, usage rules…" required>{{ old('description', $l?->description) }}</textarea>
            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

{{-- Vehicle Details --}}
<div class="form-section">
    <h6><i class="fas fa-car me-1"></i>Vehicle / Asset Details</h6>
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Make</label>
            <input type="text" name="make" class="form-control" value="{{ old('make', $l?->make) }}" placeholder="Toyota, CAT, Komatsu…">
        </div>
        <div class="col-md-4">
            <label class="form-label">Model</label>
            <input type="text" name="model" class="form-control" value="{{ old('model', $l?->model) }}" placeholder="Hilux, 320D…">
        </div>
        <div class="col-md-4">
            <label class="form-label">Year</label>
            <input type="number" name="year" class="form-control" value="{{ old('year', $l?->year) }}" min="1970" max="{{ date('Y') + 1 }}" placeholder="{{ date('Y') }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Registration Plate</label>
            <input type="text" name="registration_plate" class="form-control" value="{{ old('registration_plate', $l?->registration_plate) }}" placeholder="KDA 123A">
        </div>
        <div class="col-md-4">
            <label class="form-label">Fuel Type</label>
            <select name="fuel_type" class="form-select">
                <option value="">— Select —</option>
                @foreach(['petrol'=>'Petrol','diesel'=>'Diesel','electric'=>'Electric','hybrid'=>'Hybrid','other'=>'Other'] as $v => $lbl)
                <option value="{{ $v }}" {{ old('fuel_type', $l?->fuel_type) === $v ? 'selected' : '' }}>{{ $lbl }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Transmission</label>
            <select name="transmission" class="form-select">
                <option value="">— Select —</option>
                <option value="manual" {{ old('transmission', $l?->transmission) === 'manual' ? 'selected' : '' }}>Manual</option>
                <option value="automatic" {{ old('transmission', $l?->transmission) === 'automatic' ? 'selected' : '' }}>Automatic</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Seats</label>
            <input type="number" name="seats" class="form-control" value="{{ old('seats', $l?->seats) }}" min="1" placeholder="5">
        </div>
        <div class="col-md-4">
            <label class="form-label">Load Capacity</label>
            <input type="text" name="load_capacity" class="form-control" value="{{ old('load_capacity', $l?->load_capacity) }}" placeholder="5 Tons, 10 m³…">
        </div>
        <div class="col-md-4">
            <label class="form-label">Drive Mode *</label>
            <select name="drive_mode" class="form-select" required>
                <option value="self_drive" {{ old('drive_mode', $l?->drive_mode) === 'self_drive' ? 'selected' : '' }}>Self Drive</option>
                <option value="with_driver" {{ old('drive_mode', $l?->drive_mode) === 'with_driver' ? 'selected' : '' }}>With Driver</option>
                <option value="either" {{ old('drive_mode', $l?->drive_mode) === 'either' ? 'selected' : '' }}>Either</option>
            </select>
        </div>
        <div class="col-12">
            <label class="form-label">Features <span style="color:var(--muted)">(comma-separated)</span></label>
            <input type="text" name="features" class="form-control" value="{{ old('features', is_array($l?->features) ? implode(', ', $l->features) : $l?->features) }}" placeholder="GPS, AC, Bull bar, Winch, Tow hook…">
        </div>
    </div>
</div>

{{-- Pricing --}}
<div class="form-section">
    <h6><i class="fas fa-tag me-1"></i>Pricing (KES)</h6>
    <div class="row g-3">
        <div class="col-md-3">
            <label class="form-label">Hourly Rate</label>
            <div class="input-group">
                <span class="input-group-text" style="background:var(--border);border-color:var(--border);color:var(--muted)">KES</span>
                <input type="number" name="hourly_rate" class="form-control" value="{{ old('hourly_rate', $l?->hourly_rate) }}" min="0" step="50">
            </div>
        </div>
        <div class="col-md-3">
            <label class="form-label">Daily Rate</label>
            <div class="input-group">
                <span class="input-group-text" style="background:var(--border);border-color:var(--border);color:var(--muted)">KES</span>
                <input type="number" name="daily_rate" class="form-control" value="{{ old('daily_rate', $l?->daily_rate) }}" min="0" step="100">
            </div>
        </div>
        <div class="col-md-3">
            <label class="form-label">Weekly Rate</label>
            <div class="input-group">
                <span class="input-group-text" style="background:var(--border);border-color:var(--border);color:var(--muted)">KES</span>
                <input type="number" name="weekly_rate" class="form-control" value="{{ old('weekly_rate', $l?->weekly_rate) }}" min="0" step="500">
            </div>
        </div>
        <div class="col-md-3">
            <label class="form-label">Monthly Rate</label>
            <div class="input-group">
                <span class="input-group-text" style="background:var(--border);border-color:var(--border);color:var(--muted)">KES</span>
                <input type="number" name="monthly_rate" class="form-control" value="{{ old('monthly_rate', $l?->monthly_rate) }}" min="0" step="1000">
            </div>
        </div>
        <div class="col-md-4">
            <label class="form-label">Sale Price</label>
            <div class="input-group">
                <span class="input-group-text" style="background:var(--border);border-color:var(--border);color:var(--muted)">KES</span>
                <input type="number" name="sale_price" class="form-control" value="{{ old('sale_price', $l?->sale_price) }}" min="0" step="1000">
            </div>
        </div>
        <div class="col-md-4">
            <label class="form-label">Security Deposit</label>
            <div class="input-group">
                <span class="input-group-text" style="background:var(--border);border-color:var(--border);color:var(--muted)">KES</span>
                <input type="number" name="security_deposit" class="form-control" value="{{ old('security_deposit', $l?->security_deposit) }}" min="0" step="500">
            </div>
        </div>
        <div class="col-md-4">
            <label class="form-label">Driver Surcharge (Daily)</label>
            <div class="input-group">
                <span class="input-group-text" style="background:var(--border);border-color:var(--border);color:var(--muted)">KES</span>
                <input type="number" name="driver_surcharge_daily" class="form-control" value="{{ old('driver_surcharge_daily', $l?->driver_surcharge_daily) }}" min="0" step="100">
            </div>
        </div>
    </div>
</div>

{{-- Location --}}
<div class="form-section">
    <h6><i class="fas fa-map-marker-alt me-1"></i>Location</h6>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">County *</label>
            <select name="county" class="form-select @error('county') is-invalid @enderror" required>
                <option value="">Select county…</option>
                @foreach($counties as $county)
                <option value="{{ $county }}" {{ old('county', $l?->county) === $county ? 'selected' : '' }}>{{ $county }}</option>
                @endforeach
            </select>
            @error('county')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">City / Town</label>
            <input type="text" name="city" class="form-control" value="{{ old('city', $l?->city) }}" placeholder="Westlands, Mombasa CBD…">
        </div>
    </div>
</div>

{{-- Photos --}}
<div class="form-section">
    <h6><i class="fas fa-images me-1"></i>Photos</h6>

    @if($l && $l->photos->count())
    <p style="color:var(--muted);font-size:.82rem" class="mb-2">Existing photos — first is primary:</p>
    <div class="photo-preview mb-3">
        @foreach($l->photos as $photo)
        <div class="photo-thumb">
            <img src="{{ Storage::url($photo->file_path) }}" alt="">
            <form method="POST" action="{{ route('listings.photos.delete', [$l, $photo]) }}" style="display:inline">
                @csrf @method('DELETE')
                <button type="submit" class="remove-btn" title="Remove"><i class="fas fa-times"></i></button>
            </form>
        </div>
        @endforeach
    </div>
    @endif

    <label class="form-label">{{ $l?->photos->count() ? 'Add More Photos' : 'Upload Photos' }} <span style="color:var(--muted)">(JPG/PNG · max 5MB each)</span></label>
    <input type="file" name="photos[]" class="form-control" accept="image/*" multiple>
    <div style="color:var(--muted);font-size:.75rem;margin-top:.25rem">First photo becomes the primary thumbnail. You can upload up to 10 photos.</div>
</div>
