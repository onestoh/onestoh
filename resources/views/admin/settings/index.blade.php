@extends('layouts.admin')
@section('title', 'Platform Settings')

@push('styles')
<style>
.nav-tabs { border-bottom: 1px solid var(--border); }
.nav-tabs .nav-link { color: var(--muted); border: none; border-bottom: 2px solid transparent; padding: .6rem 1rem; font-size: .85rem; border-radius: 0; }
.nav-tabs .nav-link.active { color: var(--amber); border-bottom-color: var(--amber); background: transparent; }
.nav-tabs .nav-link:hover { color: var(--text); background: transparent; }
.form-control, .form-select, textarea { background: var(--black) !important; border: 1px solid var(--border) !important; color: var(--text) !important; font-size: .875rem; }
.form-control:focus, .form-select:focus, textarea:focus { border-color: var(--amber) !important; box-shadow: 0 0 0 3px rgba(232,146,42,.12) !important; }
.form-label { color: var(--muted); font-size: .78rem; text-transform: uppercase; letter-spacing: .06em; margin-bottom: .4rem; }
input[type="color"] { width: 38px; height: 38px; padding: 2px; border-radius: 6px; border: 1px solid var(--border); background: var(--surface); cursor: pointer; }
.section-title { font-size: .72rem; text-transform: uppercase; letter-spacing: .1em; color: var(--muted); margin-bottom: 1rem; padding-bottom: .5rem; border-bottom: 1px solid var(--border); }
.form-switch .form-check-input { background-color: var(--border); border-color: var(--border); width: 2.5em; height: 1.3em; }
.form-switch .form-check-input:checked { background-color: var(--amber); border-color: var(--amber); }
.logo-preview { width: 80px; height: 80px; object-fit: contain; border-radius: 8px; background: var(--surface); border: 1px solid var(--border); }
.input-group-text { background: var(--surface) !important; border-color: var(--border) !important; color: var(--muted) !important; }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold">Platform Settings</h4>
        <small style="color:var(--muted)">Configure all aspects of TheOnlineYard</small>
    </div>
</div>

@if(session('success'))
<div class="alert mb-4" style="background:rgba(46,204,138,.1);border:1px solid rgba(46,204,138,.3);color:var(--green);border-radius:10px">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
</div>
@endif

