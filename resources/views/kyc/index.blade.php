@extends('layouts.dashboard')

@section('title', 'KYC Verification')

@section('content')
<div class="mb-4">
    <h4 style="color:var(--text);font-weight:700">Identity Verification</h4>
    <p style="color:var(--muted)">Upload your documents to verify your account and unlock full platform features.</p>
</div>

@if(session('success'))
<div class="alert mb-4" style="background:rgba(46,204,138,.1);border-color:rgba(46,204,138,.3);color:var(--green)">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
</div>
@endif

@php
$allDocs = $documents->flatten();
$overallStatus = 'not_submitted';
if ($allDocs->count()) {
    if ($allDocs->every(fn($d) => $d->status === 'verified')) $overallStatus = 'verified';
    elseif ($allDocs->contains(fn($d) => $d->status === 'rejected')) $overallStatus = 'rejected';
    else $overallStatus = 'pending';
}
@endphp

<div class="p-3 rounded-3 mb-4 d-flex align-items-center gap-3" style="background:var(--surface);border:1px solid var(--border)">
    <div style="font-size:2rem">
        @if($overallStatus === 'verified') <i class="fas fa-shield-check" style="color:var(--green)"></i>
        @elseif($overallStatus === 'pending') <i class="fas fa-clock" style="color:var(--amber)"></i>
        @elseif($overallStatus === 'rejected') <i class="fas fa-times-circle" style="color:#f87171"></i>
        @else <i class="fas fa-id-card" style="color:var(--muted)"></i>
        @endif
    </div>
    <div>
        <div style="color:var(--text);font-weight:600">Verification Status:
            @if($overallStatus === 'verified') <span style="color:var(--green)">Verified</span>
            @elseif($overallStatus === 'pending') <span style="color:var(--amber)">Pending Review</span>
            @elseif($overallStatus === 'rejected') <span style="color:#f87171">Action Required</span>
            @else <span style="color:var(--muted)">Not Started</span>
            @endif
        </div>
        <div style="color:var(--muted);font-size:.875rem">
            @if($overallStatus === 'pending') Our team is reviewing your documents. This typically takes 1-2 business days.
            @elseif($overallStatus === 'rejected') Some documents were rejected. Please re-upload the flagged documents below.
            @elseif($overallStatus === 'not_submitted') Please upload the required documents to get verified.
            @else All documents have been verified. Your account is fully verified.
            @endif
        </div>
    </div>
</div>

@php
$docTypes = [
    'national_id_front' => ['label' => 'National ID (Front)', 'required' => true, 'roles' => null],
    'national_id_back' => ['label' => 'National ID (Back)', 'required' => true, 'roles' => null],
    'drivers_license' => ['label' => "Driver's License", 'required' => false, 'roles' => null],
    'business_registration' => ['label' => 'Business Registration Certificate', 'required' => false, 'roles' => ['yard_owner']],
    'kra_pin' => ['label' => 'KRA PIN Certificate', 'required' => false, 'roles' => null],
    'ntsa_certificate' => ['label' => 'NTSA Certificate', 'required' => false, 'roles' => ['yard_owner']],
];
@endphp

<div class="row g-3">
    @foreach($docTypes as $type => $info)
    @if($info['roles'] === null || in_array($user->role, $info['roles']))
    @php $doc = $documents->get($type)?->first() @endphp
    <div class="col-md-6">
        <div class="p-3 rounded-3 h-100" style="background:var(--surface);border:1px solid var(--border)">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <div style="color:var(--text);font-weight:600">{{ $info['label'] }}</div>
                    @if($info['required'])<span style="color:var(--amber);font-size:.75rem">Required</span>@endif
                </div>
                @if($doc)
                @if($doc->status === 'verified')
                <span class="badge" style="background:rgba(46,204,138,.15);color:var(--green)"><i class="fas fa-check me-1"></i>Verified</span>
                @elseif($doc->status === 'pending')
                <span class="badge" style="background:rgba(232,146,42,.15);color:var(--amber)"><i class="fas fa-clock me-1"></i>Pending</span>
                @elseif($doc->status === 'rejected')
                <span class="badge" style="background:rgba(220,53,69,.15);color:#f87171"><i class="fas fa-times me-1"></i>Rejected</span>
                @endif
                @else
                <span class="badge" style="background:rgba(112,136,168,.1);color:var(--muted)">Not Uploaded</span>
                @endif
            </div>

            @if($doc)
            <div style="color:var(--muted);font-size:.78rem" class="mb-2">
                Uploaded {{ $doc->updated_at->diffForHumans() }}
            </div>
            @if($doc->status === 'rejected' && $doc->admin_notes)
            <div class="p-2 rounded-2 mb-2" style="background:rgba(220,53,69,.08);border:1px solid rgba(220,53,69,.2)">
                <div style="color:#f87171;font-size:.78rem;font-weight:600">Rejection reason:</div>
                <div style="color:var(--text);font-size:.85rem">{{ $doc->admin_notes }}</div>
            </div>
            @endif
            @endif

            @if(!$doc || $doc->status !== 'verified')
            <form method="POST" action="{{ route('kyc.store') }}" enctype="multipart/form-data" class="mt-2">
                @csrf
                <input type="hidden" name="document_type" value="{{ $type }}">
                <div class="mb-2">
                    <input type="file" name="file" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png" required
                        style="background:var(--dark);border-color:var(--border);color:var(--text)">
                    <div style="color:var(--muted);font-size:.72rem" class="mt-1">PDF, JPG or PNG — max 5MB</div>
                </div>
                <button type="submit" class="btn btn-sm" style="background:var(--amber);color:#000;font-weight:600">
                    {{ $doc ? 'Re-upload' : 'Upload' }}
                </button>
            </form>
            @endif
        </div>
    </div>
    @endif
    @endforeach
</div>
@endsection
