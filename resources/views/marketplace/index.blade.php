@extends('layouts.app')

@section('title', 'Marketplace')

@section('content')
<div class="container-fluid py-4" style="max-width:1400px">
    <div class="d-flex gap-4">
        {{-- Filter Sidebar --}}
        <div style="min-width:240px;width:240px">
            <div class="p-3 rounded-3" style="background:var(--surface);border:1px solid var(--border);position:sticky;top:80px">
                <h6 class="mb-3" style="color:var(--amber);font-weight:600;text-transform:uppercase;font-size:.75rem;letter-spacing:.08em">Filters</h6>
                <form method="GET" action="{{ route('marketplace') }}" id="filterForm">
                    <div class="mb-3">
                        <input type="text" name="keyword" class="form-control form-control-sm" placeholder="Search listings…" value="{{ request('keyword') }}" style="background:var(--dark);border-color:var(--border);color:var(--text)">
                    </div>

                    <div class="mb-3">
                        <p class="mb-2" style="color:var(--muted);font-size:.8rem;font-weight:600">CATEGORY</p>
                        @foreach($categories as $cat)
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" name="category_id[]" value="{{ $cat->id }}" id="cat{{ $cat->id }}"
                                {{ in_array($cat->id, (array) request('category_id')) ? 'checked' : '' }}>
                            <label class="form-check-label" for="cat{{ $cat->id }}" style="color:var(--text);font-size:.875rem">{{ $cat->name }}</label>
                        </div>
                        @endforeach
                    </div>

                    <div class="mb-3">
                        <p class="mb-2" style="color:var(--muted);font-size:.8rem;font-weight:600">TYPE</p>
                        @foreach(['both' => 'All', 'rental' => 'Rental', 'sale' => 'For Sale'] as $val => $label)
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="radio" name="listing_mode" value="{{ $val }}" id="mode_{{ $val }}"
                                {{ request('listing_mode', 'both') === $val ? 'checked' : '' }}>
                            <label class="form-check-label" for="mode_{{ $val }}" style="color:var(--text);font-size:.875rem">{{ $label }}</label>
                        </div>
                        @endforeach
                    </div>

                    <div class="mb-3">
                        <p class="mb-2" style="color:var(--muted);font-size:.8rem;font-weight:600">COUNTY</p>
                        <select name="county" class="form-select form-select-sm" style="background:var(--dark);border-color:var(--border);color:var(--text)">
                            <option value="">All Counties</option>
                            @foreach(['Nairobi','Mombasa','Kisumu','Nakuru','Eldoret','Thika','Malindi','Kitale','Garissa','Kakamega','Machakos','Nyeri','Meru','Embu','Kisii','Kericho','Bungoma','Busia','Homa Bay','Migori','Siaya','Vihiga','Trans Nzoia','Uasin Gishu','Nandi','Baringo','Laikipia','Samburu','Turkana','West Pokot','Elgeyo-Marakwet','Nyandarua','Kirinyaga',"Murang'a",'Kiambu','Makueni','Kitui','Tharaka-Nithi','Isiolo','Marsabit','Mandera','Wajir','Moyale','Tana River','Lamu','Taita-Taveta','Kwale','Kilifi','Kajiado'] as $county)
                            <option value="{{ $county }}" {{ request('county') === $county ? 'selected' : '' }}>{{ $county }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <p class="mb-2" style="color:var(--muted);font-size:.8rem;font-weight:600">SORT BY</p>
                        <select name="sort_by" class="form-select form-select-sm" style="background:var(--dark);border-color:var(--border);color:var(--text)">
                            <option value="newest" {{ request('sort_by','newest') === 'newest' ? 'selected' : '' }}>Newest</option>
                            <option value="price_asc" {{ request('sort_by') === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_desc" {{ request('sort_by') === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="rating" {{ request('sort_by') === 'rating' ? 'selected' : '' }}>Rating</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-sm w-100 mb-2" style="background:var(--amber);color:#000;font-weight:600">Apply Filters</button>
                    <a href="{{ route('marketplace') }}" class="btn btn-sm w-100" style="border:1px solid var(--border);color:var(--muted)">Clear Filters</a>
                </form>
            </div>
        </div>

        {{-- Listing Grid --}}
        <div class="flex-grow-1">
            {{-- Active filter chips --}}
            @if(request()->hasAny(['keyword','category_id','county','listing_mode','sort_by']))
            <div class="d-flex flex-wrap gap-2 mb-3">
                @if(request('keyword'))
                <span class="badge rounded-pill" style="background:var(--surface);border:1px solid var(--amber);color:var(--amber)">
                    "{{ request('keyword') }}" <a href="{{ request()->fullUrlWithoutQuery(['keyword']) }}" style="color:var(--amber);text-decoration:none">×</a>
                </span>
                @endif
                @if(request('county'))
                <span class="badge rounded-pill" style="background:var(--surface);border:1px solid var(--border);color:var(--text)">
                    {{ request('county') }} <a href="{{ request()->fullUrlWithoutQuery(['county']) }}" style="color:var(--muted);text-decoration:none">×</a>
                </span>
                @endif
                @if(request('listing_mode') && request('listing_mode') !== 'both')
                <span class="badge rounded-pill" style="background:var(--surface);border:1px solid var(--border);color:var(--text)">
                    {{ ucfirst(request('listing_mode')) }} <a href="{{ request()->fullUrlWithoutQuery(['listing_mode']) }}" style="color:var(--muted);text-decoration:none">×</a>
                </span>
                @endif
            </div>
            @endif

            <div class="d-flex justify-content-between align-items-center mb-3">
                <p class="mb-0" style="color:var(--muted);font-size:.9rem"><span style="color:var(--text);font-weight:600">{{ $listings->total() }}</span> listings found</p>
            </div>

            @if($listings->count())
            <div class="row g-3">
                @foreach($listings as $listing)
                <div class="col-md-6 col-xl-4">
                    @include('partials.listing-card', ['listing' => $listing])
                </div>
                @endforeach
            </div>
            <div class="mt-4">
                {{ $listings->links('pagination::bootstrap-5') }}
            </div>
            @else
            <div class="text-center py-5" style="color:var(--muted)">
                <i class="fas fa-search fa-3x mb-3 d-block" style="opacity:.3"></i>
                <h5 style="color:var(--text)">No listings found</h5>
                <p>Try adjusting your filters or <a href="{{ route('marketplace') }}" style="color:var(--amber)">browse all listings</a>.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
