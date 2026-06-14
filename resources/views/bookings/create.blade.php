@extends('layouts.dashboard')

@section('title', 'Book Listing')
@section('page-title', 'Book a Listing')

@push('styles')
<style>
.info-card { background: var(--black); border: 1px solid var(--border); border-radius: 10px; padding: 1.25rem; }
.form-control, .form-select { background: var(--surface); border: 1px solid var(--border); color: var(--text); }
.form-control:focus, .form-select:focus { background: var(--surface); border-color: var(--amber); color: var(--text); box-shadow: 0 0 0 2px rgba(232,146,42,.15); }
.form-label { color: var(--muted); font-size: .82rem; margin-bottom: .3rem; }
.cost-row { display: flex; justify-content: space-between; padding: .4rem 0; border-bottom: 1px solid var(--border); font-size: .875rem; }
.cost-row:last-child { border: none; font-weight: 700; }
</style>
@endpush

@section('content')
<div class="row g-4 justify-content-center">
    <div class="col-lg-7">
        {{-- Listing summary --}}
        <div class="info-card mb-4">
            <div class="d-flex gap-3">
                @if($listing->photos->count())
                <img src="{{ Storage::url($listing->photos->first()->file_path) }}"
                    style="width:90px;height:70px;object-fit:cover;border-radius:8px;flex-shrink:0" alt="">
                @else
                <div style="width:90px;height:70px;background:var(--surface);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i class="fas fa-car text-amber fa-2x"></i>
                </div>
                @endif
                <div>
                    <div class="fw-semibold" style="color:var(--text)">{{ $listing->title }}</div>
                    <div style="color:var(--muted);font-size:.82rem"><i class="fas fa-map-marker-alt me-1"></i>{{ $listing->county }}</div>
                    <div class="mt-1" style="font-size:.82rem;color:var(--amber)">
                        @if($listing->daily_rate) KES {{ number_format($listing->daily_rate) }}/day @endif
                        @if($listing->hourly_rate) &nbsp;· KES {{ number_format($listing->hourly_rate) }}/hr @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="info-card" x-data="{
            durationType: '{{ $durationType }}',
            startDate: '{{ $startDate }}',
            endDate: '{{ $endDate }}',
            dailyRate: {{ $listing->daily_rate ?? 0 }},
            hourlyRate: {{ $listing->hourly_rate ?? 0 }},
            weeklyRate: {{ $listing->weekly_rate ?? 0 }},
            monthlyRate: {{ $listing->monthly_rate ?? 0 }},
            securityDeposit: {{ $listing->security_deposit ?? 0 }},
            platformFeeRate: 0.10,
            get baseAmount() {
                if (!this.startDate || !this.endDate) return 0;
                const start = new Date(this.startDate), end = new Date(this.endDate);
                const diffMs = end - start;
                if (diffMs <= 0) return 0;
                const hours = diffMs / 3600000;
                const days = diffMs / 86400000;
                if (this.durationType === 'hourly') return Math.ceil(hours) * this.hourlyRate;
                if (this.durationType === 'weekly') return Math.ceil(days / 7) * this.weeklyRate;
                if (this.durationType === 'monthly') return Math.ceil(days / 30) * this.monthlyRate;
                return Math.ceil(days) * this.dailyRate;
            },
            get platformFee() { return Math.round(this.baseAmount * this.platformFeeRate); },
            get total() { return this.baseAmount + this.platformFee + this.securityDeposit; }
        }">
            <h6 class="mb-3" style="color:var(--amber);font-size:.75rem;text-transform:uppercase;letter-spacing:1px">Booking Details</h6>

            <form method="POST" action="{{ route('bookings.store') }}">
                @csrf
                <input type="hidden" name="listing_id" value="{{ $listing->id }}">

                <div class="mb-3">
                    <label class="form-label">Duration Type</label>
                    <div class="d-flex gap-2 flex-wrap">
                        @if($listing->hourly_rate)
                        <label class="d-flex align-items-center gap-2 px-3 py-2 rounded-2 cursor-pointer" style="border:1px solid var(--border);cursor:pointer" :style="durationType==='hourly' ? 'border-color:var(--amber);background:rgba(232,146,42,.1)' : ''">
                            <input type="radio" name="duration_type" value="hourly" x-model="durationType" style="display:none">
                            <span style="font-size:.85rem;" :style="durationType==='hourly' ? 'color:var(--amber)' : 'color:var(--muted)'">Hourly</span>
                        </label>
                        @endif
                        @if($listing->daily_rate)
                        <label class="d-flex align-items-center gap-2 px-3 py-2 rounded-2" style="border:1px solid var(--border);cursor:pointer" :style="durationType==='daily' ? 'border-color:var(--amber);background:rgba(232,146,42,.1)' : ''">
                            <input type="radio" name="duration_type" value="daily" x-model="durationType" style="display:none">
                            <span style="font-size:.85rem;" :style="durationType==='daily' ? 'color:var(--amber)' : 'color:var(--muted)'">Daily</span>
                        </label>
                        @endif
                        @if($listing->weekly_rate)
                        <label class="d-flex align-items-center gap-2 px-3 py-2 rounded-2" style="border:1px solid var(--border);cursor:pointer" :style="durationType==='weekly' ? 'border-color:var(--amber);background:rgba(232,146,42,.1)' : ''">
                            <input type="radio" name="duration_type" value="weekly" x-model="durationType" style="display:none">
                            <span style="font-size:.85rem;" :style="durationType==='weekly' ? 'color:var(--amber)' : 'color:var(--muted)'">Weekly</span>
                        </label>
                        @endif
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Start Date & Time</label>
                        <input type="datetime-local" name="start_datetime" class="form-control @error('start_datetime') is-invalid @enderror"
                            x-model="startDate" value="{{ old('start_datetime', $startDate ? $startDate.'T08:00' : '') }}"
                            min="{{ now()->addDay()->format('Y-m-d') }}T00:00" required>
                        @error('start_datetime')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">End Date & Time</label>
                        <input type="datetime-local" name="end_datetime" class="form-control @error('end_datetime') is-invalid @enderror"
                            x-model="endDate" value="{{ old('end_datetime', $endDate ? $endDate.'T17:00' : '') }}"
                            min="{{ now()->addDays(2)->format('Y-m-d') }}T00:00" required>
                        @error('end_datetime')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Special Notes / Requirements</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Any special requirements, pickup location details…">{{ old('notes') }}</textarea>
                </div>

                {{-- Cost breakdown --}}
                <div class="p-3 rounded-3 mb-4" style="background:var(--surface)">
                    <div class="cost-row"><span style="color:var(--muted)">Base Amount</span><span style="color:var(--text)">KES <span x-text="baseAmount.toLocaleString()">0</span></span></div>
                    <div class="cost-row"><span style="color:var(--muted)">Platform Fee (10%)</span><span style="color:var(--text)">KES <span x-text="platformFee.toLocaleString()">0</span></span></div>
                    @if($listing->security_deposit)
                    <div class="cost-row"><span style="color:var(--muted)">Security Deposit</span><span style="color:var(--text)">KES {{ number_format($listing->security_deposit) }}</span></div>
                    @endif
                    <div class="cost-row mt-1 pt-1" style="border-top:1px solid var(--border)">
                        <span style="color:var(--text);font-weight:700">Total Due</span>
                        <span style="color:var(--amber);font-size:1.1rem;font-weight:700">KES <span x-text="total.toLocaleString()">0</span></span>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 mb-3 p-2 rounded-2" style="background:rgba(232,146,42,.08);border:1px solid rgba(232,146,42,.2);font-size:.8rem;color:var(--amber)">
                    <i class="fas fa-clock"></i> Your slot will be held for <strong>15 minutes</strong> after booking — complete payment to confirm.
                </div>

                <button type="submit" class="btn btn-amber w-100 py-2 fw-semibold">
                    <i class="fas fa-calendar-check me-2"></i>Confirm Booking
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
