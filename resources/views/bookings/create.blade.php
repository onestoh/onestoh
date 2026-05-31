@extends('layouts.app')
@section('title', 'Book — ' . $property->title . ' — EstateYard')

@section('content')
<div style="padding-top:90px; background:var(--navy); min-height:100vh;">
<div class="container" style="padding:32px; max-width:900px; margin:0 auto;">

  <!-- Breadcrumb -->
  <div style="display:flex; align-items:center; gap:8px; margin-bottom:24px; font-size:13px; color:var(--muted);">
    <a href="{{ url('/marketplace') }}" style="color:var(--muted);">Marketplace</a>
    <span>/</span>
    <a href="{{ url('/marketplace/listing/' . $property->id) }}" style="color:var(--muted);">{{ $property->title }}</a>
    <span>/</span>
    <span style="color:var(--white);">Book</span>
  </div>

  <!-- Step indicator -->
  <div style="display:flex; gap:0; margin-bottom:32px; border:1px solid var(--border); border-radius:10px; overflow:hidden;">
    @foreach(['Dates & Guests', 'Your Details', 'Payment'] as $i => $step)
    <div id="step-tab-{{ $i+1 }}" onclick="goStep({{ $i+1 }})" style="flex:1; padding:14px 16px; text-align:center; font-size:13px; font-weight:600; cursor:pointer; transition:background .2s; {{ $i===0 ? 'background:var(--gold-dim); color:var(--gold);' : 'background:var(--navy3); color:var(--muted);' }} {{ $i<2 ? 'border-right:1px solid var(--border);' : '' }}">
      <span style="display:inline-block; width:22px; height:22px; border-radius:50%; background:{{ $i===0 ? 'var(--gold)' : 'var(--surface)' }}; color:{{ $i===0 ? 'var(--navy)' : 'var(--muted)' }}; font-size:11px; line-height:22px; text-align:center; margin-right:8px;">{{ $i+1 }}</span>
      {{ $step }}
    </div>
    @endforeach
  </div>

  <div style="display:grid; grid-template-columns:1fr 340px; gap:28px; align-items:start;">

    <!-- Main steps -->
    <div>
      <!-- STEP 1 -->
      <div id="step-1">
        <div style="background:var(--navy3); border:1px solid var(--border-dim); border-radius:var(--radius); padding:28px; margin-bottom:20px;">
          <h2 style="font-family:var(--font-serif); font-size:24px; color:var(--white); margin-bottom:20px;">{{ $property->title }}</h2>

          @if(count($rooms) > 0)
          <div style="margin-bottom:24px;">
            <div style="font-size:13px; font-weight:600; color:var(--muted); text-transform:uppercase; letter-spacing:1px; margin-bottom:12px;">Select Room</div>
            <div style="display:grid; gap:10px;" id="roomCards">
              @foreach($rooms as $room)
              <label style="display:flex; align-items:center; gap:14px; padding:14px; border:1px solid var(--border-dim); border-radius:10px; cursor:pointer; transition:border-color .2s;" class="room-card" data-price="{{ $room->price_per_night }}">
                <input type="radio" name="room_id" value="{{ $room->id }}" style="accent-color:var(--gold);" {{ $loop->first ? 'checked' : '' }}>
                <div style="flex:1;">
                  <div style="font-weight:600; color:var(--white);">{{ $room->name }}</div>
                  <div style="font-size:12px; color:var(--muted);">{{ ucfirst($room->room_type) }} · {{ $room->capacity }} guests · Floor {{ $room->floor ?? '—' }}</div>
                  @if($room->amenities)
                  <div style="display:flex; flex-wrap:wrap; gap:4px; margin-top:6px;">
                    @foreach(array_slice((array)$room->amenities, 0, 4) as $am)
                    <span class="chip" style="font-size:11px; padding:2px 8px;">{{ $am }}</span>
                    @endforeach
                  </div>
                  @endif
                </div>
                <div style="text-align:right;">
                  <div style="color:var(--gold); font-weight:700; font-size:16px;">KES {{ number_format($room->price_per_night) }}</div>
                  <div style="font-size:12px; color:var(--muted);">/night</div>
                </div>
              </label>
              @endforeach
            </div>
          </div>
          @endif

          <!-- Calendar -->
          <div style="margin-bottom:24px;">
            <div style="font-size:13px; font-weight:600; color:var(--muted); text-transform:uppercase; letter-spacing:1px; margin-bottom:12px;">Select Dates</div>
            <div id="calendarContainer" style="display:grid; grid-template-columns:1fr 1fr; gap:20px; overflow-x:auto;"></div>
          </div>

          <!-- Date inputs + guests -->
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px;">
            <div>
              <label class="form-label" style="font-size:11px;">CHECK-IN</label>
              <input type="date" id="checkInInput" class="form-control" value="{{ $checkIn }}" min="{{ date('Y-m-d') }}" required>
            </div>
            <div>
              <label class="form-label" style="font-size:11px;">CHECK-OUT</label>
              <input type="date" id="checkOutInput" class="form-control" value="{{ $checkOut }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
            </div>
          </div>

          <div style="border:1px solid var(--border); border-radius:8px; padding:14px; display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <div>
              <div style="font-size:10px; font-weight:600; color:var(--muted); text-transform:uppercase;">GUESTS</div>
              <span id="guestDisplay" style="font-size:14px; color:var(--white);">{{ $guests }} guest{{ $guests > 1 ? 's' : '' }}</span>
            </div>
            <div style="display:flex; gap:10px; align-items:center;">
              <button type="button" onclick="changeGuests(-1)" style="width:30px; height:30px; border-radius:50%; border:1px solid var(--border); background:none; color:var(--white); cursor:pointer; font-size:18px; line-height:1;">−</button>
              <span id="guestCount" style="color:var(--white); font-size:16px; min-width:16px; text-align:center;">{{ $guests }}</span>
              <button type="button" onclick="changeGuests(1)" style="width:30px; height:30px; border-radius:50%; border:1px solid var(--border); background:none; color:var(--white); cursor:pointer; font-size:18px; line-height:1;">+</button>
            </div>
          </div>

          <button type="button" onclick="goStep(2)" class="btn btn-gold" style="width:100%; justify-content:center; padding:14px; font-size:15px;" id="nextStep1">Next: Your Details →</button>
        </div>
      </div>

      <!-- STEP 2 -->
      <div id="step-2" style="display:none;">
        <div style="background:var(--navy3); border:1px solid var(--border-dim); border-radius:var(--radius); padding:28px; margin-bottom:20px;">
          <h2 style="font-family:var(--font-serif); font-size:22px; color:var(--white); margin-bottom:20px;">Your Details</h2>

          <div class="form-group">
            <label class="form-label">Full Name</label>
            <input type="text" class="form-control" id="guestName" value="{{ session('user_name', '') }}" placeholder="Your full name" required>
          </div>
          <div class="form-group">
            <label class="form-label">Email Address</label>
            <input type="email" class="form-control" id="guestEmail" value="{{ session('user_email', '') }}" placeholder="you@example.com" required>
          </div>
          <div class="form-group">
            <label class="form-label">Phone Number</label>
            <input type="tel" class="form-control" id="guestPhone" placeholder="+254 700 000 000">
          </div>
          <div class="form-group">
            <label class="form-label">Special Requests <span style="color:var(--muted); font-size:12px;">(optional)</span></label>
            <textarea class="form-control" id="specialRequests" rows="3" placeholder="Any special requests or notes...">{{ old('special_requests') }}</textarea>
          </div>

          <!-- House rules -->
          <div style="background:var(--surface); border-radius:10px; padding:16px; margin-bottom:20px;">
            <div style="font-size:13px; font-weight:600; color:var(--white); margin-bottom:10px;">🏠 House Rules</div>
            @foreach(['🕙 Quiet hours after 10 PM', '🚭 Non-smoking property', '🐾 No pets without prior approval', '✅ Check-in after 2:00 PM', '🔑 Check-out before 11:00 AM'] as $rule)
            <div style="font-size:13px; color:var(--muted); padding:4px 0;">{{ $rule }}</div>
            @endforeach
          </div>

          <div style="display:flex; gap:12px;">
            <button type="button" onclick="goStep(1)" class="btn btn-outline" style="flex:1; justify-content:center;">← Back</button>
            <button type="button" onclick="goStep(3)" class="btn btn-gold" style="flex:2; justify-content:center; padding:14px;">Next: Payment →</button>
          </div>
        </div>
      </div>

      <!-- STEP 3 -->
      <div id="step-3" style="display:none;">
        <div style="background:var(--navy3); border:1px solid var(--border-dim); border-radius:var(--radius); padding:28px; margin-bottom:20px;">
          <h2 style="font-family:var(--font-serif); font-size:22px; color:var(--white); margin-bottom:20px;">Confirm & Pay</h2>

          <!-- Booking summary -->
          <div style="background:var(--surface); border-radius:10px; padding:16px; margin-bottom:20px;">
            <div style="font-size:13px; font-weight:600; color:var(--white); margin-bottom:12px;">Booking Summary</div>
            <div style="font-size:13px; color:var(--muted);">📍 {{ $property->title }}</div>
            <div style="font-size:13px; color:var(--muted); margin-top:4px;" id="summaryDates">—</div>
            <div style="font-size:13px; color:var(--muted); margin-top:4px;" id="summaryGuests">—</div>
          </div>

          <!-- Payment methods -->
          <div style="font-size:13px; font-weight:600; color:var(--muted); text-transform:uppercase; letter-spacing:1px; margin-bottom:12px;">Payment Method</div>
          <div style="display:grid; gap:10px; margin-bottom:20px;">
            <label style="display:flex; align-items:center; gap:12px; padding:14px; border:1px solid var(--border-dim); border-radius:10px; cursor:pointer;" id="mpesaOption">
              <input type="radio" name="payMethod" value="mpesa" checked style="accent-color:var(--gold);">
              <div style="width:40px; height:40px; border-radius:8px; background:#00b300; display:flex; align-items:center; justify-content:center; font-size:18px;">M</div>
              <div>
                <div style="font-weight:600; color:var(--white);">M-Pesa</div>
                <div style="font-size:12px; color:var(--muted);">Lipa Na M-Pesa · STK Push</div>
              </div>
            </label>
            <label style="display:flex; align-items:center; gap:12px; padding:14px; border:1px solid var(--border-dim); border-radius:10px; cursor:pointer;">
              <input type="radio" name="payMethod" value="bank" style="accent-color:var(--gold);">
              <div style="width:40px; height:40px; border-radius:8px; background:var(--blue); display:flex; align-items:center; justify-content:center; font-size:18px;">🏦</div>
              <div>
                <div style="font-weight:600; color:var(--white);">Bank Transfer</div>
                <div style="font-size:12px; color:var(--muted);">Direct bank transfer</div>
              </div>
            </label>
          </div>

          <div id="mpesaPhoneWrap" style="margin-bottom:20px;">
            <label class="form-label">M-Pesa Phone Number</label>
            <input type="tel" id="mpesaPhone" class="form-control" placeholder="0700 000 000" value="{{ session('user_phone', '') }}">
          </div>

          <!-- Actual form -->
          <form id="bookingForm" method="POST" action="{{ url('/bookings') }}">
            @csrf
            <input type="hidden" name="property_id" value="{{ $property->id }}">
            <input type="hidden" name="check_in" id="formCheckIn">
            <input type="hidden" name="check_out" id="formCheckOut">
            <input type="hidden" name="guests_count" id="formGuests">
            <input type="hidden" name="room_id" id="formRoomId">
            <input type="hidden" name="payment_method" id="formPayMethod" value="mpesa">
            <input type="hidden" name="special_requests" id="formRequests">

            <div style="display:flex; gap:12px;">
              <button type="button" onclick="goStep(2)" class="btn btn-outline" style="flex:1; justify-content:center;">← Back</button>
              <button type="submit" onclick="prepareForm()" class="btn btn-gold" style="flex:2; justify-content:center; padding:14px; font-size:15px;" id="submitBooking">
                🔒 Confirm Booking
              </button>
            </div>
          </form>
          <p style="text-align:center; font-size:12px; color:var(--muted); margin-top:12px;">Payment processed securely · You'll confirm on the next page</p>
        </div>
      </div>
    </div>

    <!-- Price summary sidebar -->
    <div style="position:sticky; top:100px;">
      <div style="background:var(--navy3); border:1px solid var(--border); border-radius:var(--radius); padding:20px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
          <div>
            <span style="font-size:22px; font-weight:700; color:var(--gold); font-family:var(--font-serif);">KES {{ number_format($property->price) }}</span>
            <span style="color:var(--muted); font-size:13px;"> / night</span>
          </div>
          <div style="display:flex; gap:4px;">
            <span style="color:var(--gold);">★</span>
            <span style="font-size:13px; color:var(--white);">4.8</span>
          </div>
        </div>

        <div id="pricingPanel" style="display:none; background:var(--surface); border-radius:8px; padding:14px; margin-bottom:14px;">
          <div style="display:flex; justify-content:space-between; margin-bottom:8px; font-size:13px; color:var(--muted);">
            <span id="nightsLbl">0 nights</span>
            <span id="basePriceLbl">KES 0</span>
          </div>
          <div style="display:flex; justify-content:space-between; margin-bottom:8px; font-size:13px; color:var(--muted);">
            <span>Cleaning fee</span>
            <span id="cleaningLbl">KES 0</span>
          </div>
          <div style="display:flex; justify-content:space-between; margin-bottom:8px; font-size:13px; color:var(--muted);">
            <span>Service fee (5%)</span>
            <span id="serviceLbl">KES 0</span>
          </div>
          <div style="display:flex; justify-content:space-between; padding-top:12px; border-top:1px solid var(--border); font-size:15px; font-weight:600; color:var(--white); margin-top:4px;">
            <span>Total</span>
            <span id="totalLbl" style="color:var(--gold);">KES 0</span>
          </div>
        </div>

        <div id="selectDatesMsg" style="text-align:center; font-size:13px; color:var(--muted); padding:16px 0;">
          Select check-in and check-out dates to see pricing
        </div>
      </div>
    </div>
  </div>
