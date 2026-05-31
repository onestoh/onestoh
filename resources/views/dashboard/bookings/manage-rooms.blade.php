@extends('layouts.dashboard')
@section('title', 'Manage Hotel Rooms — EstateYard')
@section('page-title', 'Hotel Room Management')
@section('page-subtitle', 'Manage rooms, pricing rules, and availability')

@section('content')

<div style="display:flex; gap:0; border:1px solid var(--border); border-radius:10px; overflow:hidden; margin-bottom:28px;">
  @foreach(['rooms' => '🛏 Rooms', 'pricing' => '💰 Pricing Rules', 'block' => '🚫 Block Dates'] as $key => $label)
  <div onclick="showRoomTab('{{ $key }}')" id="rtab-{{ $key }}" style="flex:1; padding:13px 16px; text-align:center; font-size:13px; font-weight:600; cursor:pointer; transition:background .2s; {{ $key==='rooms' ? 'background:var(--gold-dim); color:var(--gold);' : 'background:var(--navy3); color:var(--muted);' }} {{ !$loop->last ? 'border-right:1px solid var(--border);' : '' }}">
    {{ $label }}
  </div>
  @endforeach
</div>

<!-- ROOMS TAB -->
<div id="rtabcontent-rooms">
  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <div style="font-size:16px; font-weight:600; color:var(--white);">Hotel Rooms</div>
    <button onclick="document.getElementById('addRoomModal').style.display='flex'" class="btn btn-gold">+ Add Room</button>
  </div>

  <div id="roomsList" style="display:grid; gap:16px;">
    <div style="text-align:center; padding:48px; color:var(--muted);">
      <div style="font-size:48px; margin-bottom:12px;">🛏</div>
      <div>No rooms added yet. Click "Add Room" to get started.</div>
    </div>
  </div>
</div>

<!-- PRICING RULES TAB -->
<div id="rtabcontent-pricing" style="display:none;">
  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <div style="font-size:16px; font-weight:600; color:var(--white);">Pricing Rules</div>
    <button onclick="document.getElementById('addPricingModal').style.display='flex'" class="btn btn-gold">+ Add Rule</button>
  </div>
  <div class="table-card">
    <table class="data-table">
      <thead><tr><th>Label</th><th>Day/Date Range</th><th>Price/Night</th><th>Min Nights</th><th>Priority</th><th>Actions</th></tr></thead>
      <tbody id="pricingRulesBody">
        <tr><td colspan="6" style="text-align:center; color:var(--muted); padding:24px;">No pricing rules configured.</td></tr>
      </tbody>
    </table>
  </div>
</div>

<!-- BLOCK DATES TAB -->
<div id="rtabcontent-block" style="display:none;">
  <div style="font-size:16px; font-weight:600; color:var(--white); margin-bottom:16px;">Block/Unblock Dates</div>
  <div style="background:var(--navy3); border:1px solid var(--border-dim); border-radius:var(--radius); padding:24px;">
    <p style="color:var(--muted); font-size:14px; margin-bottom:20px;">Click on dates to block them as owner-blocked. Click again to unblock.</p>
    <div id="blockCalendar" style="display:grid; grid-template-columns:1fr 1fr; gap:24px;"></div>
    <div style="margin-top:16px; display:flex; gap:12px; font-size:12px;">
      <span style="display:flex; align-items:center; gap:6px;"><span style="width:12px; height:12px; background:var(--red); border-radius:3px;"></span> Booked</span>
      <span style="display:flex; align-items:center; gap:6px;"><span style="width:12px; height:12px; background:var(--orange); border-radius:3px;"></span> Owner Blocked</span>
      <span style="display:flex; align-items:center; gap:6px;"><span style="width:12px; height:12px; background:var(--surface); border:1px solid var(--border); border-radius:3px;"></span> Available</span>
    </div>
  </div>
</div>

