@extends('layouts.dashboard')
@section('title', 'Tenant Dashboard — EstateYard')
@section('page-title', 'My Home')
@section('page-subtitle', 'Tenant · Rent, Lease & Maintenance')

@section('sidebar-nav')
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Overview</div>
  <a href="{{ url('/dashboard/tenant') }}" class="dash-nav-item active"><span class="dash-nav-icon">🔑</span> Dashboard</a>
  <a href="{{ url('/marketplace') }}" class="dash-nav-item"><span class="dash-nav-icon">🔍</span> Browse Properties</a>
</div>
<div class="dash-nav-section">
  <div class="dash-nav-section-title">Rent</div>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">💳</span> Pay Rent</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">📋</span> Payment History</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">🧾</span> Invoices</a>
</div>
<div class="dash-nav-section">
  <div class="dash-nav-section-title">My Lease</div>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">📄</span> Lease Agreement</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">📂</span> Documents</a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">🔒</span> Security Bond</a>
</div>
<div class="dash-nav-section">
  <div class="dash-nav-section-title">More</div>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">🔧</span> Maintenance <span class="dash-nav-badge">2</span></a>
  <a href="#" class="dash-nav-item"><span class="dash-nav-icon">❤️</span> Saved Properties</a>
</div>
@endsection

@section('content')

@if(session('success'))
<div class="alert alert-green" style="margin-bottom:24px;">{{ session('success') }}</div>
@endif

@if($upcomingPayment && $activeLease)
<div class="alert alert-gold" style="margin-bottom:24px; font-size:14px;">
  💳 <strong>Rent due {{ $upcomingPayment->due_date ? \Carbon\Carbon::parse($upcomingPayment->due_date)->diffForHumans() : 'soon' }}:</strong>
  KSh {{ number_format($upcomingPayment->amount) }} for {{ $activeLease->property->title ?? 'your property' }}.
  <a href="#pay-rent" style="color:var(--navy); background:var(--gold); border-radius:4px; padding:2px 10px; font-size:12px; font-weight:700; margin-left:12px;">Pay Now</a>
</div>
@endif

<!-- KPIs -->
<div class="kpi-grid">
  <div class="kpi-card">
    <div class="kpi-icon">💳</div>
    <div class="kpi-value">{{ $upcomingPayment ? 'KSh '.number_format($upcomingPayment->amount) : 'None' }}</div>
    <div class="kpi-label">Rent Due</div>
    <div class="kpi-change down">{{ $upcomingPayment && $upcomingPayment->due_date ? 'Due '.\Carbon\Carbon::parse($upcomingPayment->due_date)->format('d M Y') : 'No pending payment' }}</div>
  </div>
  <div class="kpi-card">
    <div class="kpi-icon">📋</div>
    <div class="kpi-value">{{ $activeLease ? 'Active' : 'None' }}</div>
    <div class="kpi-label">Current Lease</div>
    <div class="kpi-change {{ $activeLease ? 'up' : 'down' }}">{{ $activeLease ? $activeLease->property->title ?? 'N/A' : 'No active lease' }}</div>
  </div>
  <div class="kpi-card">
    <div class="kpi-icon">📊</div>
    <div class="kpi-value">{{ $paymentHistory->count() }}</div>
    <div class="kpi-label">Payments Made</div>
    <div class="kpi-change up">Total history</div>
  </div>
  <div class="kpi-card">
    <div class="kpi-icon">🔧</div>
    <div class="kpi-value">{{ $maintenanceRequests->where('status','open')->count() }}</div>
    <div class="kpi-label">Open Maintenance</div>
    <div class="kpi-change down">{{ $maintenanceRequests->where('priority','urgent')->count() }} urgent</div>
  </div>
</div>