<div class="card">
    <div class="card-header p-0">
        <ul class="nav nav-tabs px-3 flex-nowrap overflow-auto" id="settingsTabs">
            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#company"><i class="fas fa-building me-1"></i>Company</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#legal"><i class="fas fa-file-contract me-1"></i>Legal</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#theme"><i class="fas fa-palette me-1"></i>Theme</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#locale"><i class="fas fa-globe me-1"></i>Locale</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#billing"><i class="fas fa-coins me-1"></i>Billing</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#notifications"><i class="fas fa-envelope me-1"></i>Email</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#social"><i class="fas fa-share-alt me-1"></i>Social</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#security"><i class="fas fa-shield-alt me-1"></i>Security</a></li>
        </ul>
    </div>
    <div class="card-body tab-content p-4">

        {{-- COMPANY TAB --}}
        <div class="tab-pane fade show active" id="company">
            <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="tab" value="company">
                <div class="section-title">Brand Identity</div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Company Name *</label>
                        <input type="text" name="company_name" class="form-control" value="{{ $s->get('company_name','TheOnlineYard') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tagline</label>
                        <input type="text" name="company_tagline" class="form-control" value="{{ $s->get('company_tagline') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Company Logo</label>
                        @if($s->get('company_logo'))
                        <div class="mb-2"><img src="{{ Storage::url($s->get('company_logo')) }}" class="logo-preview" alt="Logo"></div>
                        @endif
                        <input type="file" name="company_logo" class="form-control" accept="image/*">
                        <small style="color:var(--muted);font-size:.75rem">PNG/SVG recommended, max 2MB</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Favicon</label>
                        @if($s->get('company_favicon'))
                        <div class="mb-2"><img src="{{ Storage::url($s->get('company_favicon')) }}" style="width:32px;height:32px;object-fit:contain" alt="Favicon"></div>
                        @endif
                        <input type="file" name="company_favicon" class="form-control" accept="image/*">
                        <small style="color:var(--muted);font-size:.75rem">ICO/PNG 32×32px, max 512KB</small>
                    </div>
                </div>
                <div class="section-title">Contact Information</div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Company Email *</label>
                        <input type="email" name="company_email" class="form-control" value="{{ $s->get('company_email') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="company_phone" class="form-control" value="{{ $s->get('company_phone') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Physical Address</label>
                        <input type="text" name="company_address" class="form-control" value="{{ $s->get('company_address') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Website URL</label>
                        <input type="url" name="company_website" class="form-control" value="{{ $s->get('company_website') }}">
                    </div>
                </div>
                <div class="section-title">About the Company</div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Mission Statement</label>
                        <textarea name="company_mission" class="form-control" rows="4">{{ $s->get('company_mission') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Vision Statement</label>
                        <textarea name="company_vision" class="form-control" rows="4">{{ $s->get('company_vision') }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Letterhead Footer Text</label>
                        <input type="text" name="letterhead_footer" class="form-control" value="{{ $s->get('letterhead_footer') }}" placeholder="TheOnlineYard Ltd | Nairobi, Kenya | info@theonlineyard.co.ke">
                        <small style="color:var(--muted);font-size:.75rem">Appears on invoices, receipts and official documents</small>
                    </div>
                </div>
                <button type="submit" class="btn btn-amber px-4"><i class="fas fa-save me-2"></i>Save Company Settings</button>
            </form>
        </div>

        {{-- LEGAL TAB --}}
        <div class="tab-pane fade" id="legal">
            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf
                <input type="hidden" name="tab" value="legal">
                <div class="section-title">Terms & Conditions</div>
                <div class="mb-4">
                    <textarea name="terms_and_conditions" class="form-control" rows="14">{{ $s->get('terms_and_conditions') }}</textarea>
                    <small style="color:var(--muted);font-size:.75rem">Shown at registration and available at /terms</small>
                </div>
                <div class="section-title">Privacy Policy</div>
                <div class="mb-4">
                    <textarea name="privacy_policy" class="form-control" rows="14">{{ $s->get('privacy_policy') }}</textarea>
                    <small style="color:var(--muted);font-size:.75rem">Shown at registration and available at /privacy</small>
                </div>
                <button type="submit" class="btn btn-amber px-4"><i class="fas fa-save me-2"></i>Save Legal Documents</button>
            </form>
        </div>

        {{-- THEME TAB --}}
        <div class="tab-pane fade" id="theme" x-data="{
            primary: '{{ $s->get('primary_color','#E8922A') }}',
            secondary: '{{ $s->get('secondary_color','#2ECC8A') }}',
            gradStart: '{{ $s->get('gradient_start','#E8922A') }}',
            gradEnd: '{{ $s->get('gradient_end','#E84040') }}'
        }">
            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf
                <input type="hidden" name="tab" value="theme">
                <div class="section-title">Color Palette</div>
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Primary / Accent Color</label>
                        <div class="d-flex align-items-center gap-3">
                            <input type="color" name="primary_color" x-model="primary" value="{{ $s->get('primary_color','#E8922A') }}">
                            <input type="text" class="form-control" x-model="primary" style="font-family:monospace;max-width:110px">
                            <span class="flex-grow-1 rounded-3" :style="`background:${primary};height:36px`"></span>
                        </div>
                        <small style="color:var(--muted);font-size:.75rem">Buttons, highlights, links</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Secondary / Success Color</label>
                        <div class="d-flex align-items-center gap-3">
                            <input type="color" name="secondary_color" x-model="secondary" value="{{ $s->get('secondary_color','#2ECC8A') }}">
                            <input type="text" class="form-control" x-model="secondary" style="font-family:monospace;max-width:110px">
                            <span class="flex-grow-1 rounded-3" :style="`background:${secondary};height:36px`"></span>
                        </div>
                        <small style="color:var(--muted);font-size:.75rem">Success states, verified badges</small>
                    </div>
                </div>
                <div class="section-title">Hero Gradient</div>
                <div class="row g-4 mb-4">
                    <div class="col-md-5">
                        <label class="form-label">Gradient Start</label>
                        <div class="d-flex align-items-center gap-3">
                            <input type="color" name="gradient_start" x-model="gradStart" value="{{ $s->get('gradient_start','#E8922A') }}">
                            <input type="text" class="form-control" x-model="gradStart" style="font-family:monospace;max-width:110px">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Gradient End</label>
                        <div class="d-flex align-items-center gap-3">
                            <input type="color" name="gradient_end" x-model="gradEnd" value="{{ $s->get('gradient_end','#E84040') }}">
                            <input type="text" class="form-control" x-model="gradEnd" style="font-family:monospace;max-width:110px">
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="w-100 rounded-3" :style="`background:linear-gradient(135deg,${gradStart},${gradEnd});height:52px`"></div>
                    </div>
                </div>
                <div class="section-title">Display Mode</div>
                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="dark_mode" id="darkModeToggle" {{ $s->get('dark_mode','true') === 'true' ? 'checked' : '' }}>
                        <label class="form-check-label" for="darkModeToggle" style="color:var(--text)">Dark Mode (recommended)</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-amber px-4"><i class="fas fa-save me-2"></i>Save Theme Settings</button>
            </form>
        </div>

        {{-- LOCALE TAB --}}
        <div class="tab-pane fade" id="locale">
            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf
                <input type="hidden" name="tab" value="locale">
                <div class="section-title">Language & Region</div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Default Language</label>
                        <select name="default_language" class="form-select">
                            @foreach(['en'=>'English','sw'=>'Swahili (Kiswahili)','fr'=>'French (Français)'] as $code=>$label)
                            <option value="{{ $code }}" {{ $s->get('default_language','en')===$code?'selected':'' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Country</label>
                        <select name="default_country" class="form-select">
                            @foreach(['KE'=>'🇰🇪 Kenya','UG'=>'🇺🇬 Uganda','TZ'=>'🇹🇿 Tanzania','RW'=>'🇷🇼 Rwanda','ET'=>'🇪🇹 Ethiopia','NG'=>'🇳🇬 Nigeria','ZA'=>'🇿🇦 South Africa','GH'=>'🇬🇭 Ghana'] as $code=>$label)
                            <option value="{{ $code }}" {{ $s->get('default_country','KE')===$code?'selected':'' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Default Currency</label>
                        <select name="default_currency" class="form-select">
                            @foreach(['KES'=>'KES — Kenyan Shilling','UGX'=>'UGX — Ugandan Shilling','TZS'=>'TZS — Tanzanian Shilling','NGN'=>'NGN — Nigerian Naira','ZAR'=>'ZAR — South African Rand','GHS'=>'GHS — Ghanaian Cedi','USD'=>'USD — US Dollar'] as $code=>$label)
                            <option value="{{ $code }}" {{ $s->get('default_currency','KES')===$code?'selected':'' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Timezone</label>
                        <select name="timezone" class="form-select">
                            @foreach(['Africa/Nairobi'=>'Africa/Nairobi (EAT UTC+3)','Africa/Kampala'=>'Africa/Kampala (EAT UTC+3)','Africa/Dar_es_Salaam'=>'Africa/Dar es Salaam (EAT UTC+3)','Africa/Lagos'=>'Africa/Lagos (WAT UTC+1)','Africa/Johannesburg'=>'Africa/Johannesburg (SAST UTC+2)','Africa/Accra'=>'Africa/Accra (GMT UTC+0)','UTC'=>'UTC'] as $tz=>$label)
                            <option value="{{ $tz }}" {{ $s->get('timezone','Africa/Nairobi')===$tz?'selected':'' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-amber px-4"><i class="fas fa-save me-2"></i>Save Locale Settings</button>
            </form>
        </div>

        {{-- BILLING TAB --}}
        <div class="tab-pane fade" id="billing">
            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf
                <input type="hidden" name="tab" value="billing">
                <div class="section-title">Platform Fees</div>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Platform Fee (%)</label>
                        <div class="input-group">
                            <input type="number" name="platform_fee_percentage" class="form-control" value="{{ $s->get('platform_fee_percentage','10') }}" min="0" max="50" step="0.5">
                            <span class="input-group-text">%</span>
                        </div>
                        <small style="color:var(--muted);font-size:.75rem">Deducted from each booking payout</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Security Deposit (%)</label>
                        <div class="input-group">
                            <input type="number" name="security_deposit_percentage" class="form-control" value="{{ $s->get('security_deposit_percentage','20') }}" min="0" max="100" step="1">
                            <span class="input-group-text">%</span>
                        </div>
                        <small style="color:var(--muted);font-size:.75rem">Of booking total, held during rental</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Min Booking Duration (hours)</label>
                        <input type="number" name="min_booking_hours" class="form-control" value="{{ $s->get('min_booking_hours','4') }}" min="1" max="72">
                    </div>
                </div>
                <div class="section-title">M-Pesa / Payment Gateway</div>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">M-Pesa Environment</label>
                        <select name="mpesa_env" class="form-select">
                            <option value="sandbox" {{ $s->get('mpesa_env','sandbox')==='sandbox'?'selected':'' }}>Sandbox (Testing)</option>
                            <option value="production" {{ $s->get('mpesa_env')==='production'?'selected':'' }}>Production (Live)</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-amber px-4"><i class="fas fa-save me-2"></i>Save Billing Settings</button>
            </form>
        </div>

        {{-- EMAIL TAB --}}
        <div class="tab-pane fade" id="notifications">
            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf
                <input type="hidden" name="tab" value="notifications">
                <div class="section-title">SMTP Configuration</div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">SMTP Host</label>
                        <input type="text" name="smtp_host" class="form-control" value="{{ $s->get('smtp_host') }}" placeholder="smtp.mailgun.org">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">SMTP Port</label>
                        <input type="number" name="smtp_port" class="form-control" value="{{ $s->get('smtp_port','587') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">SMTP Username</label>
                        <input type="text" name="smtp_username" class="form-control" value="{{ $s->get('smtp_username') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">From Name</label>
                        <input type="text" name="smtp_from_name" class="form-control" value="{{ $s->get('smtp_from_name','TheOnlineYard') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">From Email Address</label>
                        <input type="email" name="smtp_from_email" class="form-control" value="{{ $s->get('smtp_from_email') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">WhatsApp Support Number</label>
                        <input type="text" name="support_whatsapp" class="form-control" value="{{ $s->get('support_whatsapp') }}" placeholder="+254700000000">
                    </div>
                </div>
                <button type="submit" class="btn btn-amber px-4"><i class="fas fa-save me-2"></i>Save Email Settings</button>
            </form>
        </div>

        {{-- SOCIAL TAB --}}
        <div class="tab-pane fade" id="social">
            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf
                <input type="hidden" name="tab" value="social">
                <div class="section-title">Social Media Links</div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label"><i class="fab fa-facebook me-1" style="color:#1877f2"></i>Facebook Page URL</label>
                        <input type="url" name="facebook_url" class="form-control" value="{{ $s->get('facebook_url') }}" placeholder="https://facebook.com/theonlineyard">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><i class="fab fa-twitter me-1" style="color:#1da1f2"></i>Twitter / X URL</label>
                        <input type="url" name="twitter_url" class="form-control" value="{{ $s->get('twitter_url') }}" placeholder="https://x.com/theonlineyard">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><i class="fab fa-instagram me-1" style="color:#e1306c"></i>Instagram URL</label>
                        <input type="url" name="instagram_url" class="form-control" value="{{ $s->get('instagram_url') }}" placeholder="https://instagram.com/theonlineyard">
                    </div>
                </div>
                <button type="submit" class="btn btn-amber px-4"><i class="fas fa-save me-2"></i>Save Social Links</button>
            </form>
        </div>

        {{-- SECURITY TAB --}}
        <div class="tab-pane fade" id="security">
            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf
                <input type="hidden" name="tab" value="security">
                <div class="section-title">Two-Factor Authentication (MFA)</div>
                <div class="card p-3 mb-4" style="background:var(--surface);border-color:var(--border)">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div style="font-weight:600;color:var(--text)">Require MFA on every login</div>
                            <div style="color:var(--muted);font-size:.8rem;margin-top:.25rem">Users receive a 6-digit OTP after entering their password</div>
                        </div>
                        <div class="form-check form-switch ms-3">
                            <input class="form-check-input" type="checkbox" name="mfa_enabled" id="mfaToggle" {{ $s->get('mfa_enabled','true')==='true'?'checked':'' }}>
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label">OTP Delivery Method</label>
                    <div class="d-flex gap-4 mt-1">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="mfa_method" id="mfaEmail" value="email" {{ $s->get('mfa_method','email')==='email'?'checked':'' }}>
                            <label class="form-check-label" for="mfaEmail" style="color:var(--text)">
                                <i class="fas fa-envelope me-1" style="color:var(--amber)"></i>Email (recommended)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="mfa_method" id="mfaSms" value="sms" {{ $s->get('mfa_method')==='sms'?'checked':'' }}>
                            <label class="form-check-label" for="mfaSms" style="color:var(--text)">
                                <i class="fas fa-mobile-alt me-1" style="color:var(--amber)"></i>SMS (requires SMS gateway)
                            </label>
                        </div>
                    </div>
                    <small style="color:var(--muted);font-size:.75rem">SMS falls back to email until an SMS gateway is configured</small>
                </div>
                <div class="p-3 rounded-3 mb-4" style="background:rgba(232,146,42,.06);border:1px solid rgba(232,146,42,.2)">
                    <div style="color:var(--amber);font-size:.8rem;font-weight:600"><i class="fas fa-info-circle me-1"></i>Note</div>
                    <div style="color:var(--muted);font-size:.78rem;margin-top:.25rem">
                        Codes are valid for 10 minutes. Users can resend from the verification page. Disabling MFA allows direct login after correct password entry.
                    </div>
                </div>
                <button type="submit" class="btn btn-amber px-4"><i class="fas fa-save me-2"></i>Save Security Settings</button>
            </form>
        </div>

    </div>
</div>
@endsection
