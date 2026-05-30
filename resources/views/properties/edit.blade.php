@extends('layouts.app')
@section('title', 'Edit Property — EstateYard')

@push('styles')
<style>
  .property-form-page { max-width: 900px; margin: 0 auto; padding: 100px 24px 60px; }
  .form-section { background: var(--navy3); border: 1px solid var(--border-dim); border-radius: var(--radius); padding: 28px; margin-bottom: 24px; }
  .form-section-title { font-family: var(--font-serif); font-size: 18px; font-weight: 700; color: var(--white); margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid var(--border-dim); display: flex; align-items: center; gap: 10px; }
  .form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
  .form-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }
  .amenities-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 10px; }
  .amenity-item { display: flex; align-items: center; gap: 8px; background: var(--surface); border: 1px solid var(--border-dim); border-radius: 8px; padding: 10px 12px; cursor: pointer; transition: all .2s; }
  .amenity-item:hover { border-color: var(--gold); }
  .amenity-item input[type=checkbox] { accent-color: var(--gold); width: 15px; height: 15px; flex-shrink: 0; }
  .amenity-item label { font-size: 13px; color: var(--muted); cursor: pointer; }
  .image-upload-zone { border: 2px dashed var(--border); border-radius: var(--radius); padding: 40px; text-align: center; cursor: pointer; transition: all .2s; background: var(--surface); }
  .image-upload-zone:hover { border-color: var(--gold); }
  .image-preview-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 10px; margin-top: 16px; }
  .image-preview-item { position: relative; aspect-ratio: 1; border-radius: 8px; overflow: hidden; }
  .image-preview-item img { width: 100%; height: 100%; object-fit: cover; }
  .image-preview-item .remove-btn { position: absolute; top: 4px; right: 4px; background: rgba(0,0,0,0.7); border: none; color: #fff; border-radius: 50%; width: 22px; height: 22px; cursor: pointer; font-size: 12px; display: flex; align-items: center; justify-content: center; }
  .existing-images-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 10px; margin-bottom: 16px; }
  .existing-image { position: relative; aspect-ratio: 1; border-radius: 8px; overflow: hidden; border: 1px solid var(--border-dim); }
  .existing-image img { width: 100%; height: 100%; object-fit: cover; }
  .page-breadcrumb { font-size: 13px; color: var(--muted); margin-bottom: 8px; }
  .page-breadcrumb a { color: var(--gold); text-decoration: none; }
  .page-heading { font-family: var(--font-serif); font-size: 32px; font-weight: 700; color: var(--white); margin-bottom: 28px; }
  @media(max-width:640px) { .form-grid-2, .form-grid-3 { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="property-form-page">

  <div class="page-breadcrumb">
    <a href="{{ url('/dashboard') }}">Dashboard</a> &rsaquo; Edit Property
  </div>
  <div class="page-heading">✏️ Edit Property</div>

  @if($errors->any())
  <div class="alert alert-red" style="margin-bottom:20px;">
    <strong>Please fix the following errors:</strong>
    <ul style="margin:8px 0 0 16px; font-size:13px;">
      @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
    </ul>
  </div>
  @endif

  @if(session('success'))
  <div class="alert alert-green" style="margin-bottom:20px;">{{ session('success') }}</div>
  @endif

  <form action="{{ url('/properties/' . $property->id) }}" method="POST" enctype="multipart/form-data" data-validate>
    @csrf
    @method('PUT')

    {{-- BASIC INFO --}}
    <div class="form-section">
      <div class="form-section-title">🏷️ Basic Information</div>
      <div class="form-group" style="margin-bottom:16px;">
        <label class="form-label">Property Title <span style="color:var(--red);">*</span></label>
        <input type="text" name="title" class="form-control" placeholder="e.g. Modern 4-Bed Villa in Karen" value="{{ old('title', $property->title) }}" required maxlength="255">
      </div>
      <div class="form-grid-2">
        <div class="form-group">
          <label class="form-label">Property Type <span style="color:var(--red);">*</span></label>
          <select name="type" class="form-control" required>
            <option value="">— Select Type —</option>
            @foreach($propertyTypes as $t)
            <option value="{{ $t }}" {{ old('type', $property->type) === $t ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$t)) }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Listing Type <span style="color:var(--red);">*</span></label>
          <select name="listing_type" class="form-control" required>
            <option value="">— Select Listing —</option>
            @foreach($listingTypes as $lt)
            <option value="{{ $lt }}" {{ old('listing_type', $property->listing_type) === $lt ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$lt)) }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>

    {{-- DESCRIPTION --}}
    <div class="form-section">
      <div class="form-section-title">📝 Description</div>
      <div class="form-group">
        <label class="form-label">Property Description <span style="color:var(--red);">*</span></label>
        <textarea name="description" class="form-control" rows="6" required style="resize:vertical;">{{ old('description', $property->description) }}</textarea>
      </div>
    </div>

    {{-- LOCATION --}}
    <div class="form-section">
      <div class="form-section-title">📍 Location</div>
      <div class="form-grid-2">
        <div class="form-group">
          <label class="form-label">County <span style="color:var(--red);">*</span></label>
          <select name="county" class="form-control" required>
            <option value="">— Select County —</option>
            @foreach($counties as $county)
            <option value="{{ $county }}" {{ old('county', $property->county) === $county ? 'selected' : '' }}>{{ $county }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Constituency / Sub-County</label>
          <input type="text" name="constituency" class="form-control" placeholder="e.g. Westlands" value="{{ old('constituency', $property->constituency) }}">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Specific Location / Address <span style="color:var(--red);">*</span></label>
        <input type="text" name="location" class="form-control" value="{{ old('location', $property->location) }}" required>
      </div>
    </div>

    {{-- PROPERTY DETAILS --}}
    <div class="form-section">
      <div class="form-section-title">🏠 Property Details</div>
      <div class="form-grid-3">
        <div class="form-group">
          <label class="form-label">Bedrooms</label>
          <input type="number" name="bedrooms" class="form-control" min="0" max="50" value="{{ old('bedrooms', $property->bedrooms) }}">
        </div>
        <div class="form-group">
          <label class="form-label">Bathrooms</label>
          <input type="number" name="bathrooms" class="form-control" min="0" max="50" value="{{ old('bathrooms', $property->bathrooms) }}">
        </div>
        <div class="form-group">
          <label class="form-label">Area (sq ft)</label>
          <input type="number" name="area_sqft" class="form-control" min="0" step="0.01" value="{{ old('area_sqft', $property->area_sqft) }}">
        </div>
        <div class="form-group">
          <label class="form-label">Year Built</label>
          <input type="number" name="year_built" class="form-control" min="1900" max="2100" value="{{ old('year_built', $property->year_built) }}">
        </div>
        <div class="form-group">
          <label class="form-label">Number of Floors</label>
          <input type="number" name="floors" class="form-control" min="1" value="{{ old('floors', $property->floors) }}">
        </div>
      </div>
    </div>

    {{-- PRICE --}}
    <div class="form-section">
      <div class="form-section-title">💰 Price</div>
      <div class="form-grid-2">
        <div class="form-group">
          <label class="form-label">Price (KES) <span style="color:var(--red);">*</span></label>
          <input type="number" name="price" class="form-control" min="1" step="0.01" value="{{ old('price', $property->price) }}" required>
        </div>
        <div class="form-group">
          <label class="form-label">Price Period</label>
          <select name="price_period" class="form-control">
            <option value="">— N/A (sale) —</option>
            @foreach(['monthly','yearly','weekly','daily'] as $pp)
            <option value="{{ $pp }}" {{ old('price_period', $property->price_period) === $pp ? 'selected' : '' }}>{{ ucfirst($pp) }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>

    {{-- AMENITIES --}}
    <div class="form-section">
      <div class="form-section-title">✨ Amenities</div>
      @php $existingAmenities = is_array($property->amenities) ? $property->amenities : []; @endphp
      <div class="amenities-grid">
        @foreach(['Swimming Pool','Gym','Parking','Security','CCTV','Generator','Borehole','Garden','Balcony','Air Conditioning','Lift/Elevator','WiFi','DSQ','Solar Panels','Backup Water','Play Area','Rooftop Terrace','Laundry Room'] as $amenity)
        <div class="amenity-item">
          <input type="checkbox" name="amenities[]" id="amenity_{{ Str::slug($amenity) }}" value="{{ $amenity }}"
            {{ (is_array(old('amenities')) ? in_array($amenity, old('amenities')) : in_array($amenity, $existingAmenities)) ? 'checked' : '' }}>
          <label for="amenity_{{ Str::slug($amenity) }}">{{ $amenity }}</label>
        </div>
        @endforeach
      </div>
    </div>

    {{-- EXISTING IMAGES --}}
    @if(!empty($property->images) && count($property->images))
    <div class="form-section">
      <div class="form-section-title">🖼️ Current Images</div>
      <div class="existing-images-grid">
        @foreach($property->images as $img)
        <div class="existing-image">
          <img src="{{ Storage::disk('public')->url($img) }}" alt="Property image">
        </div>
        @endforeach
      </div>
      <p style="font-size:13px; color:var(--muted);">Upload new images below to add to existing ones.</p>
    </div>
    @endif

    {{-- NEW IMAGES --}}
    <div class="form-section">
      <div class="form-section-title">📷 Add More Images <span style="font-size:13px; font-weight:400; color:var(--muted);">(appended to existing)</span></div>
      <div class="image-upload-zone" onclick="document.getElementById('imageInput').click();" id="uploadZone">
        <div style="font-size:36px; margin-bottom:12px;">📷</div>
        <div style="font-size:15px; color:var(--white); font-weight:600; margin-bottom:6px;">Click to upload new images</div>
        <div style="font-size:13px; color:var(--muted);">JPG, PNG, WebP · Max 5MB per image</div>
      </div>
      <input type="file" id="imageInput" name="images[]" multiple accept="image/*" style="display:none;" onchange="previewImages(this)">
      <div class="image-preview-grid" id="imagePreviewGrid"></div>
    </div>

    {{-- SUBMIT --}}
    <div style="display:flex; gap:12px; justify-content:flex-end; flex-wrap:wrap;">
      <a href="{{ url('/dashboard') }}" class="btn btn-outline" style="padding:14px 24px;">Cancel</a>
      <button type="submit" class="btn btn-gold" style="padding:14px 32px; font-size:15px;">
        ✅ Save Changes
      </button>
    </div>

  </form>
</div>
@endsection

@push('scripts')
<script>
function previewImages(input) {
  const grid = document.getElementById('imagePreviewGrid');
  grid.innerHTML = '';
  Array.from(input.files).slice(0, 10).forEach((file, i) => {
    const reader = new FileReader();
    reader.onload = e => {
      const div = document.createElement('div');
      div.className = 'image-preview-item';
      div.innerHTML = `<img src="${e.target.result}" alt="Preview ${i+1}">
        <button type="button" class="remove-btn" onclick="removePreview(${i})">✕</button>`;
      grid.appendChild(div);
    };
    reader.readAsDataURL(file);
  });
  if (input.files.length) document.getElementById('uploadZone').style.borderColor = 'var(--gold)';
}
function removePreview(i) {
  const input = document.getElementById('imageInput');
  const dt = new DataTransfer();
  Array.from(input.files).forEach((f, idx) => { if (idx !== i) dt.items.add(f); });
  input.files = dt.files;
  previewImages(input);
}
</script>
@endpush