<!-- PAY RENT + LEASE DETAILS -->
<div class="grid-2" style="margin-bottom:28px;" id="pay-rent">

  <!-- Pay Rent -->
  <div style="background:var(--navy3); border:1px solid var(--border); border-radius:var(--radius); padding:28px;">
    <div class="table-card-title" style="margin-bottom:20px;">💳 Pay Rent</div>
    @if($activeLease && $upcomingPayment)
    <div style="background:var(--surface); border-radius:12px; padding:20px; margin-bottom:20px;">
      <div style="font-size:12px; font-family:var(--font-mono); color:var(--muted); margin-bottom:6px;">AMOUNT DUE — {{ strtoupper(date('F Y')) }}</div>
      <div style="font-family:var(--font-serif); font-size:42px; font-weight:700; color:var(--gold); line-height:1;">KSh {{ number_format($upcomingPayment->amount) }}</div>
      <div style="font-size:13px; color:var(--muted); margin-top:6px;">{{ $activeLease->property->title ?? 'Your Property' }} · Due: {{ $upcomingPayment->due_date ? \Carbon\Carbon::parse($upcomingPayment->due_date)->format('d M Y') : 'N/A' }}</div>
    </div>
    <div style="display:flex; flex-direction:column; gap:10px;">
      <form method="POST" action="{{ url('/rent/pay') }}" id="mpesaForm">
        @csrf
        <input type="hidden" name="lease_id" value="{{ $activeLease->id }}">
        <input type="hidden" name="amount" value="{{ $upcomingPayment->amount }}">
        <input type="hidden" name="payment_method" value="mpesa">
        <button type="button" onclick="submitRentPayment('mpesa')" class="btn btn-gold" style="width:100%; justify-content:center; font-size:15px; padding:14px;">
          💳 Pay via M-Pesa (STK Push)
        </button>
      </form>
      <form method="POST" action="{{ url('/rent/pay') }}" id="bankForm">
        @csrf
        <input type="hidden" name="lease_id" value="{{ $activeLease->id }}">
        <input type="hidden" name="amount" value="{{ $upcomingPayment->amount }}">
        <input type="hidden" name="payment_method" value="bank">
        <button type="button" onclick="submitRentPayment('bank')" class="btn btn-outline" style="width:100%; justify-content:center; font-size:14px; padding:12px;">
          🏦 Pay via Bank Account
        </button>
      </form>
    </div>
    @else
    <div style="text-align:center; padding:32px 0; color:var(--muted);">
      <div style="font-size:48px; margin-bottom:12px;">✅</div>
      <div style="font-size:15px;">No pending rent payments.</div>
    </div>
    @endif
    <div style="font-size:12px; color:var(--muted); text-align:center; margin-top:14px;">🔒 Payment processed through EstateYard secure portal only</div>
  </div>

  <!-- Lease Details -->
  <div style="background:var(--navy3); border:1px solid var(--border-dim); border-radius:var(--radius); padding:28px;">
    <div class="table-card-title" style="margin-bottom:20px;">📄 Current Lease</div>
    @if($activeLease)
    @foreach([
      ['Property', $activeLease->property->title ?? 'N/A'],
      ['Landlord', $activeLease->landlord->name ?? 'N/A'],
      ['Lease Start', $activeLease->start_date ? $activeLease->start_date->format('d M Y') : 'N/A'],
      ['Lease End', $activeLease->end_date ? $activeLease->end_date->format('d M Y') : 'N/A'],
      ['Monthly Rent', 'KSh '.number_format($activeLease->monthly_rent ?? 0)],
      ['Status', strtoupper($activeLease->status)],
    ] as $d)
    <div style="display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px solid var(--border-dim);">
      <span style="font-size:13px; color:var(--muted);">{{ $d[0] }}</span>
      <span style="font-size:13px; color:var(--white); font-weight:500;">{{ $d[1] }}</span>
    </div>
    @endforeach
    <div style="margin-top:16px; display:flex; gap:8px;">
      <button class="btn btn-sm btn-outline" style="flex:1; justify-content:center;">📄 Download Lease</button>
      <button class="btn btn-sm btn-outline" style="flex:1; justify-content:center;">✉️ Contact Landlord</button>
    </div>
    @else
    <div style="text-align:center; padding:32px 0; color:var(--muted);">
      <div style="font-size:48px; margin-bottom:12px;">🔑</div>
      <div>No active lease found.</div>
    </div>
    @endif
  </div>
</div>

<!-- PAYMENT HISTORY -->
<div class="table-card" style="margin-bottom:28px;">
  <div class="table-card-header">
    <div class="table-card-title">📋 Payment History</div>
    <button class="btn btn-sm btn-outline">📊 Export PDF</button>
  </div>
  <table class="data-table">
    <thead><tr><th>Amount</th><th>Due Date</th><th>Paid On</th><th>Method</th><th>Status</th></tr></thead>
    <tbody>
      @forelse($paymentHistory as $p)
      <tr>
        <td class="td-price">KSh {{ number_format($p->amount) }}</td>
        <td>{{ $p->due_date ? \Carbon\Carbon::parse($p->due_date)->format('d M Y') : '—' }}</td>
        <td>{{ $p->paid_at ? \Carbon\Carbon::parse($p->paid_at)->format('d M Y') : '—' }}</td>
        <td>{{ ucfirst($p->method ?? '—') }}</td>
        <td><span class="status-pill status-{{ $p->status }}">{{ strtoupper($p->status) }}</span></td>
      </tr>
      @empty
      <tr><td colspan="5" style="text-align:center; color:var(--muted); padding:24px;">No payment history.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<!-- MAINTENANCE -->
<div class="table-card">
  <div class="table-card-header">
    <div class="table-card-title">🔧 Maintenance Requests</div>
    <button onclick="showToast('Redirect to maintenance form', 'green')" class="btn btn-gold btn-sm">+ New Request</button>
  </div>
  <table class="data-table">
    <thead><tr><th>Issue</th><th>Property</th><th>Submitted</th><th>Priority</th><th>Status</th></tr></thead>
    <tbody>
      @forelse($maintenanceRequests as $m)
      <tr>
        <td class="td-name">🔧 {{ $m->title }}</td>
        <td>{{ $m->property->title ?? 'N/A' }}</td>
        <td>{{ $m->created_at->diffForHumans() }}</td>
        <td><span class="status-pill {{ $m->priority==='urgent'||$m->priority==='high' ? 'status-overdue' : ($m->priority==='medium' ? 'status-review' : 'status-draft') }}">{{ strtoupper($m->priority) }}</span></td>
        <td><span class="status-pill status-{{ $m->status === 'open' ? 'pending' : ($m->status === 'resolved' ? 'active' : 'review') }}">{{ strtoupper($m->status) }}</span></td>
      </tr>
      @empty
      <tr><td colspan="5" style="text-align:center; color:var(--muted); padding:24px;">No maintenance requests.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection

@push('scripts')
<script>
function submitRentPayment(method) {
  const form = method === 'mpesa' ? document.getElementById('mpesaForm') : document.getElementById('bankForm');
  const formData = new FormData(form);

  fetch('{{ url("/rent/pay") }}', {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
    body: formData
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      if (method === 'mpesa') {
        showToast(data.message || 'M-Pesa STK Push sent. Enter your PIN.', 'green');
      } else {
        showToast('Bank transfer: Account ESTATEYARD-{{ $activeLease->id ?? "" }}, Equity Bank, Westlands.', 'blue');
      }
    } else {
      showToast(data.message || 'Payment failed. Try again.', 'red');
    }
  })
  .catch(() => showToast('Network error. Please try again.', 'red'));
}
</script>
@endpush
