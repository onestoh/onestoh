@extends('layouts.dashboard')

@section('title', 'Analytics — EstateYard')
@section('page-title', 'Analytics')
@section('page-subtitle', 'Revenue, performance & property insights')

@section('content')

  @php
    $totalRentRevenue = collect($monthlyRevenue)->sum('revenue');
    $totalBookingRevenue = collect($bookingRevenue)->sum('revenue');
    $totalRevenue = $totalRentRevenue + $totalBookingRevenue;
    $maxRevenue = max(collect($monthlyRevenue)->max('revenue') ?: 1, collect($bookingRevenue)->max('revenue') ?: 1);
  @endphp

  {{-- KPI Cards --}}
  <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px,1fr)); gap:20px; margin-bottom:32px;">
    @foreach([
      ['Total Views', number_format($totalViews), '👁', 'var(--blue)'],
      ['Total Saves', number_format($totalSaves), '❤️', 'var(--red)'],
      ['Confirmed Bookings', number_format($totalBookings), '📅', 'var(--green)'],
      ['Total Revenue', 'KES ' . number_format($totalRevenue), '📈', 'var(--gold)'],
    ] as $kpi)
    <div class="kpi-card">
      <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px;">
        <div style="color:var(--muted); font-size:12px; font-weight:600; letter-spacing:.04em; text-transform:uppercase;">{{ $kpi[0] }}</div>
        <div style="font-size:20px;">{{ $kpi[2] }}</div>
      </div>
      <div style="font-size:28px; font-weight:700; font-family:var(--font-serif); color:{{ $kpi[3] }};">{{ $kpi[1] }}</div>
    </div>
    @endforeach
  </div>

  {{-- Revenue Chart --}}
  <div class="form-card" style="margin-bottom:32px;">
    <h2 style="color:var(--gold); font-family:var(--font-serif); font-size:20px; margin-bottom:24px;">Monthly Revenue — Last 6 Months</h2>
    <div style="display:flex; align-items:flex-end; gap:12px; height:200px; padding:0 8px;">
      @foreach($monthlyRevenue as $idx => $m)
      @php
        $rentPct = $maxRevenue > 0 ? round(($m['revenue'] / $maxRevenue) * 100) : 0;
        $bookPct = $maxRevenue > 0 ? round((($bookingRevenue[$idx]['revenue'] ?? 0) / $maxRevenue) * 100) : 0;
      @endphp
      <div style="flex:1; display:flex; flex-direction:column; align-items:center; gap:6px; height:100%; justify-content:flex-end;">
        <div style="font-size:10px; color:var(--muted); font-family:var(--font-mono);">
          {{ $m['revenue'] > 0 ? 'KES ' . number_format($m['revenue']/1000, 0) . 'k' : '0' }}
        </div>
        <div style="width:100%; display:flex; flex-direction:column; justify-content:flex-end; height:160px; gap:2px;">
          @if($bookPct > 0)
          <div style="width:100%; background:var(--blue); border-radius:4px 4px 0 0; height:{{ max($bookPct, 4) }}%; min-height:3px;" title="Booking revenue"></div>
          @endif
          <div style="width:100%; background:linear-gradient(180deg, var(--gold), var(--gold2)); border-radius:{{ $bookPct > 0 ? '0' : '4px 4px' }} 0 0; height:{{ max($rentPct, 4) }}%; min-height:4px;" title="Rent revenue"></div>
        </div>
        <div style="font-size:10px; color:var(--muted); white-space:nowrap; text-align:center;">{{ $m['month'] }}</div>
      </div>
      @endforeach
    </div>
    <div style="display:flex; gap:20px; margin-top:16px; font-size:12px; color:var(--muted);">
      <span style="display:flex; align-items:center; gap:6px;"><span style="width:12px; height:12px; background:var(--gold); border-radius:3px;"></span> Rent Revenue</span>
      <span style="display:flex; align-items:center; gap:6px;"><span style="width:12px; height:12px; background:var(--blue); border-radius:3px;"></span> Booking Revenue</span>
    </div>
  </div>

  {{-- Property Performance Table --}}
  <div class="form-card">
    <h2 style="color:var(--gold); font-family:var(--font-serif); font-size:20px; margin-bottom:20px;">Property Performance</h2>
    <div style="overflow-x:auto;">
      <table class="data-table">
        <thead>
          <tr>
            <th>Property</th>
            <th>Type</th>
            <th>Views</th>
            <th>Saves</th>
            <th>Bookings</th>
            <th>Occupancy</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($properties as $prop)
          @php
            $propBookings = $prop->bookings->where('status', 'confirmed');
            $bookedDays = $propBookings->sum(fn($b) => max(1, \Carbon\Carbon::parse($b->check_in)->diffInDays(\Carbon\Carbon::parse($b->check_out))));
            $totalDays = 180; // 6 months reference
            $occupancy = $totalDays > 0 ? round(($bookedDays / $totalDays) * 100) : 0;
          @endphp
          <tr>
            <td style="color:var(--white); font-weight:500;">{{ \Illuminate\Support\Str::limit($prop->title, 35) }}</td>
            <td><span class="badge badge-blue">{{ ucfirst($prop->listing_type ?? 'sale') }}</span></td>
            <td>{{ number_format($prop->view_count ?? 0) }}</td>
            <td>{{ number_format($prop->save_count ?? 0) }}</td>
            <td>{{ $propBookings->count() }}</td>
            <td>
              <div style="display:flex; align-items:center; gap:8px;">
                <div style="width:60px; height:6px; background:var(--navy3); border-radius:3px; overflow:hidden;">
                  <div style="width:{{ min($occupancy, 100) }}%; height:100%; background:{{ $occupancy > 70 ? 'var(--green)' : ($occupancy > 40 ? 'var(--gold)' : 'var(--muted)') }}; border-radius:3px;"></div>
                </div>
                <span style="font-size:12px; color:var(--muted);">{{ $occupancy }}%</span>
              </div>
            </td>
            <td><span class="status-pill status-{{ $prop->status === 'active' ? 'active' : 'draft' }}">{{ ucfirst($prop->status) }}</span></td>
          </tr>
          @empty
          <tr><td colspan="7" style="text-align:center; color:var(--muted); padding:32px;">No properties yet. <a href="{{ url('/properties/create') }}" style="color:var(--gold);">Add a listing →</a></td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

@endsection
