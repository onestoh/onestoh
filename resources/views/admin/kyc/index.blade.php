@extends('layouts.admin')

@section('title', 'KYC Reviews')

@section('content')
<h4 class="mb-4" style="color:var(--text);font-weight:700">KYC Reviews <span style="color:var(--muted);font-size:.8rem;font-weight:400">({{ $pendingDocs->count() }} users pending)</span></h4>

@if($pendingDocs->isEmpty())
<div class="text-center py-5" style="color:var(--muted)">
    <i class="fas fa-check-circle fa-3x mb-3 d-block text-success" style="opacity:.6"></i>
    <h5 style="color:var(--text)">All clear!</h5>
    <p>No pending KYC documents to review.</p>
</div>
@else
@foreach($pendingDocs as $userId => $docs)
@php $user = $docs->first()->user; @endphp
<div class="card mb-4">
    <div class="card-header d-flex align-items-center gap-3">
        <div>
            <div style="color:var(--text);font-weight:600">{{ $user->name }}</div>
            <div style="color:var(--muted);font-size:.78rem">{{ $user->email }} &nbsp;·&nbsp; {{ ucwords(str_replace('_',' ',$user->role)) }}</div>
        </div>
        <div class="ms-auto d-flex gap-2">
            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm" style="border:1px solid var(--border);color:var(--muted);font-size:.78rem">View Profile</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row g-3">
            @foreach($docs as $doc)
            <div class="col-md-6">
                <div class="p-3 rounded-3" style="background:var(--black);border:1px solid var(--border)">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <div style="color:var(--text);font-weight:600;font-size:.875rem">{{ ucwords(str_replace('_',' ',$doc->document_type)) }}</div>
                            <div style="color:var(--muted);font-size:.75rem">{{ $doc->created_at->diffForHumans() }}</div>
                        </div>
                        <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="btn btn-sm" style="border:1px solid var(--border);color:var(--muted);font-size:.75rem">
                            <i class="fas fa-eye me-1"></i>View File
                        </a>
                    </div>
                    <div class="d-flex gap-2">
                        <form method="POST" action="{{ route('admin.kyc.approve', $doc) }}" class="flex-grow-1">
                            @csrf
                            <button type="submit" class="btn btn-sm w-100" style="background:rgba(46,204,138,.15);color:var(--green);border:1px solid rgba(46,204,138,.3)">
                                <i class="fas fa-check me-1"></i>Approve
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.kyc.reject', $doc) }}" class="flex-grow-1" x-data="{ open: false }">
                            @csrf
                            <button type="button" @click="open = !open" class="btn btn-sm w-100" style="background:rgba(232,64,64,.15);color:var(--danger);border:1px solid rgba(232,64,64,.3)">
                                <i class="fas fa-times me-1"></i>Reject
                            </button>
                            <div x-show="open" class="mt-2">
                                <input type="text" name="notes" class="form-control form-control-sm mb-1" placeholder="Reason for rejection…"
                                    style="background:var(--surface);border-color:var(--border);color:var(--text)">
                                <button type="submit" class="btn btn-sm w-100 btn-danger" style="font-size:.75rem">Confirm Rejection</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endforeach
@endif
@endsection
