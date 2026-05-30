@extends('layouts.app')
@section('title', '4-Bed Villa with Pool, Karen — EstateYard')
@section('content')
<div style="padding-top:90px; background:var(--navy); min-height:100vh;">
  <div class="container" style="padding:32px;">

    <!-- Breadcrumb -->
    <div style="display:flex; align-items:center; gap:8px; margin-bottom:20px; font-size:13px; color:var(--muted);">
      <a href="{{ url('/marketplace') }}" style="color:var(--muted);">Marketplace</a>
      <span>/</span><span>Residential</span><span>/</span><span>For Sale</span><span>/</span>
      <span style="color:var(--white);">4-Bed Villa, Karen</span>
    </div>

    <div style="display:grid; grid-template-columns:1fr 380px; gap:32px; align-items:start;">

      <!-- Left -->
      <div>
        <!-- Gallery -->
        <div style="background:var(--navy3); border:1px solid var(--border-dim); border-radius:var(--radius); overflow:hidden; margin-bottom:24px;">
          <div style="height:380px; background:linear-gradient(135deg,rgba(212,168,67,0.05),var(--navy3)); display:flex; align-items:center; justify-content:center; font-size:100px; position:relative;">
            🏠
            <div style="position:absolute; top:16px; left:16px; display:flex; gap:8px;">
              <span class="badge badge-green">✓ Verified</span>
              <span class="badge badge-gold">For Sale</span>
              <span class="badge badge-blue">Featured</span>
            </div>
            <div style="position:absolute; bottom:16px; right:16px; display:flex; gap:8px;">
              <button class="btn btn-sm btn-outline">📷 24 Photos</button>
              <button class="btn btn-sm btn-outline">🎬 Virtual Tour</button>
              <button class="btn btn-sm btn-outline">🗺 Map</button>
            </div>
          </div>
          <!-- Thumbnails -->
          <div style="display:flex; gap:8px; padding:12px; overflow-x:auto;">
            @foreach(['🏠','🛏','🚿','🍳','🌊','🌳','🚗','🛋'] as $t)
            <div style="width:72px; height:56px; flex-shrink:0; background:var(--surface); border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:24px; cursor:pointer; border:2px solid {{ $loop->first ? 'var(--gold)' : 'transparent' }};">{{ $t }}</div>
            @endforeach
          </div>
        </div>

        <!-- Details -->
        <div style="background:var(--navy3); border:1px solid var(--border-dim); border-radius:var(--radius); padding:28px; margin-bottom:24px;">
          <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:16px; margin-bottom:20px;">
            <div>
              <div class="section-tag">Residential · For Sale</div>
              <h1 style="font-family:var(--font-serif); font-size:clamp(28px,4vw,42px); font-weight:700; color:var(--white); line-height:1.1; margin:8px 0;">4-Bedroom Villa with Pool</h1>
              <div style="font-size:14px; color:var(--muted);">📍 Karen, Nairobi · Property ID: EY-2024-KA-0041</div>
            </div>
            <div style="text-align:right;">
              <div style="font-family:var(--font-serif); font-size:42px; font-weight:700; color:var(--gold); line-height:1;">KSh 28.5M</div>
              <div style="font-size:12px; color:var(--muted);">Negotiable · Escrow protected</div>
            </div>
          </div>

          <div style="display:flex; gap:20px; flex-wrap:wrap; padding:16px; background:var(--surface); border-radius:10px; margin-bottom:20px;">
            @foreach([['🛏','4 Bedrooms'],['🚿','3 Bathrooms'],['📐','340m²'],['🚗','2 Car Garage'],['🌊','Swimming Pool'],['🌳','Large Garden']] as $spec)
            <div style="text-align:center;">
              <div style="font-size:20px;">{{ $spec[0] }}</div>
              <div style="font-size:12px; color:var(--muted); margin-top:4px; font-family:var(--font-mono);">{{ $spec[1] }}</div>
            </div>
            @endforeach
          </div>

          <div class="tabs" style="margin-bottom:24px;">
            <a href="#" class="tab-item active">Overview</a>
            <a href="#" class="tab-item">Amenities</a>
            <a href="#" class="tab-item">Floor Plan</a>
            <a href="#" class="tab-item">Location</a>
            <a href="#" class="tab-item">Documents</a>
          </div>

          <p style="color:var(--muted); font-size:14px; line-height:1.8; margin-bottom:16px;">
            Stunning 4-bedroom villa located in the prestigious Karen suburb of Nairobi. This beautifully designed property sits on a generous 0.75-acre plot, featuring lush tropical gardens, a heated swimming pool, and breathtaking views of the Ngong Hills.
          </p>
          <p style="color:var(--muted); font-size:14px; line-height:1.8;">
            The main house has an open-plan kitchen and living area, formal dining room, master bedroom with en-suite and walk-in wardrobe, three additional bedrooms each with en-suite bathrooms, a study/home office, and a domestic staff quarters. 2-car garage with extra parking for 4 vehicles.
          </p>

          <div style="margin-top:20px;">
            <div class="section-tag" style="margin-bottom:12px;">Amenities</div>
            <div style="display:flex; gap:8px; flex-wrap:wrap;">
              @foreach(['🌊 Heated Pool','🌳 Garden','🚗 2-Car Garage','🔒 24hr Security','💡 Backup Generator','🌐 Fibre Internet','🛁 Jacuzzi','🏋️ Gym Room','🌴 Outdoor BBQ','🐕 Pet Friendly'] as $am)
              <span class="chip">{{ $am }}</span>
              @endforeach
            </div>
          </div>
        </div>

        <!-- Map Placeholder -->
        <div class="map-container" style="margin-bottom:24px;">
          <div class="map-placeholder">
            <div class="icon">🗺</div>
            <div style="font-size:16px; color:var(--white); font-weight:600; margin-bottom:8px;">Karen, Nairobi</div>
            <div style="font-size:13px;">GPS Coordinates: -1.3427° S, 36.6952° E</div>
          </div>
        </div>

        <!-- Nearby -->
        <div style="background:var(--navy3); border:1px solid var(--border-dim); border-radius:var(--radius); padding:24px;">
          <h3 style="font-size:15px; font-weight:600; color:var(--white); margin-bottom:16px;">📍 Nearby</h3>
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
            @foreach([['🏫','Karen C Primary','0.8km'],['🏥','Karen Hospital','1.2km'],['🛒','Karen Hub Mall','0.5km'],['🚌','Karen Bus Terminal','0.9km'],['⛽','Total Petrol Station','0.3km'],['🌿','Karen Park','0.2km']] as $n)
            <div style="display:flex; align-items:center; gap:10px; font-size:13px; color:var(--muted);">
              <span>{{ $n[0] }}</span><span>{{ $n[1] }}</span><span style="color:var(--gold); font-family:var(--font-mono); font-size:11px; margin-left:auto;">{{ $n[2] }}</span>
            </div>
            @endforeach
          </div>
        </div>
      </div>

      <!-- Right -->
      <div style="position:sticky; top:100px;">

        <!-- Referral Link -->
        <div style="background:var(--navy3); border:1px solid var(--border); border-radius:var(--radius); padding:20px; margin-bottom:16px;">
          <div style="display:flex; align-items:center; gap:8px; margin-bottom:12px;">
            <span>🔗</span>
            <span style="font-size:13px; font-weight:600; color:var(--white);">Referral Link</span>
            <span class="badge badge-gold" style="font-size:10px;">KSh 50,000 commission</span>
          </div>
          <div class="referral-link-box" style="margin-bottom:10px;">
            <span class="referral-link-url">estateyard.com/ref/EY-2024-KA-0041?ref=...</span>
            <button class="btn btn-gold btn-sm" data-copy="https://estateyard.com/ref/EY-2024-KA-0041">Copy</button>
          </div>
          <div style="font-size:12px; color:var(--muted);">Share this link and earn KSh 50,000 when a deal closes via your referral</div>
        </div>

        <!-- Contact / Book -->
        <div style="background:var(--navy2); border:1px solid var(--border); border-radius:var(--radius); padding:24px; margin-bottom:16px;">
          <h3 style="font-size:16px; font-weight:600; color:var(--white); margin-bottom:16px;">Book an Inspection</h3>
          <form data-validate>
            <div class="form-group">
              <label class="form-label">Your Name</label>
              <input type="text" class="form-control" placeholder="Full name" required>
            </div>
            <div class="form-group">
              <label class="form-label">Phone Number</label>
              <input type="tel" class="form-control" placeholder="+254 700 000 000" required>
            </div>
            <div class="form-group">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" placeholder="you@example.com" required>
            </div>
            <div class="form-group">
              <label class="form-label">Preferred Date</label>
              <input type="date" class="form-control" required>
            </div>
            <div class="form-group">
              <label class="form-label">Preferred Time</label>
              <select class="form-control">
                <option>9:00 AM</option><option>10:00 AM</option><option>11:00 AM</option>
                <option>2:00 PM</option><option>3:00 PM</option><option>4:00 PM</option>
              </select>
            </div>
            <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center; margin-bottom:10px;" onclick="showToast('Inspection request submitted!', 'green'); return false;">📅 Book Inspection</button>
            <button type="button" class="btn btn-outline" style="width:100%; justify-content:center;">💬 Message Agent</button>
          </form>
        </div>

        <!-- Agent Info -->
        <div style="background:var(--navy3); border:1px solid var(--border-dim); border-radius:var(--radius); padding:20px; margin-bottom:16px;">
          <div class="section-tag" style="margin-bottom:12px;">Listed By</div>
          <div style="display:flex; align-items:center; gap:12px; margin-bottom:14px;">
            <div style="width:52px; height:52px; border-radius:50%; background:var(--gold-dim); border:2px solid var(--gold); display:flex; align-items:center; justify-content:center; font-size:22px;">👨‍💼</div>
            <div>
              <div style="font-size:15px; font-weight:600; color:var(--white);">John Kariuki</div>
              <div class="verified-badge">✓ LICENSED BROKER</div>
              <div style="font-size:12px; color:var(--muted); margin-top:2px;">42 listings · 128 deals closed</div>
            </div>
          </div>
          <div style="display:flex; gap:8px;">
            <button class="btn btn-outline btn-sm" style="flex:1; justify-content:center;">📞 Call</button>
            <button class="btn btn-outline btn-sm" style="flex:1; justify-content:center;">💬 Chat</button>
            <button class="btn btn-outline btn-sm" style="flex:1; justify-content:center;">📧 Email</button>
          </div>
        </div>

        <!-- Escrow Info -->
        <div style="background:rgba(46,204,138,0.05); border:1px solid rgba(46,204,138,0.2); border-radius:var(--radius); padding:18px;">
          <div style="display:flex; align-items:center; gap:8px; margin-bottom:10px;">
            <span>🔒</span>
            <span style="font-size:13px; font-weight:600; color:var(--green);">Escrow Protected Transaction</span>
          </div>
          <p style="font-size:12px; color:var(--muted); line-height:1.6;">All payments go through EstateYard's regulated escrow engine. Funds are only released when all conditions — title transfer, valuation, and legal checks — are fully met by both parties.</p>
        </div>
      </div>
    </div>

    <!-- Related Properties -->
    <div style="margin-top:60px;">
      <h2 style="font-size:22px; font-weight:600; color:var(--white); margin-bottom:24px;">Similar Properties in Karen</h2>
      <div class="property-grid">
        @foreach([['🏡','For Sale','3-Bed Home, Karen','Karen','3','2','240m²','KSh 22M'],['🏠','For Sale','5-Bed Villa, Langata','Langata','5','4','500m²','KSh 42M'],['🏘️','For Rent','4-Bed House','Karen Road','4','3','320m²','KSh 120K/mo']] as $r)
        <div class="property-card">
          <div class="property-card-img"><span>{{ $r[0] }}</span><div class="property-card-badges"><span class="badge badge-{{ $r[1]==='For Sale' ? 'green' : 'blue' }}">{{ $r[1] }}</span><span class="badge badge-green">✓ Verified</span></div></div>
          <div class="property-card-body">
            <div class="property-card-title">{{ $r[2] }}</div>
            <div class="property-card-location">📍 {{ $r[3] }}</div>
            <div class="property-card-specs"><span class="property-card-spec">🛏 {{ $r[4] }}</span><span class="property-card-spec">🚿 {{ $r[5] }}</span><span class="property-card-spec">📐 {{ $r[6] }}</span></div>
            <div class="property-card-footer"><div class="property-card-price">{{ $r[7] }}</div><a href="#" class="btn btn-gold btn-sm">View</a></div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</div>
@endsection