<!-- Add Room Modal -->
<div id="addRoomModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); z-index:9999; align-items:center; justify-content:center;">
  <div style="background:var(--navy2); border:1px solid var(--border); border-radius:var(--radius); padding:32px; max-width:500px; width:90%; max-height:90vh; overflow-y:auto;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
      <h3 style="font-size:18px; font-weight:600; color:var(--white); font-family:var(--font-serif);">Add Hotel Room</h3>
      <button onclick="document.getElementById('addRoomModal').style.display='none'" style="background:none; border:none; color:var(--muted); font-size:20px; cursor:pointer;">✕</button>
    </div>
    <form id="addRoomForm">
      <div class="form-group">
        <label class="form-label">Room Number</label>
        <input type="text" class="form-control" id="rRoomNum" placeholder="101" required>
      </div>
      <div class="form-group">
        <label class="form-label">Room Type</label>
        <select class="form-control" id="rRoomType">
          @foreach(['standard','deluxe','suite','penthouse','family','single','double','twin'] as $t)
          <option value="{{ $t }}">{{ ucfirst($t) }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Room Name</label>
        <input type="text" class="form-control" id="rName" placeholder="Deluxe Sea View Room" required>
      </div>
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
        <div class="form-group">
          <label class="form-label">Price per Night (KES)</label>
          <input type="number" class="form-control" id="rPrice" placeholder="15000" required>
        </div>
        <div class="form-group">
          <label class="form-label">Capacity</label>
          <input type="number" class="form-control" id="rCapacity" placeholder="2" min="1" value="2">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Floor</label>
        <input type="number" class="form-control" id="rFloor" placeholder="1">
      </div>
      <div class="form-group">
        <label class="form-label">Amenities</label>
        <div style="display:flex; flex-wrap:wrap; gap:8px;">
          @foreach(['WiFi','AC','TV','Minibar','Safe','Balcony','Sea View','Jacuzzi','King Bed','Work Desk'] as $am)
          <label style="display:flex; align-items:center; gap:6px; cursor:pointer; font-size:13px; color:var(--muted);">
            <input type="checkbox" name="amenities[]" value="{{ $am }}" style="accent-color:var(--gold);"> {{ $am }}
          </label>
          @endforeach
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Description <span style="color:var(--muted); font-size:12px;">(optional)</span></label>
        <textarea class="form-control" id="rDesc" rows="2" placeholder="Room description..."></textarea>
      </div>
      <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center; padding:12px;">Add Room</button>
    </form>
  </div>
</div>

<!-- Add Pricing Rule Modal -->
<div id="addPricingModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); z-index:9999; align-items:center; justify-content:center;">
  <div style="background:var(--navy2); border:1px solid var(--border); border-radius:var(--radius); padding:32px; max-width:480px; width:90%;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
      <h3 style="font-size:18px; font-weight:600; color:var(--white); font-family:var(--font-serif);">Add Pricing Rule</h3>
      <button onclick="document.getElementById('addPricingModal').style.display='none'" style="background:none; border:none; color:var(--muted); font-size:20px; cursor:pointer;">✕</button>
    </div>
    <form id="addPricingForm">
      <div class="form-group">
        <label class="form-label">Label</label>
        <input type="text" class="form-control" id="pLabel" placeholder="Weekend Rate">
      </div>
      <div class="form-group">
        <label class="form-label">Price per Night (KES)</label>
        <input type="number" class="form-control" id="pPrice" placeholder="20000" required>
      </div>
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
        <div class="form-group">
          <label class="form-label">Day of Week <span style="color:var(--muted); font-size:11px;">(0=Sun)</span></label>
          <select class="form-control" id="pDow">
            <option value="">Any day</option>
            @foreach(['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $i => $d)
            <option value="{{ $i }}">{{ $d }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Min Nights</label>
          <input type="number" class="form-control" id="pMinNights" value="1" min="1">
        </div>
      </div>
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
        <div class="form-group">
          <label class="form-label">Date From</label>
          <input type="date" class="form-control" id="pDateFrom">
        </div>
        <div class="form-group">
          <label class="form-label">Date To</label>
          <input type="date" class="form-control" id="pDateTo">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Priority</label>
        <input type="number" class="form-control" id="pPriority" value="0" min="0">
      </div>
      <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center; padding:12px;">Add Rule</button>
    </form>
  </div>
</div>

@endsection

@push('scripts')
<script>
const PROPERTY_ID = {{ request()->route('propertyId') ?? 0 }};

function showRoomTab(name) {
  ['rooms','pricing','block'].forEach(t => {
    document.getElementById('rtabcontent-' + t).style.display = t === name ? 'block' : 'none';
    const tab = document.getElementById('rtab-' + t);
    if (tab) {
      tab.style.background = t === name ? 'var(--gold-dim)' : 'var(--navy3)';
      tab.style.color = t === name ? 'var(--gold)' : 'var(--muted)';
    }
  });
}
</script>
@endpush
