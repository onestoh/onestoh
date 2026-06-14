@extends('layouts.admin')
@section('title', 'Platform Settings')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold">Platform Settings</h4>
        <small class="text-muted">Configure global platform parameters</small>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="fas fa-cog me-2 text-amber"></i>Fee & Booking Settings</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.settings.update') }}">
                    @csrf

                    @if($errors->any())
                    <div class="alert alert-danger mb-4">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    {{-- Platform Fee % --}}
                    <div class="mb-4">
                        <label for="platform_fee_percentage" class="form-label fw-semibold">
                            Platform Fee (%)
                            <span class="text-muted fw-normal" style="font-size:0.8rem;">— charged to the listing owner on each completed booking</span>
                        </label>
                        <div class="input-group">
                            <input type="number" id="platform_fee_percentage" name="platform_fee_percentage"
                                value="{{ old('platform_fee_percentage', $settings->get('platform_fee_percentage')?->value ?? 10) }}"
                                min="0" max="50" step="0.5" required
                                class="form-control @error('platform_fee_percentage') is-invalid @enderror"
                                style="background:var(--black);border-color:var(--border);color:var(--text);">
                            <span class="input-group-text" style="background:var(--surface);border-color:var(--border);color:var(--muted);">%</span>
                        </div>
                        @error('platform_fee_percentage')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                        <div class="text-muted mt-1" style="font-size:0.78rem;">Current: <strong>{{ $settings->get('platform_fee_percentage')?->value ?? '10' }}%</strong></div>
                    </div>

                    {{-- Security Deposit % --}}
                    <div class="mb-4">
                        <label for="security_deposit_percentage" class="form-label fw-semibold">
                            Security Deposit (%)
                            <span class="text-muted fw-normal" style="font-size:0.8rem;">— percentage of booking value held as deposit</span>
                        </label>
                        <div class="input-group">
                            <input type="number" id="security_deposit_percentage" name="security_deposit_percentage"
                                value="{{ old('security_deposit_percentage', $settings->get('security_deposit_percentage')?->value ?? 20) }}"
                                min="0" max="100" step="1" required
                                class="form-control @error('security_deposit_percentage') is-invalid @enderror"
                                style="background:var(--black);border-color:var(--border);color:var(--text);">
                            <span class="input-group-text" style="background:var(--surface);border-color:var(--border);color:var(--muted);">%</span>
                        </div>
                        @error('security_deposit_percentage')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                        <div class="text-muted mt-1" style="font-size:0.78rem;">Current: <strong>{{ $settings->get('security_deposit_percentage')?->value ?? '20' }}%</strong></div>
                    </div>

                    {{-- Min Booking Hours --}}
                    <div class="mb-4">
                        <label for="min_booking_hours" class="form-label fw-semibold">
                            Minimum Booking Hours
                            <span class="text-muted fw-normal" style="font-size:0.8rem;">— minimum rental duration in hours</span>
                        </label>
                        <div class="input-group">
                            <input type="number" id="min_booking_hours" name="min_booking_hours"
                                value="{{ old('min_booking_hours', $settings->get('min_booking_hours')?->value ?? 4) }}"
                                min="1" step="1" required
                                class="form-control @error('min_booking_hours') is-invalid @enderror"
                                style="background:var(--black);border-color:var(--border);color:var(--text);">
                            <span class="input-group-text" style="background:var(--surface);border-color:var(--border);color:var(--muted);">hrs</span>
                        </div>
                        @error('min_booking_hours')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                        <div class="text-muted mt-1" style="font-size:0.78rem;">Current: <strong>{{ $settings->get('min_booking_hours')?->value ?? '4' }} hours</strong></div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-amber px-5">
                            <i class="fas fa-save me-2"></i>Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
