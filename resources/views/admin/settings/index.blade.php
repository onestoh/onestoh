@extends('layouts.admin')
@section('title', 'Platform Settings')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold"><i class="fas fa-cog me-2 text-amber"></i>Platform Settings</h4>
        <small class="text-muted">Configure all global platform parameters</small>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-4">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show mb-4">
    <ul class="mb-0 ps-3">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Tab Navigation --}}
<ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist" style="border-bottom-color: var(--border);">
    @foreach([
        ['id'=>'company',      'icon'=>'fa-building',      'label'=>'Company'],
        ['id'=>'legal',        'icon'=>'fa-file-contract', 'label'=>'Legal'],
        ['id'=>'theme',        'icon'=>'fa-palette',       'label'=>'Theme'],
        ['id'=>'locale',       'icon'=>'fa-globe',         'label'=>'Locale'],
        ['id'=>'billing',      'icon'=>'fa-credit-card',   'label'=>'Billing'],
        ['id'=>'notifications','icon'=>'fa-envelope',      'label'=>'Notifications'],
        ['id'=>'social',       'icon'=>'fa-share-alt',     'label'=>'Social'],
        ['id'=>'security',     'icon'=>'fa-shield-alt',    'label'=>'Security'],
    ] as $tab)
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ $loop->first ? 'active' : '' }}"
            id="{{ $tab['id'] }}-tab"
            data-bs-toggle="tab"
            data-bs-target="#tab-{{ $tab['id'] }}"
            type="button" role="tab"
            style="color:var(--muted); border-color: transparent;">
            <i class="fas {{ $tab['icon'] }} me-1"></i>{{ $tab['label'] }}
        </button>
    </li>
    @endforeach
</ul>

<style>
.nav-tabs .nav-link.active { background: var(--surface); border-color: var(--border) var(--border) var(--surface) !important; color: var(--amber) !important; }
.nav-tabs .nav-link:hover { color: var(--amber) !important; }
.form-control, .form-select { background: var(--black) !important; border-color: var(--border) !important; color: var(--text) !important; }
.form-control:focus, .form-select:focus { border-color: var(--amber) !important; box-shadow: 0 0 0 0.2rem rgba(232,146,42,0.15) !important; }
.form-check-input:checked { background-color: var(--amber); border-color: var(--amber); }
.input-group-text { background: var(--surface) !important; border-color: var(--border) !important; color: var(--muted) !important; }
</style>

