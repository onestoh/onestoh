@extends('layouts.dashboard')

@section('title', 'KYC Verification')
@section('page-title', 'KYC Verification')

@section('content')
<div class="mb-4 d-flex align-items-center justify-content-between">
    <div>
        <h4 style="color:var(--text);font-weight:700">Identity Verification</h4>
        <p style="color:var(--muted)">Upload your documents to unlock full platform access.</p>
    </div>
    <div>
        @php $status = auth()->user()->status @endphp
        @if($status === 'verified')
        <span class="badge fs-6 px-3 py-2" style="background:rgba(46,204,138,.15);color:var(--green);border:1px solid rgba(46,204,138,.3)"><i class="fas fa-check-circle me-2"></i>Verified</span>
        @elseif($status === 'pending')
        <span class="badge fs-6 px-3 py-2" style="background:rgba(232,146,42,.15);color:var(--amber);border:1px solid rgba(232,146,42,.3)"><i class="fas fa-clock me-2"></i>Pending Review</span>
        @elseif($status === 'rejected')
        <span class="badge fs-6 px-3 py-2" style="background:rgba(220,53,69,.15);color:#f87171;border:1px solid rgba(220,53,69,.3)"><i class="fas fa-times-circle me-2"></i>Rejected</span>
        @else
        <span class="badge fs-6 px-3 py-2" style="background:rgba(112,136,168,.15);color:var(--muted);border:1px solid var(--border)">Not Submitted</span>
        @endif
    </div>
</div>

@php
$docTypes = [
    'national_id_front'       => ['label' => 'National ID (Front)', 'icon' => 'fa-id-card'],
    'national_id_back'        => ['label' => 'National ID (Back)',  'icon' => 'fa-id-card'],
    'drivers_license'         => ['label' => "Driver's License",    'icon' => 'fa-car'],
    'kra_pin'                 => ['label' => 'KRA PIN Certificate', 'icon' => 'fa-file-invoice'],
];
if(in_array(auth()->user()->role, ['yard_owner','individual_owner'])) {
    $docTypes['business_registration'] = ['label' => 'Business Registration',   'icon' => 'fa-building'];
    $docTypes['ntsa_certificate']      = ['label' => 'NTSA Clearance Certificate', 'icon' => 'fa-certificate'];
}
@endphp

<div class="row g-3">
    @foreach($docTypes as $type => $meta)
    @php $doc = $documents->get($type)?->first() @endphp
    <div class="col-md-6">
        <div class="p-3 rounded-3 h-100" style="background:var(--surface);border:1px solid var(--border)">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:42px;height:42px;background:rgba(232,146,42,.1);color:var(--amber)">
                    <i class="fas {{ $meta['icon'] }}"></i>
                </div>
                <div class="flex-grow-1">
                    <div style="color:var(--text);font-weight:600">{{ $meta['label'] }}</div>
                    @if($doc)
                    <div style="color:var(--muted);font-size:.78rem">Uploaded {{ $doc->created_at->diffForHumans() }}</div>
                    @endif
                </div>
                @if($doc)
                @if($doc->status === 'approved')
                <span class="badge" style="background:rgba(46,204,138,.15);color:var(--green)"><i class="fas fa-check me-1"></i>Approved</span>
                @elseif($doc->status === 'pending')
                <span class="badge" style="background:rgba(232,146,42,.15);color:var(--amber)"><i class="fas fa-clock me-1"></i>Pending</span>
                @elseif($doc->status === 'rejected')
                <span class="badge" style="background:rgba(220,53,69,.15);color:#f87171"><i class="fas fa-times me-1"></i>Rejected</span>
                @endif
                @else
                <span class="badge" style="background:rgba(112,136,168,.1);color:var(--muted)">Not Uploaded</span>
                @endif
            </div>

            @if($doc && $doc->status === 'rejected' && $doc->admin_notes)
            <div class="p-2 rounded-2 mb-3" style="background:rgba(220,53,69,.08);border:1px solid rgba(220,53,69,.2)">
                <p style="color:#f87171;font-size:.82rem;margin:0"><i class="fas fa-exclamation-triangle me-1"></i>{{ $doc->admin_notes }}</p>
            </div>
            @endif

            @if(!$doc || $doc->status === 'rejected')
            <form method="POST" action="{{ route('kyc.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="document_type" value="{{ $type }}">
                <div class="mb-2">
                    <input type="file" name="file" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png" required
                        style="background:var(--dark);border-color:var(--border);color:var(--text)">
                    <div style="color:var(--muted);font-size:.75rem" class="mt-1">PDF, JPG or PNG — max 5 MB</div>
                </div>
                <button type="submit" class="btn btn-sm btn-amber w-100">
                    <i class="fas fa-upload me-1"></i>Upload Document
                </button>
            </form>
            @elseif($doc->status === 'approved')
            <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="btn btn-sm w-100" style="border:1px solid var(--border);color:var(--muted)">
                <i class="fas fa-eye me-1"></i>View File
            </a>
            @else
            <p style="color:var(--muted);font-size:.82rem" class="mb-0">Document is under review. You will be notified once it is processed.</p>
            @endif
        </div>
    </div>
    @endforeach
</div>
@endsection
