@extends('layouts.dashboard')
@section('title', 'Host Bookings — EstateYard')
@section('page-title', 'Incoming Bookings')
@section('page-subtitle', 'All bookings for your properties')

@section('content')

@if(session('success'))
<div class="alert alert-green" style="margin-bottom:20px;">{{ session('success') }}</div>
@endif

<!-- Status filter + Export -->
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
  <div style="display:flex; gap:8px; flex-wrap:wrap;" id="statusFilters">
    @foreach(['all' => 'All', 'pending' => 'Pending', 'confirmed' => 'Confirmed', 'checked_in' => 'Checked In', 'checked_out' => 'Completed', 'cancelled' => 'Cancelled'] as $val => $label)
    <button onclick="filterBookings('{{ $val }}')" id="filter-{{ $val }}" class="btn btn-sm {{ $val==='all' ? 'btn-gold' : 'btn-outline' }}">{{ $label }}</button>
    @endforeach
  </div>
  <button onclick="exportCSV()" class="btn btn-sm btn-outline">⬇️ Export CSV</button>
</div>

@if($bookings->isEmpty())
<div style="text-align:center; padding:80px 32px; color:var(--muted);">
  <div style="font-size:60px; margin-bottom:16px;">📭</div>
  <div style="font-size:18px; font-weight:600; color:var(--white); margin-bottom:8px;">No bookings yet</div>
  <p style="font-size:14px;">When guests book your properties, they'll appear here.</p>
</div>
@else
<div class="table-card">
  <div class="table-card-header">
    <div class="table-card-title">📋 All Bookings ({{ $bookings->count() }})</div>
  </div>
  <div style="overflow-x:auto;">
    <table class="data-table" id="bookingsTable">
      <thead>
        <tr>
          <th>Ref</th>
          <th>Guest</th>
          <th>Property</th>
          <th>Room</th>
          <th>Check-in</th>
          <th>Check-out</th>
          <th>Nights</th>
          <th>Total</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($bookings as $booking)
        @php
          $statusColors = ['pending'=>'gold','confirmed'=>'green','paid'=>'green','checked_in'=>'blue','checked_out'=>'muted','cancelled'=>'red','refunded'=>'orange'];
          $sc = $statusColors[$booking->status] ?? 'muted';
        @endphp
        <tr data-status="{{ $booking->status }}">
          <td><span style="font-family:var(--font-mono); font-size:11px; color:var(--gold);">BOOK-{{ $booking->id }}</span></td>
          <td>
            <div style="font-weight:600; color:var(--white);">{{ $booking->guest->name ?? 'Guest' }}</div>
            <div style="font-size:11px; color:var(--muted);">{{ $booking->guest->email ?? '' }}</div>
          </td>
          <td>
            <div style="font-size:13px; color:var(--white);">{{ Str::limit($booking->property->title ?? '—', 30) }}</div>
          </td>
          <td>
            <span style="font-size:12px; color:var(--muted);">{{ $booking->room->name ?? '—' }}</span>
          </td>
          <td style="font-family:var(--font-mono); font-size:12px;">{{ \Carbon\Carbon::parse($booking->check_in)->format('d M Y') }}</td>
          <td style="font-family:var(--font-mono); font-size:12px;">{{ \Carbon\Carbon::parse($booking->check_out)->format('d M Y') }}</td>
          <td style="text-align:center;">{{ $booking->nights }}</td>
          <td style="color:var(--gold); font-weight:600;">KES {{ number_format($booking->total_price) }}</td>
          <td>
            <span style="background:rgba(100,100,100,0.15); color:var(--{{ $sc }}); border:1px solid rgba(100,100,100,0.2); padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600; white-space:nowrap;">{{ strtoupper($booking->status) }}</span>
          </td>
          <td>
            <div style="display:flex; gap:6px;">
              <a href="{{ route('booking.confirmation', $booking->id) }}" class="btn btn-sm btn-outline">View</a>
              @if(in_array($booking->status, ['pending','confirmed']))
              <form method="POST" action="{{ route('booking.cancel', $booking->id) }}" onsubmit="return confirm('Cancel booking?')">
                @csrf
                <button type="submit" class="btn btn-sm" style="background:rgba(224,82,82,0.1); color:var(--red); border:1px solid rgba(224,82,82,0.3);">Cancel</button>
              </form>
              @endif
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif

@endsection

@push('scripts')
<script>
function filterBookings(status) {
  document.querySelectorAll('#statusFilters .btn').forEach(btn => {
    btn.className = btn.id === 'filter-' + status ? 'btn btn-sm btn-gold' : 'btn btn-sm btn-outline';
  });
  document.querySelectorAll('#bookingsTable tbody tr').forEach(row => {
    row.style.display = status === 'all' || row.dataset.status === status ? '' : 'none';
  });
}

function exportCSV() {
  const rows = [['Ref','Guest','Property','Check-in','Check-out','Nights','Total','Status']];
  document.querySelectorAll('#bookingsTable tbody tr').forEach(row => {
    const cells = row.querySelectorAll('td');
    if (cells.length >= 9 && row.style.display !== 'none') {
      rows.push([
        cells[0].textContent.trim(),
        cells[1].textContent.trim().replace(/\n/g,' '),
        cells[2].textContent.trim(),
        cells[4].textContent.trim(),
        cells[5].textContent.trim(),
        cells[6].textContent.trim(),
        cells[7].textContent.trim(),
        cells[8].textContent.trim(),
      ]);
    }
  });
  const csv = rows.map(r => r.map(c => '"' + c.replace(/"/g,'""') + '"').join(',')).join('\n');
  const blob = new Blob([csv], {type:'text/csv'});
  const a = document.createElement('a');
  a.href = URL.createObjectURL(blob);
  a.download = 'estateyard-bookings.csv';
  a.click();
}
</script>
@endpush
