@extends('layouts.admin')
@section('title', 'Categories')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-0 fw-bold"><i class="fas fa-tags text-amber me-2"></i>Categories</h4>
        <small class="text-muted">Manage asset categories for listings</small>
    </div>
</div>

<div class="row g-4">
    {{-- LEFT: Add Category --}}
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-plus-circle me-2 text-amber"></i>Add Category</h6>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger py-2">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $err)
                                <li style="font-size:.85rem">{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form method="POST" action="{{ route('admin.categories.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" style="color:var(--muted);font-size:.8rem;text-transform:uppercase;letter-spacing:.5px">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                            placeholder="e.g. Heavy Machinery"
                            style="background:rgba(0,0,0,.3);border:1px solid var(--border);color:var(--text)">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="color:var(--muted);font-size:.8rem;text-transform:uppercase;letter-spacing:.5px">Description <span class="text-muted fw-normal">(optional)</span></label>
                        <textarea name="description" class="form-control" rows="3"
                            placeholder="Brief description of this category"
                            style="background:rgba(0,0,0,.3);border:1px solid var(--border);color:var(--text)">{{ old('description') }}</textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label" style="color:var(--muted);font-size:.8rem;text-transform:uppercase;letter-spacing:.5px">Icon class <span class="text-muted fw-normal">(optional)</span></label>
                        <input type="text" name="icon" class="form-control" value="{{ old('icon') }}"
                            placeholder="e.g. fa-car"
                            style="background:rgba(0,0,0,.3);border:1px solid var(--border);color:var(--text)">
                        <small style="color:var(--muted);font-size:.75rem">Font Awesome icon class, e.g. <code style="color:var(--amber)">fa-car</code>, <code style="color:var(--amber)">fa-truck</code>, <code style="color:var(--amber)">fa-tractor</code></small>
                    </div>
                    <button type="submit" class="btn btn-amber w-100">
                        <i class="fas fa-plus me-2"></i>Create Category
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- RIGHT: Categories Table --}}
    <div class="col-md-7">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="mb-0"><i class="fas fa-list me-2 text-amber"></i>All Categories</h6>
                <span class="badge" style="background:rgba(232,146,42,.15);color:var(--amber);border:1px solid rgba(232,146,42,.3)">{{ $categories->count() }} total</span>
            </div>
            <div class="card-body p-0">
                @if($categories->isEmpty())
                    <div class="text-center py-5" style="color:var(--muted)">
                        <i class="fas fa-tags fa-2x mb-3 d-block"></i>
                        No categories yet. Add your first one.
                    </div>
                @else
                <div class="table-responsive">
                    <table class="table table-sm mb-0" style="border-color:var(--border)">
                        <thead>
                            <tr style="border-color:var(--border);background:rgba(0,0,0,.2)">
                                <th style="color:var(--muted);font-size:.75rem;padding:.75rem 1rem">Icon</th>
                                <th style="color:var(--muted);font-size:.75rem">Name</th>
                                <th style="color:var(--muted);font-size:.75rem">Description</th>
                                <th style="color:var(--muted);font-size:.75rem;text-align:center">Listings</th>
                                <th style="color:var(--muted);font-size:.75rem;text-align:right;padding-right:1rem">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $cat)
                            <tr style="border-color:var(--border)" x-data="{ editing: false }">
                                <td style="padding:.75rem 1rem;vertical-align:middle">
                                    @if($cat->icon)
                                        <i class="fas {{ $cat->icon }}" style="color:var(--amber);font-size:1.1rem;width:20px;text-align:center"></i>
                                    @else
                                        <i class="fas fa-tag" style="color:var(--muted);font-size:1rem;width:20px;text-align:center"></i>
                                    @endif
                                </td>
                                <td style="vertical-align:middle">
                                    <span x-show="!editing" style="font-weight:600;color:var(--text)">{{ $cat->name }}</span>
                                    <span x-show="editing" x-cloak>
                                        <form method="POST" action="{{ route('admin.categories.update', $cat) }}" class="d-flex gap-2 align-items-center flex-wrap">
                                            @csrf @method('PUT')
                                            <input type="text" name="name" value="{{ $cat->name }}" class="form-control form-control-sm" style="background:rgba(0,0,0,.3);border:1px solid var(--border);color:var(--text);width:120px">
                                            <input type="text" name="icon" value="{{ $cat->icon }}" placeholder="fa-icon" class="form-control form-control-sm" style="background:rgba(0,0,0,.3);border:1px solid var(--border);color:var(--text);width:90px">
                                            <textarea name="description" rows="1" class="form-control form-control-sm" style="background:rgba(0,0,0,.3);border:1px solid var(--border);color:var(--text);width:140px;resize:none">{{ $cat->description }}</textarea>
                                            <button type="submit" class="btn btn-sm btn-amber"><i class="fas fa-check"></i></button>
                                            <button type="button" class="btn btn-sm" style="background:rgba(255,255,255,.05);color:var(--text)" @click="editing=false"><i class="fas fa-times"></i></button>
                                        </form>
                                    </span>
                                </td>
                                <td style="vertical-align:middle;color:var(--muted);font-size:.85rem" x-show="!editing">
                                    {{ $cat->description ? Str::limit($cat->description, 60) : '—' }}
                                </td>
                                <td x-show="!editing" style="text-align:center;vertical-align:middle">
                                    <span style="font-weight:700;color:var(--amber)">{{ $cat->listings_count }}</span>
                                </td>
                                <td x-show="!editing" style="text-align:right;padding-right:1rem;vertical-align:middle">
                                    <button type="button" class="btn btn-sm me-1" style="background:rgba(232,146,42,.1);color:var(--amber);border:1px solid rgba(232,146,42,.2)"
                                        @click="editing=true">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @if($cat->listings_count == 0)
                                    <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" class="d-inline"
                                        onsubmit="return confirm('Delete category \'{{ addslashes($cat->name) }}\'? This cannot be undone.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm" style="background:rgba(232,64,64,.1);color:var(--danger);border:1px solid rgba(232,64,64,.2)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @else
                                    <button class="btn btn-sm disabled" style="background:rgba(255,255,255,.03);color:var(--muted)" title="Cannot delete — has listings">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush
