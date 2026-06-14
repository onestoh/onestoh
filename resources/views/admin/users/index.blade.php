@extends('layouts.admin')

@section('title', 'Users')

@push('styles')
<style>
.form-control, .form-select { background: var(--black); border: 1px solid var(--border); color: var(--text); font-size: .85rem; }
.status-pill { padding: 2px 8px; border-radius: 12px; font-size: .72rem; font-weight: 600; }
.pill-verified { background: rgba(46,204,138,.15); color: var(--green); }
.pill-pending { background: rgba(232,146,42,.15); color: var(--amber); }
.pill-suspended { background: rgba(232,64,64,.15); color: var(--danger); }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 style="color:var(--text);font-weight:700;margin:0">Users</h4>
    <span style="color:var(--muted);font-size:.875rem">{{ $users->total() }} total</span>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-5"><input type="text" name="q" class="form-control" placeholder="Search name, email, phone…" value="{{ request('q') }}"></div>
            <div class="col-md-3">
                <select name="role" class="form-select">
                    <option value="">All Roles</option>
                    @foreach(['client','yard_owner','individual_owner','broker','operator','super_admin'] as $r)
                    <option value="{{ $r }}" {{ request('role') === $r ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$r)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach(['pending','verified','suspended'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-amber w-100 btn-sm">Search</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-sm mb-0" style="color:var(--text);--bs-table-bg:transparent">
            <thead style="font-size:.72rem;color:var(--muted);text-transform:uppercase;border-bottom:1px solid var(--border)">
                <tr><th class="px-3 py-2">User</th><th>Role</th><th>Status</th><th>Listings</th><th>Bookings</th><th>Joined</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr style="border-color:var(--border)">
                    <td class="px-3 py-2">
                        <a href="{{ route('admin.users.show', $user) }}" style="color:var(--text);text-decoration:none">
                            <div style="font-weight:600;font-size:.875rem">{{ $user->name }}</div>
                            <div style="color:var(--muted);font-size:.75rem">{{ $user->email }}</div>
                        </a>
                    </td>
                    <td style="font-size:.8rem;color:var(--muted)">{{ ucwords(str_replace('_',' ',$user->role)) }}</td>
                    <td><span class="status-pill pill-{{ $user->status }}">{{ ucfirst($user->status) }}</span></td>
                    <td style="font-size:.85rem">{{ $user->listings_count }}</td>
                    <td style="font-size:.85rem">{{ $user->client_bookings_count }}</td>
                    <td style="font-size:.78rem;color:var(--muted)">{{ $user->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            @if($user->status !== 'verified')
                            <form method="POST" action="{{ route('admin.users.verify', $user) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm px-2 py-1" style="background:rgba(46,204,138,.15);color:var(--green);font-size:.72rem" title="Verify">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            @endif
                            @if($user->status !== 'suspended' && $user->role !== 'super_admin')
                            <form method="POST" action="{{ route('admin.users.suspend', $user) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm px-2 py-1" style="background:rgba(232,64,64,.15);color:var(--danger);font-size:.72rem" title="Suspend" onclick="return confirm('Suspend this user?')">
                                    <i class="fas fa-ban"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer" style="background:transparent;border-top:1px solid var(--border)">
        {{ $users->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
