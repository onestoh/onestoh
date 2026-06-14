@extends('layouts.app')

@section('title', $listing->title)

@section('content')
<div class="container py-4" style="max-width:1200px" x-data="{ activePhoto: '{{ $listing->photos->first()?->file_path ?? '' }}' }">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="background:none;padding:0">
            <li class="breadcrumb-item"><a href="{{ route('marketplace') }}" style="color:var(--amber)">Marketplace</a></li>
            <li class="breadcrumb-item active" style="color:var(--muted)">{{ Str::limit($listing->title, 40) }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        {{-- Left: Gallery + Tabs --}}
        <div class="col-lg-8">
            {{-- Gallery --}}
            <div class="rounded-3 overflow-hidden mb-3" style="height:420px;background:var(--dark)">
                @if($listing->photos->count())
                <img :src="'/storage/' + activePhoto" class="w-100 h-100" style="object-fit:cover" alt="{{ $listing->title }}">
                @else
                <div class="d-flex align-items-center justify-content-center h-100" style="color:var(--muted)">
                    <i class="fas fa-image fa-4x"></i>
                </div>
                @endif
            </div>
            @if($listing->photos->count() > 1)
            <div class="d-flex gap-2 mb-4 overflow-auto">
                @foreach($listing->photos as $photo)
                <img src="{{ Storage::url($photo->file_path) }}" class="rounded-2 flex-shrink-0"
                    style="width:80px;height:60px;object-fit:cover;cursor:pointer;border:2px solid transparent"
                    :style="activePhoto === '{{ $photo->file_path }}' ? 'border-color:var(--amber)' : ''"
                    @click="activePhoto = '{{ $photo->file_path }}'" alt="">
                @endforeach
            </div>
            @endif

            {{-- Tabs --}}
            <ul class="nav nav-tabs mb-3" style="border-color:var(--border)" id="listingTabs">
                <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#overview" style="color:var(--text)">Overview</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#specs" style="color:var(--text)">Specs</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#location" style="color:var(--text)">Location</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#reviews" style="color:var(--text)">Reviews ({{ $reviews->count() }})</a></li>
            </ul>
            <div class="tab-content p-3 rounded-3" style="background:var(--surface);border:1px solid var(--border)">
                <div class="tab-pane fade show active" id="overview">
                    <p style="color:var(--text)">{{ $listing->description }}</p>
                    @if($listing->features)
                    <h6 style="color:var(--amber)" class="mt-3">Features</h6>
                    <ul class="list-unstyled row row-cols-2">
                        @foreach((array)$listing->features as $feature)
                        <li class="col mb-1" style="color:var(--text)"><i class="fas fa-check text-success me-2"></i>{{ $feature }}</li>
                        @endforeach
                    </ul>
                    @endif
                    <div class="row mt-3 g-2">
                        @if($listing->year)<div class="col-6 col-md-4"><span style="color:var(--muted);font-size:.8rem">YEAR</span><p style="color:var(--text)" class="mb-0">{{ $listing->year }}</p></div>@endif
                        @if($listing->make)<div class="col-6 col-md-4"><span style="color:var(--muted);font-size:.8rem">MAKE</span><p style="color:var(--text)" class="mb-0">{{ $listing->make }}</p></div>@endif
                        @if($listing->model)<div class="col-6 col-md-4"><span style="color:var(--muted);font-size:.8rem">MODEL</span><p style="color:var(--text)" class="mb-0">{{ $listing->model }}</p></div>@endif
                        @if($listing->condition ?? null)<div class="col-6 col-md-4"><span style="color:var(--muted);font-size:.8rem">CONDITION</span><p style="color:var(--text)" class="mb-0">{{ ucfirst($listing->condition) }}</p></div>@endif
                    </div>
                </div>

                <div class="tab-pane fade" id="specs">
                    <div class="row g-3">
                        @if($listing->drive_mode)<div class="col-6"><span style="color:var(--muted);font-size:.8rem">DRIVE MODE</span><p style="color:var(--text)" class="mb-0">{{ ucfirst($listing->drive_mode) }}</p></div>@endif
                        @if($listing->fuel_type)<div class="col-6"><span style="color:var(--muted);font-size:.8rem">FUEL TYPE</span><p style="color:var(--text)" class="mb-0">{{ ucfirst($listing->fuel_type) }}</p></div>@endif
                        @if($listing->transmission)<div class="col-6"><span style="color:var(--muted);font-size:.8rem">TRANSMISSION</span><p style="color:var(--text)" class="mb-0">{{ ucfirst($listing->transmission) }}</p></div>@endif
                        @if($listing->seats)<div class="col-6"><span style="color:var(--muted);font-size:.8rem">SEATS</span><p style="color:var(--text)" class="mb-0">{{ $listing->seats }}</p></div>@endif
                        @if($listing->load_capacity)<div class="col-6"><span style="color:var(--muted);font-size:.8rem">PAYLOAD</span><p style="color:var(--text)" class="mb-0">{{ $listing->load_capacity }}</p></div>@endif
                        <div class="col-6"><span style="color:var(--muted);font-size:.8rem">DRIVER INCLUDED</span><p style="color:var(--text)" class="mb-0">{{ $listing->drive_mode === 'self_drive' ? 'No' : 'Yes' }}</p></div>
                        @if($listing->security_deposit)<div class="col-6"><span style="color:var(--muted);font-size:.8rem">SECURITY DEPOSIT</span><p style="color:var(--text)" class="mb-0">KES {{ number_format($listing->security_deposit) }}</p></div>@endif
                    </div>
                </div>

                <div class="tab-pane fade" id="location">
                    @if($listing->yard)
                    <p style="color:var(--muted);font-size:.85rem">YARD</p>
                    <p style="color:var(--text);font-weight:600">{{ $listing->yard->name }}</p>
                    @endif
                    <div class="d-flex gap-4">
                        <div><span style="color:var(--muted);font-size:.8rem">COUNTY</span><p style="color:var(--text)" class="mb-0">{{ $listing->county }}</p></div>
                        @if($listing->city)<div><span style="color:var(--muted);font-size:.8rem">CITY / TOWN</span><p style="color:var(--text)" class="mb-0">{{ $listing->city }}</p></div>@endif
                    </div>
                </div>

                <div class="tab-pane fade" id="reviews">
                    @if($reviews->count())
                    @php $avgRating = $reviews->avg('overall_rating') @endphp
                    <div class="d-flex align-items-center gap-3 mb-4 p-3 rounded-3" style="background:var(--dark)">
                        <div class="text-center">
                            <div style="font-size:2.5rem;font-weight:700;color:var(--amber)">{{ number_format($avgRating, 1) }}</div>
                            <div style="color:var(--muted);font-size:.8rem">out of 5</div>
                        </div>
                        <div>
                            @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star" style="color:{{ $i <= round($avgRating) ? 'var(--amber)' : 'var(--border)' }}"></i>
                            @endfor
                            <div style="color:var(--muted);font-size:.85rem">{{ $reviews->count() }} review(s)</div>
                        </div>
                    </div>
                    @foreach($reviews as $review)
                    <div class="mb-4 pb-4" style="border-bottom:1px solid var(--border)">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;background:var(--amber);color:#000;font-weight:700;font-size:.9rem">
                                {{ strtoupper(substr($review->reviewer->name ?? 'A', 0, 1)) }}
                            </div>
                            <div>
                                <div style="color:var(--text);font-weight:600">{{ $review->reviewer->name ?? 'Anonymous' }}</div>
                                <div style="color:var(--muted);font-size:.78rem">{{ $review->created_at->diffForHumans() }}</div>
                            </div>
                            <div class="ms-auto">
                                @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star fa-sm" style="color:{{ $i <= $review->overall_rating ? 'var(--amber)' : 'var(--border)' }}"></i>
                                @endfor
                            </div>
                        </div>
                        <p style="color:var(--text);font-size:.9rem" class="mb-0">{{ $review->comment }}</p>
                    </div>
                    @endforeach
                    @else
                    <p style="color:var(--muted)">No reviews yet.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right: Owner info + Pricing card --}}
        <div class="col-lg-4">
            <div style="position:sticky;top:80px">
                {{-- Title & Owner --}}
                <div class="mb-3 p-3 rounded-3" style="background:var(--surface);border:1px solid var(--border)">
                    @if($listing->category)
                    <span class="badge mb-2" style="background:var(--amber);color:#000">{{ $listing->category->name }}</span>
                    @endif
                    <h4 style="color:var(--text);font-weight:700">{{ $listing->title }}</h4>
                    <p style="color:var(--muted);font-size:.875rem"><i class="fas fa-map-marker-alt me-1"></i>{{ $listing->city ? $listing->city . ', ' : '' }}{{ $listing->county }}</p>
                    @if($listing->average_rating > 0)
                    <div class="d-flex align-items-center gap-1 mb-2">
                        @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star fa-sm" style="color:{{ $i <= round($listing->average_rating) ? 'var(--amber)' : 'var(--border)' }}"></i>
                        @endfor
                        <span style="color:var(--muted);font-size:.8rem">({{ $listing->rating_count }})</span>
                    </div>
                    @endif
                    <div class="d-flex align-items-center gap-2 mt-3 pt-3" style="border-top:1px solid var(--border)">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;background:var(--amber);color:#000;font-weight:700">
                            {{ strtoupper(substr($listing->user->name ?? 'O', 0, 1)) }}
                        </div>
                        <div>
                            <div style="color:var(--text);font-weight:600">{{ $listing->user->name ?? 'Owner' }}</div>
                            <div style="color:var(--muted);font-size:.78rem">
                                @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star fa-xs" style="color:{{ $i <= round($listing->user->trust_score ?? 0) ? 'var(--amber)' : 'var(--border)' }}"></i>
                                @endfor
                                @if($listing->user->status === 'verified')
                                <span class="ms-2 badge" style="background:rgba(46,204,138,.15);color:var(--green);font-size:.7rem"><i class="fas fa-check-circle me-1"></i>Verified</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Pricing Card --}}
                <div class="p-3 rounded-3" style="background:var(--surface);border:1px solid var(--border)">
                    <h6 style="color:var(--amber);font-weight:600" class="mb-3">Pricing</h6>
                    @if($listing->listing_mode !== 'sale')
                    <div class="d-flex flex-column gap-2 mb-3">
                        @if($listing->hourly_rate)<div class="d-flex justify-content-between"><span style="color:var(--muted)">Hourly</span><span style="color:var(--text);font-weight:600">KES {{ number_format($listing->hourly_rate) }}</span></div>@endif
                        @if($listing->daily_rate)<div class="d-flex justify-content-between"><span style="color:var(--muted)">Daily</span><span style="color:var(--text);font-weight:600">KES {{ number_format($listing->daily_rate) }}</span></div>@endif
                        @if($listing->weekly_rate)<div class="d-flex justify-content-between"><span style="color:var(--muted)">Weekly</span><span style="color:var(--text);font-weight:600">KES {{ number_format($listing->weekly_rate) }}</span></div>@endif
                        @if($listing->monthly_rate)<div class="d-flex justify-content-between"><span style="color:var(--muted)">Monthly</span><span style="color:var(--text);font-weight:600">KES {{ number_format($listing->monthly_rate) }}</span></div>@endif
                    </div>
                    @auth
                    <form action="{{ route('bookings.create') }}" method="GET">
                        <input type="hidden" name="listing_id" value="{{ $listing->id }}">
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label style="color:var(--muted);font-size:.78rem">Start Date</label>
                                <input type="date" name="start_date" class="form-control form-control-sm" style="background:var(--dark);border-color:var(--border);color:var(--text)" min="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-6">
                                <label style="color:var(--muted);font-size:.78rem">End Date</label>
                                <input type="date" name="end_date" class="form-control form-control-sm" style="background:var(--dark);border-color:var(--border);color:var(--text)" min="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label style="color:var(--muted);font-size:.78rem">Duration Type</label>
                            <div class="d-flex gap-2 mt-1">
                                @foreach(['hourly','daily','weekly','monthly'] as $dur)
                                <div class="form-check form-check-inline mb-0">
                                    <input class="form-check-input" type="radio" name="duration_type" value="{{ $dur }}" id="dur_{{ $dur }}" {{ $dur === 'daily' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="dur_{{ $dur }}" style="color:var(--text);font-size:.8rem">{{ ucfirst($dur) }}</label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <button type="submit" class="btn w-100" style="background:var(--amber);color:#000;font-weight:700">Book Now</button>
                    </form>
                    @else
                    <a href="{{ route('login') }}" class="btn w-100" style="background:var(--amber);color:#000;font-weight:700">Login to Book</a>
                    @endauth
                    @endif

                    @if($listing->listing_mode !== 'rental')
                    @if($listing->sale_price)
                    <div class="mt-3 pt-3" style="border-top:1px solid var(--border)">
                        <div class="d-flex justify-content-between mb-2">
                            <span style="color:var(--muted)">Sale Price</span>
                            <span style="color:var(--amber);font-weight:700;font-size:1.1rem">KES {{ number_format($listing->sale_price) }}</span>
                        </div>
                        @auth
                        <a href="mailto:{{ $listing->user->email }}?subject=Enquiry: {{ $listing->title }}" class="btn w-100" style="border:1px solid var(--amber);color:var(--amber)">Enquire About Purchase</a>
                        @else
                        <a href="{{ route('login') }}" class="btn w-100" style="border:1px solid var(--amber);color:var(--amber)">Login to Enquire</a>
                        @endauth
                    </div>
                    @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