<div class="tab-content" id="settingsTabContent">

    {{-- ======================== COMPANY TAB ======================== --}}
    <div class="tab-pane fade show active" id="tab-company" role="tabpanel">
        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="tab" value="company">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header"><h6 class="mb-0 fw-bold"><i class="fas fa-building me-2 text-amber"></i>Company Information</h6></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Company Name <span class="text-danger">*</span></label>
                                    <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $s['company_name'] ?? 'TheOnlineYard') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Tagline</label>
                                    <input type="text" name="company_tagline" class="form-control" value="{{ old('company_tagline', $s['company_tagline'] ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="company_email" class="form-control" value="{{ old('company_email', $s['company_email'] ?? '') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Phone</label>
                                    <input type="text" name="company_phone" class="form-control" value="{{ old('company_phone', $s['company_phone'] ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Address</label>
                                    <input type="text" name="company_address" class="form-control" value="{{ old('company_address', $s['company_address'] ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Website</label>
                                    <input type="url" name="company_website" class="form-control" value="{{ old('company_website', $s['company_website'] ?? '') }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Mission Statement</label>
                                    <textarea name="company_mission" class="form-control" rows="3">{{ old('company_mission', $s['company_mission'] ?? '') }}</textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Vision Statement</label>
                                    <textarea name="company_vision" class="form-control" rows="3">{{ old('company_vision', $s['company_vision'] ?? '') }}</textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Letterhead Footer</label>
                                    <textarea name="letterhead_footer" class="form-control" rows="2">{{ old('letterhead_footer', $s['letterhead_footer'] ?? '') }}</textarea>
                                    <small class="text-muted">Used on invoices, receipts, and PDF documents.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card mb-3">
                        <div class="card-header"><h6 class="mb-0 fw-bold"><i class="fas fa-image me-2 text-amber"></i>Logo & Favicon</h6></div>
                        <div class="card-body">
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Company Logo</label>
                                @if(!empty($s['company_logo']))
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $s['company_logo']) }}" alt="Logo" style="max-height:60px;max-width:100%;border:1px solid var(--border);border-radius:6px;padding:4px;background:var(--surface);">
                                    </div>
                                @endif
                                <input type="file" name="company_logo" class="form-control" accept="image/*">
                                <small class="text-muted">Max 2MB. PNG recommended.</small>
                            </div>
                            <div>
                                <label class="form-label fw-semibold">Favicon</label>
                                @if(!empty($s['company_favicon']))
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $s['company_favicon']) }}" alt="Favicon" style="max-height:32px;border:1px solid var(--border);border-radius:4px;padding:2px;background:var(--surface);">
                                    </div>
                                @endif
                                <input type="file" name="company_favicon" class="form-control" accept="image/*">
                                <small class="text-muted">Max 512KB. ICO or PNG, 32x32px.</small>
                            </div>
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-amber py-2">
                            <i class="fas fa-save me-2"></i>Save Company Settings
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- ======================== LEGAL TAB ======================== --}}
    <div class="tab-pane fade" id="tab-legal" role="tabpanel">
        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            <input type="hidden" name="tab" value="legal">
            <div class="card">
                <div class="card-header"><h6 class="mb-0 fw-bold"><i class="fas fa-file-contract me-2 text-amber"></i>Legal Documents</h6></div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Terms & Conditions</label>
                        <textarea name="terms_and_conditions" class="form-control" rows="12">{{ old('terms_and_conditions', $s['terms_and_conditions'] ?? '') }}</textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Privacy Policy</label>
                        <textarea name="privacy_policy" class="form-control" rows="12">{{ old('privacy_policy', $s['privacy_policy'] ?? '') }}</textarea>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-amber px-5">
                            <i class="fas fa-save me-2"></i>Save Legal Documents
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- ======================== THEME TAB ======================== --}}
    <div class="tab-pane fade" id="tab-theme" role="tabpanel">
        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            <input type="hidden" name="tab" value="theme">
            <div class="row g-4" x-data="{
                primaryColor: '{{ $s['primary_color'] ?? '#E8922A' }}',
                secondaryColor: '{{ $s['secondary_color'] ?? '#2ECC8A' }}',
                gradientStart: '{{ $s['gradient_start'] ?? '#E8922A' }}',
                gradientEnd: '{{ $s['gradient_end'] ?? '#E84040' }}',
                darkMode: {{ ($s['dark_mode'] ?? 'true') === 'true' ? 'true' : 'false' }}
            }">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header"><h6 class="mb-0 fw-bold"><i class="fas fa-palette me-2 text-amber"></i>Brand Colors</h6></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Primary Color (Amber)</label>
                                    <div class="input-group">
                                        <input type="color" name="primary_color" class="form-control form-control-color" x-model="primaryColor" style="width:60px;padding:2px;">
                                        <input type="text" class="form-control" x-model="primaryColor" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Secondary Color (Green)</label>
                                    <div class="input-group">
                                        <input type="color" name="secondary_color" class="form-control form-control-color" x-model="secondaryColor" style="width:60px;padding:2px;">
                                        <input type="text" class="form-control" x-model="secondaryColor" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Gradient Start</label>
                                    <div class="input-group">
                                        <input type="color" name="gradient_start" class="form-control form-control-color" x-model="gradientStart" style="width:60px;padding:2px;">
                                        <input type="text" class="form-control" x-model="gradientStart" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Gradient End</label>
                                    <div class="input-group">
                                        <input type="color" name="gradient_end" class="form-control form-control-color" x-model="gradientEnd" style="width:60px;padding:2px;">
                                        <input type="text" class="form-control" x-model="gradientEnd" readonly>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="dark_mode" id="dark_mode" x-model="darkMode">
                                        <label class="form-check-label fw-semibold" for="dark_mode">Enable Dark Mode</label>
                                    </div>
                                    <small class="text-muted">Dark mode is the default theme for TheOnlineYard.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header"><h6 class="mb-0 fw-bold"><i class="fas fa-eye me-2 text-amber"></i>Live Preview</h6></div>
                        <div class="card-body">
                            <div :style="'padding:1.5rem;border-radius:12px;background: linear-gradient(135deg,' + gradientStart + ',' + gradientEnd + ');margin-bottom:1rem;'">
                                <h5 style="color:#fff;margin:0;font-weight:800;">TheOnlineYard</h5>
                                <p style="color:rgba(255,255,255,0.85);margin:0;font-size:0.85rem;">Gradient Banner Preview</p>
                            </div>
                            <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
                                <span :style="'padding:6px 16px;border-radius:20px;background:' + primaryColor + ';color:#000;font-weight:700;font-size:0.85rem;'">Primary Button</span>
                                <span :style="'padding:6px 16px;border-radius:20px;background:' + secondaryColor + ';color:#000;font-weight:700;font-size:0.85rem;'">Secondary</span>
                                <span :style="'padding:6px 16px;border-radius:20px;border:2px solid ' + primaryColor + ';color:' + primaryColor + ';font-weight:700;font-size:0.85rem;'">Outline</span>
                            </div>
                            <div class="mt-3 p-3 rounded" :style="'background:#0B1018;border:1px solid #1E2D42;'">
                                <span :style="'color:' + primaryColor + ';font-weight:800;'">KES 5,000</span>
                                <span style="color:#7088A8;font-size:0.85rem;margin-left:8px;">/day</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-amber px-5">
                            <i class="fas fa-save me-2"></i>Save Theme Settings
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- ======================== LOCALE TAB ======================== --}}
    <div class="tab-pane fade" id="tab-locale" role="tabpanel">
        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            <input type="hidden" name="tab" value="locale">
            <div class="card">
                <div class="card-header"><h6 class="mb-0 fw-bold"><i class="fas fa-globe me-2 text-amber"></i>Locale & Region</h6></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Default Language</label>
                            <select name="default_language" class="form-select">
                                @foreach(['en'=>'English','sw'=>'Swahili','fr'=>'French'] as $code => $lang)
                                    <option value="{{ $code }}" {{ ($s['default_language'] ?? 'en') === $code ? 'selected' : '' }}>{{ $lang }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Default Country</label>
                            <select name="default_country" class="form-select">
                                @foreach(['KE'=>'Kenya','UG'=>'Uganda','TZ'=>'Tanzania','RW'=>'Rwanda','ET'=>'Ethiopia','NG'=>'Nigeria','ZA'=>'South Africa'] as $code => $country)
                                    <option value="{{ $code }}" {{ ($s['default_country'] ?? 'KE') === $code ? 'selected' : '' }}>{{ $country }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Default Currency</label>
                            <select name="default_currency" class="form-select">
                                @foreach(['KES'=>'KES — Kenyan Shilling','UGX'=>'UGX — Ugandan Shilling','TZS'=>'TZS — Tanzanian Shilling','NGN'=>'NGN — Nigerian Naira','ZAR'=>'ZAR — South African Rand'] as $code => $label)
                                    <option value="{{ $code }}" {{ ($s['default_currency'] ?? 'KES') === $code ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Timezone</label>
                            <select name="timezone" class="form-select">
                                @foreach(['Africa/Nairobi'=>'Africa/Nairobi (EAT +3)','Africa/Kampala'=>'Africa/Kampala (EAT +3)','Africa/Dar_es_Salaam'=>'Africa/Dar es Salaam (EAT +3)','Africa/Lagos'=>'Africa/Lagos (WAT +1)','Africa/Johannesburg'=>'Africa/Johannesburg (SAST +2)'] as $tz => $label)
                                    <option value="{{ $tz }}" {{ ($s['timezone'] ?? 'Africa/Nairobi') === $tz ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-amber px-5">
                            <i class="fas fa-save me-2"></i>Save Locale Settings
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- ======================== BILLING TAB ======================== --}}
    <div class="tab-pane fade" id="tab-billing" role="tabpanel">
        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            <input type="hidden" name="tab" value="billing">
            <div class="card">
                <div class="card-header"><h6 class="mb-0 fw-bold"><i class="fas fa-credit-card me-2 text-amber"></i>Billing & Payments</h6></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Platform Fee (%)</label>
                            <div class="input-group">
                                <input type="number" name="platform_fee_percentage" class="form-control"
                                    value="{{ old('platform_fee_percentage', $s['platform_fee_percentage'] ?? '10') }}"
                                    min="0" max="50" step="0.5" required>
                                <span class="input-group-text">%</span>
                            </div>
                            <small class="text-muted">Charged to listing owner on completed bookings.</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Security Deposit (%)</label>
                            <div class="input-group">
                                <input type="number" name="security_deposit_percentage" class="form-control"
                                    value="{{ old('security_deposit_percentage', $s['security_deposit_percentage'] ?? '20') }}"
                                    min="0" max="100" step="1" required>
                                <span class="input-group-text">%</span>
                            </div>
                            <small class="text-muted">Percentage of booking value held as deposit.</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Min Booking Hours</label>
                            <div class="input-group">
                                <input type="number" name="min_booking_hours" class="form-control"
                                    value="{{ old('min_booking_hours', $s['min_booking_hours'] ?? '4') }}"
                                    min="1" step="1" required>
                                <span class="input-group-text">hrs</span>
                            </div>
                            <small class="text-muted">Minimum rental duration in hours.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">M-Pesa Environment</label>
                            <select name="mpesa_env" class="form-select">
                                <option value="sandbox" {{ ($s['mpesa_env'] ?? 'sandbox') === 'sandbox' ? 'selected' : '' }}>Sandbox (Testing)</option>
                                <option value="production" {{ ($s['mpesa_env'] ?? '') === 'production' ? 'selected' : '' }}>Production (Live)</option>
                            </select>
                            <small class="text-muted">Switch to production when ready to go live.</small>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-amber px-5">
                            <i class="fas fa-save me-2"></i>Save Billing Settings
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- ======================== NOTIFICATIONS TAB ======================== --}}
    <div class="tab-pane fade" id="tab-notifications" role="tabpanel">
        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            <input type="hidden" name="tab" value="notifications">
            <div class="card">
                <div class="card-header"><h6 class="mb-0 fw-bold"><i class="fas fa-envelope me-2 text-amber"></i>Email & Notification Settings</h6></div>
                <div class="card-body">
                    <h6 class="text-muted text-uppercase mb-3" style="font-size:0.75rem;letter-spacing:1px;">SMTP Configuration</h6>
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">SMTP Host</label>
                            <input type="text" name="smtp_host" class="form-control" value="{{ old('smtp_host', $s['smtp_host'] ?? '') }}" placeholder="smtp.gmail.com">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">SMTP Port</label>
                            <input type="text" name="smtp_port" class="form-control" value="{{ old('smtp_port', $s['smtp_port'] ?? '587') }}" placeholder="587">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">SMTP Username</label>
                            <input type="text" name="smtp_username" class="form-control" value="{{ old('smtp_username', $s['smtp_username'] ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">From Name</label>
                            <input type="text" name="smtp_from_name" class="form-control" value="{{ old('smtp_from_name', $s['smtp_from_name'] ?? 'TheOnlineYard') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">From Email</label>
                            <input type="email" name="smtp_from_email" class="form-control" value="{{ old('smtp_from_email', $s['smtp_from_email'] ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Support WhatsApp</label>
                            <input type="text" name="support_whatsapp" class="form-control" value="{{ old('support_whatsapp', $s['support_whatsapp'] ?? '') }}" placeholder="+254700000000">
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-amber px-5">
                            <i class="fas fa-save me-2"></i>Save Notification Settings
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- ======================== SOCIAL TAB ======================== --}}
    <div class="tab-pane fade" id="tab-social" role="tabpanel">
        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            <input type="hidden" name="tab" value="social">
            <div class="card">
                <div class="card-header"><h6 class="mb-0 fw-bold"><i class="fas fa-share-alt me-2 text-amber"></i>Social Media Links</h6></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold"><i class="fab fa-facebook me-1" style="color:#1877F2;"></i>Facebook URL</label>
                            <input type="url" name="facebook_url" class="form-control" value="{{ old('facebook_url', $s['facebook_url'] ?? '') }}" placeholder="https://facebook.com/theonlineyard">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold"><i class="fab fa-twitter me-1" style="color:#1DA1F2;"></i>Twitter / X URL</label>
                            <input type="url" name="twitter_url" class="form-control" value="{{ old('twitter_url', $s['twitter_url'] ?? '') }}" placeholder="https://twitter.com/theonlineyard">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold"><i class="fab fa-instagram me-1" style="color:#E1306C;"></i>Instagram URL</label>
                            <input type="url" name="instagram_url" class="form-control" value="{{ old('instagram_url', $s['instagram_url'] ?? '') }}" placeholder="https://instagram.com/theonlineyard">
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-amber px-5">
                            <i class="fas fa-save me-2"></i>Save Social Links
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- ======================== SECURITY TAB ======================== --}}
    <div class="tab-pane fade" id="tab-security" role="tabpanel">
        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            <input type="hidden" name="tab" value="security">
            <div class="card">
                <div class="card-header"><h6 class="mb-0 fw-bold"><i class="fas fa-shield-alt me-2 text-amber"></i>Security & MFA</h6></div>
                <div class="card-body">
                    <div class="mb-4 p-3 rounded" style="background:rgba(232,146,42,0.07);border:1px solid rgba(232,146,42,0.2);">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="mfa_enabled" id="mfa_enabled"
                                {{ ($s['mfa_enabled'] ?? 'true') === 'true' ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="mfa_enabled">
                                Enable Multi-Factor Authentication (MFA)
                            </label>
                        </div>
                        <p class="text-muted small mt-1 mb-0">When enabled, all users must verify their identity with a one-time code after entering their password.</p>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">MFA Delivery Method</label>
                        <div class="d-flex gap-4 mt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="mfa_method" id="mfa_email" value="email"
                                    {{ ($s['mfa_method'] ?? 'email') === 'email' ? 'checked' : '' }}>
                                <label class="form-check-label" for="mfa_email">
                                    <i class="fas fa-envelope me-1 text-amber"></i>Email OTP
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="mfa_method" id="mfa_sms" value="sms"
                                    {{ ($s['mfa_method'] ?? '') === 'sms' ? 'checked' : '' }}>
                                <label class="form-check-label" for="mfa_sms">
                                    <i class="fas fa-sms me-1 text-amber"></i>SMS OTP
                                    <span class="badge ms-1" style="background:rgba(232,146,42,0.15);color:var(--amber);font-size:0.65rem;">Requires SMS gateway</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="alert" style="background:rgba(46,204,138,0.08);border:1px solid rgba(46,204,138,0.2);color:var(--green);">
                        <i class="fas fa-info-circle me-2"></i>
                        MFA codes expire in <strong>10 minutes</strong>. Users can request a new code from the verification page.
                    </div>

                    <div class="d-flex justify-content-end mt-2">
                        <button type="submit" class="btn btn-amber px-5">
                            <i class="fas fa-save me-2"></i>Save Security Settings
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

</div>

@push('scripts')
<script>
// Preserve active tab on page reload after save
document.addEventListener('DOMContentLoaded', function() {
    const activeTab = sessionStorage.getItem('settingsActiveTab');
    if (activeTab) {
        const tab = document.querySelector(`[data-bs-target="#tab-${activeTab}"]`);
        if (tab) { new bootstrap.Tab(tab).show(); }
    }
    document.querySelectorAll('[data-bs-toggle="tab"]').forEach(function(el) {
        el.addEventListener('shown.bs.tab', function(e) {
            const id = e.target.getAttribute('data-bs-target').replace('#tab-', '');
            sessionStorage.setItem('settingsActiveTab', id);
        });
    });
});
</script>
@endpush
@endsection
