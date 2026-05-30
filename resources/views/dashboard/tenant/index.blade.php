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

<!-- RENT DUE ALERT -->
<div class="alert alert-gold" style="margin-bottom:24px; font-size:14px;">
  💳 <strong>Rent due in 3 days (June 1, 2026):</strong> KSh 95,000 for Westlands Penthouse Apt 2A.
  <a href="#" onclick="showToast('M-Pesa STK Push sent to +254 712 XXX XXX', 'green'); return false;" style="color:var(--navy); background:var(--gold); border-radius:4px; padding:2px 10px; font-size:12px; font-weight:700; margin-left:12px;">Pay Now</a>
</div>

<!-- KPIs -->
<div class="kpi-grid">
  <div class="kpi-card"><div class="kpi-icon">💳</div><div class="kpi-value">KSh 95K</div><div class="kpi-label">Rent Due</div><div class="kpi-change down">Due June 1, 2026</div></div>
  <div class="kpi-card"><div class="kpi-icon">📅</div><div class="kpi-value">3 Days</div><div class="kpi-label">Until Next Due Date</div><div class="kpi-change down">June 1st monthly</div></div>
  <div class="kpi-card"><div class="kpi-icon">🔒</div><div class="kpi-value">KSh 190K</div><div class="kpi-label">Security Bond</div><div class="kpi-change up">In escrow · Refundable</div></div>
  <div class="kpi-card"><div class="kpi-icon">🔧</div><div class="kpi-value">2</div><div class="kpi-label">Open Maintenance</div><div class="kpi-change down">1 urgent pending</div></div>
</div>

<!-- PAY RENT + LEASE DETAILS -->
<div class="grid-2" style="margin-bottom:28px;">

  <!-- Pay Rent -->
  <div style="background:var(--navy3); border:1px solid var(--border); border-radius:var(--radius); padding:28px;">
    <div class="table-card-title" style="margin-bottom:20px;">💳 Pay Rent</div>
    <div style="background:var(--surface); border-radius:12px; padding:20px; margin-bottom:20px;">
      <div style="font-size:12px; font-family:var(--font-mono); color:var(--muted); margin-bottom:6px;">AMOUNT DUE — JUNE 2026</div>
      <div style="font-family:var(--font-serif); font-size:42px; font-weight:700; color:var(--gold); line-height:1;">KSh 95,000</div>
      <div style="font-size:13px; color:var(--muted); margin-top:6px;">Westlands Penthouse, Apt 2A · Due: June 1, 2026</div>
      <div style="font-size:12px; color:var(--red); margin-top:4px;">⚠ Late fee: KSh 5,000 if paid after June 1</div>
    </div>
    <div style="display:flex; flex-direction:column; gap:10px;">
      <button onclick="showToast('M-Pesa STK Push sent to +254 712 XXX XXX. Enter your PIN to complete payment.', 'green')" class="btn btn-gold" style="justify-content:center; font-size:15px; padding:14px;">
        💳 Pay via M-Pesa (STK Push)
      </button>
      <button onclick="showToast('Bank direct debit initiated. Arrives in 1-2 business days.', 'blue')" class="btn btn-outline" style="justify-content:center; font-size:14px; padding:12px;">
        🏦 Pay via Bank Account
      </button>
    </div>
    <div style="font-size:12px; color:var(--muted); text-align:center; margin-top:14px;">🔒 Payment processed through EstateYard secure portal only</div>
  </div>

  <!-- Lease Details -->
  <div style="background:var(--navy3); border:1px solid var(--border-dim); border-radius:var(--radius); padding:28px;">
    <div class="table-card-title" style="margin-bottom:20px;">📄 Current Lease</div>
    @foreach([
      ['Property','Westlands Penthouse, Apt 2A'],
      ['Landlord','James Kamau'],
      ['Lease Start','January 1, 2025'],
      ['Lease End','December 31, 2026'],
      ['Monthly Rent','KSh 95,000'],
      ['Security Bond','KSh 190,000 (in escrow)'],
      ['Payment Due','1st of every month'],
      ['Late Fee','KSh 5,000 after grace period'],
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
  </div>
</div>

<!-- PAYMENT HISTORY -->
<div class="table-card" style="margin-bottom:28px;">
  <div class="table-card-header">
    <div class="table-card-title">📋 Payment History</div>
    <button class="btn btn-sm btn-outline">📊 Export PDF</button>
  </div>
  <table class="data-table">
    <thead><tr><th>Period</th><th>Due Date</th><th>Amount</th><th>Paid On</th><th>Method</th><th>Status</th><th>Receipt</th></tr></thead>
    <tbody>
      @foreach([
        ['May 2026','1 May 2026','KSh 95,000','30 Apr 2026','M-Pesa','paid'],
        ['Apr 2026','1 Apr 2026','KSh 95,000','1 Apr 2026','Bank','paid'],
        ['Mar 2026','1 Mar 2026','KSh 95,000','28 Feb 2026','M-Pesa','paid'],
        ['Feb 2026','1 Feb 2026','KSh 95,000','3 Feb 2026','M-Pesa','paid'],
        ['Jan 2026','1 Jan 2026','KSh 95,000','1 Jan 2026','Bank','paid'],
        ['Dec 2025','1 Dec 2025','KSh 95,000','5 Dec 2025','M-Pesa','paid'],
      ] as $p)
      <tr>
        <td class="td-name">{{ $p[0] }}</td>
        <td>{{ $p[1] }}</td>
        <td class="td-price">{{ $p[2] }}</td>
        <td>{{ $p[3] }}</td>
        <td>{{ $p[4] }}</td>
        <td><span class="status-pill status-{{ $p[5] }}">{{ strtoupper($p[5]) }}</span></td>
        <td><button class="btn btn-sm btn-ghost" style="font-size:11px;">📄 Download</button></td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

<!-- MAINTENANCE -->
<div class="table-card">
  <div class="table-card-header">
    <div class="table-card-title">🔧 Maintenance Requests</div>
    <button onclick="showToast('Maintenance request submitted!', 'green')" class="btn btn-gold btn-sm">+ New Request</button>
  </div>
  <table class="data-table">
    <thead><tr><th>Issue</th><th>Submitted</th><th>Priority</th><th>Status</th></tr></thead>
    <tbody>
      <tr><td class="td-name">🚿 Shower pressure very low</td><td>2 days ago</td><td><span class="status-pill status-review">MEDIUM</span></td><td><span class="status-pill status-pending">IN PROGRESS</span></td></tr>
      <tr><td class="td-name">💡 Living room light fixture broken</td><td>1 week ago</td><td><span class="status-pill status-draft">LOW</span></td><td><span class="status-pill status-draft">SCHEDULED</span></td></tr>
    </tbody>
  </table>
</div>
@endsection
