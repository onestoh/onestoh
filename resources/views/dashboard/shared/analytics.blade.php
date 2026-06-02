@extends('layouts.dashboard')

@section('title', 'Analytics — EstateYard')

@section('content')
<div class="dash-content">

  <div style="margin-bottom:32px;">
    <h1 style="font-family:var(--font-serif); font-size:28px; color:var(--white); margin-bottom:6px;">Platform Analytics</h1>
    <p style="color:var(--muted); font-size:14px;">Revenue, bookings, and property performance overview.</p>
  </div>

  {{-- KPI Row --}}
  <div class="grid-4" style="gap:20px; margin-bottom:32px;">
    @foreach([
      ['Total Revenue','KES '.number_format($analytics['total_revenue'] ?? 0),'📈','var(--gold)'],
      ['Active Listings', $analytics['active_listings'] ?? 0,'🏠','var(--blue)'],
      ['Bookings (30d)', $analytics['bookings_30d'] ?? 0,'📅','var(--green)'],
      ['Occupancy Rate', ($analytics['occupancy_rate'] ?? 0).'%','🏨','var(--gold)'],
    ] as $kpi)
    <div class="kpi-card">
      <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px;">
        <div style="color:var(--muted); font-size:12px; font-weight:600; letter-spacing:.04em;">{{ $kpi[0] }}</div>
        <div style="font-size:20px;">{{ $kpi[2] }}</div>
      </div>
      <div style="font-size:28px; font-weight:700; font-family:var(--font-serif); color:{{ $kpi[3] }};">{{ $kpi[1] }}</div>
    </div>
    @endforeach
  </div>

  {{-- Revenue Chart --}}
  <div class="form-card" style="margin-bottom:32px;">
    <h2 style="color:var(--gold); font-family:var(--font-serif); font-size:20px; margin-bottom:24px;">Monthly Revenue (Last 6 Months)</h2>
    @php $monthlyRevenue = $analytics['monthly_revenue'] ?? []; @endphp
    <div style="display:flex; align-items:flex-end; gap:16px; height:180px; padding:0 8px;">
      @php $maxVal = max(array_column($monthlyRevenue, 'total') ?: [1]); @endphp
      @foreach($monthlyRevenue as $month)
      @php $pct = $maxVal > 0 ? round(($month['total'] / $maxVal) * 100) : 0; @endphp
      <div style="flex:1; display:flex; flex-direction:column; align-items:center; gap:8px; height:100%; justify-content:flex-end;">
        <div style="font-size:11px; color:var(--muted);">KES {{ number_format($month['total'] / 1000, 0) }}k</div>
        <div style="width:100%; background:linear-gradient(180deg, var(--gold), var(--gold2)); border-radius:6px 6px 0 0; height:{{ max($pct, 4) }}%; min-height:4px; transition:height .3s ease;"></div>
        <div style="font-size:11px; color:var(--muted); white-space:nowrap;">{{ $month['month'] }}</div>
      </div>
      @endforeach
    </div>
  </div>

  {{-- Property Performance --}}
  <div class="form-card">
    <h2 style="color:var(--gold); font-family:var(--font-serif); font-size:20px; margin-bottom:20px;">Top Performing Properties</h2>
    <div style="overflow-x:auto;">
      <table class="data-table">
        <thead>
          <tr>
            <th>Property</th>
            <th>Type</th>
            <th>Bookings</th>
            <th>Revenue</th>
            <th>Avg. Nights</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($analytics['property_performance'] ?? [] as $perf)
          <tr>
            <td style="color:var(--white); font-weight:500;">{{ Str::limit($perf['title'], 35) }}</td>
            <td><span class="badge badge-blue">{{ ucfirst($perf['listing_type']) }}</span></td>
            <td>{{ $perf['bookings'] }}</td>
            <td style="color:var(--gold);">KES {{ number_format($perf['revenue']) }}</td>
            <td>{{ number_format($perf['avg_nights'], 1) }}</td>
            <td><span class="status-pill status-{{ $perf['status'] === 'available' ? 'active' : 'inactive' }}">{{ ucfirst($perf['status']) }}</span></td>
          </tr>
          @empty
          <tr><td colspan="6" style="text-align:center; color:var(--muted); padding:32px;">No property data yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