</div>
</div>
@endsection

@push('scripts')
<script>
const PROPERTY_ID = {{ $property->id }};
const BASE_PRICE = {{ $property->price ?? 0 }};
let blockedDates = [];
let checkInDate = '{{ $checkIn }}';
let checkOutDate = '{{ $checkOut }}';
let guestCount = {{ $guests }};
let calendarStartMonth = new Date();
calendarStartMonth.setDate(1);

// Load blocked dates
fetch('/bookings/availability/' + PROPERTY_ID)
  .then(r => r.json())
  .then(d => {
    blockedDates = d;
    renderCalendars();
  });

function renderCalendars() {
  const container = document.getElementById('calendarContainer');
  container.innerHTML = '';
  for (let i = 0; i < 2; i++) {
    let m = new Date(calendarStartMonth);
    m.setMonth(m.getMonth() + i);
    container.appendChild(buildCalendar(m));
  }
}

function buildCalendar(monthDate) {
  const div = document.createElement('div');
  const year = monthDate.getFullYear();
  const month = monthDate.getMonth();
  const monthName = monthDate.toLocaleDateString('en-US', {month:'long', year:'numeric'});

  let html = `<div>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
      <span style="font-size:13px;font-weight:600;color:var(--white);">${monthName}</span>
    </div>
    <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:2px;text-align:center;">`;

  ['Su','Mo','Tu','We','Th','Fr','Sa'].forEach(d => {
    html += `<div style="font-size:10px;font-weight:600;color:var(--muted);padding:4px;">${d}</div>`;
  });

  const firstDay = new Date(year, month, 1).getDay();
  const daysInMonth = new Date(year, month + 1, 0).getDate();

  for (let i = 0; i < firstDay; i++) {
    html += `<div></div>`;
  }

  for (let d = 1; d <= daysInMonth; d++) {
    const dateStr = `${year}-${String(month+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
    const isBlocked = blockedDates.includes(dateStr);
    const isCheckIn = dateStr === checkInDate;
    const isCheckOut = dateStr === checkOutDate;
    const isInRange = checkInDate && checkOutDate && dateStr > checkInDate && dateStr < checkOutDate;
    const isPast = dateStr < new Date().toISOString().slice(0,10);

    let bg = 'transparent';
    let color = 'var(--text)';
    let cursor = 'pointer';
    let textDecoration = 'none';
    let border = '1px solid transparent';

    if (isPast || isBlocked) {
      color = 'var(--muted2)';
      cursor = 'not-allowed';
      if (isBlocked) { textDecoration = 'line-through'; color = 'var(--red)'; }
    } else if (isCheckIn || isCheckOut) {
      bg = 'var(--gold)';
      color = 'var(--navy)';
      border = '1px solid var(--gold)';
    } else if (isInRange) {
      bg = 'var(--gold-dim)';
      color = 'var(--gold2)';
    }

    const clickable = !isPast && !isBlocked;
    html += `<div onclick="${clickable ? `pickDate('${dateStr}')` : ''}"
      style="padding:6px 2px;font-size:12px;border-radius:6px;background:${bg};color:${color};cursor:${cursor};text-decoration:${textDecoration};border:${border};transition:background .15s;"
      onmouseover="${clickable ? `this.style.background=this.style.background||'var(--surface)'` : ''}"
    >${d}</div>`;
  }

  html += `</div></div>`;
  div.innerHTML = html;
  return div;
}

function pickDate(dateStr) {
  if (!checkInDate || (checkInDate && checkOutDate)) {
    checkInDate = dateStr;
    checkOutDate = '';
    document.getElementById('checkInInput').value = dateStr;
    document.getElementById('checkOutInput').value = '';
  } else {
    if (dateStr <= checkInDate) {
      checkInDate = dateStr;
      document.getElementById('checkInInput').value = dateStr;
    } else {
      checkOutDate = dateStr;
      document.getElementById('checkOutInput').value = dateStr;
      updatePricing();
    }
  }
  renderCalendars();
}

document.getElementById('checkInInput').addEventListener('change', function() {
  checkInDate = this.value;
  renderCalendars();
  if (checkOutDate) updatePricing();
});
document.getElementById('checkOutInput').addEventListener('change', function() {
  checkOutDate = this.value;
  renderCalendars();
  if (checkInDate) updatePricing();
});

function updatePricing() {
  if (!checkInDate || !checkOutDate) return;
  fetch(`/api/search/availability?property_id=${PROPERTY_ID}&check_in=${checkInDate}&check_out=${checkOutDate}`)
    .then(r => r.json())
    .then(d => {
      const p = d.pricing;
      document.getElementById('pricingPanel').style.display = 'block';
      document.getElementById('selectDatesMsg').style.display = 'none';
      document.getElementById('nightsLbl').textContent = `KES ${p.base_total.toLocaleString()} (${p.nights} night${p.nights!==1?'s':''})`;
      document.getElementById('basePriceLbl').textContent = 'KES ' + p.base_total.toLocaleString();
      document.getElementById('cleaningLbl').textContent = 'KES ' + p.cleaning_fee.toLocaleString();
      document.getElementById('serviceLbl').textContent = 'KES ' + p.service_fee.toLocaleString();
      document.getElementById('totalLbl').textContent = 'KES ' + p.total.toLocaleString();

      // Also update step 3 summary
      document.getElementById('summaryDates').textContent = '📅 ' + checkInDate + ' → ' + checkOutDate + ' (' + p.nights + ' nights)';
      document.getElementById('summaryGuests').textContent = '👥 ' + guestCount + ' guest' + (guestCount>1?'s':'');
    });
}

function changeGuests(delta) {
  guestCount = Math.max(1, Math.min(20, guestCount + delta));
  document.getElementById('guestCount').textContent = guestCount;
  document.getElementById('guestDisplay').textContent = guestCount + ' guest' + (guestCount>1?'s':'');
}

function goStep(n) {
  for (let i = 1; i <= 3; i++) {
    const el = document.getElementById('step-' + i);
    if (el) el.style.display = i === n ? 'block' : 'none';
    const tab = document.getElementById('step-tab-' + i);
    if (tab) {
      tab.style.background = i === n ? 'var(--gold-dim)' : 'var(--navy3)';
      tab.style.color = i === n ? 'var(--gold)' : 'var(--muted)';
    }
  }
}

// Payment method toggle
document.querySelectorAll('input[name="payMethod"]').forEach(r => {
  r.addEventListener('change', function() {
    document.getElementById('mpesaPhoneWrap').style.display = this.value === 'mpesa' ? 'block' : 'none';
    document.getElementById('formPayMethod').value = this.value;
  });
});

function prepareForm() {
  document.getElementById('formCheckIn').value = checkInDate;
  document.getElementById('formCheckOut').value = checkOutDate;
  document.getElementById('formGuests').value = guestCount;
  document.getElementById('formRequests').value = document.getElementById('specialRequests').value;
  document.getElementById('formPayMethod').value = document.querySelector('input[name="payMethod"]:checked').value;

  // room
  const roomSel = document.querySelector('input[name="room_id"]:checked');
  if (roomSel) document.getElementById('formRoomId').value = roomSel.value;
}

// Init
if (checkInDate && checkOutDate) updatePricing();
</script>
@endpush
