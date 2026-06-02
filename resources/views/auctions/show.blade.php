@extends('layouts.app')
@section('title', 'Live Auction — EstateYard')
@section('content')
<div style="padding-top:90px; background:var(--navy); min-height:100vh;">
  <div class="container" style="padding:40px 32px;">
    <div style="display:grid; grid-template-columns:1fr 380px; gap:32px; align-items:start;">

      <!-- Left -->
      <div>
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:20px;">
          <a href="{{ url('/auctions') }}" style="color:var(--muted); font-size:13px;">← All Auctions</a>
          <span style="color:var(--muted);">/</span>
          <span style="font-size:13px; color:var(--muted);">Auction #{{ $auction->id }}</span>
        </div>

        <!-- Property Info -->
        <div style="background:var(--navy3); border:1px solid rgba(224,82,82,0.25); border-radius:var(--radius); overflow:hidden; margin-bottom:24px;">
          <div style="height:280px; background:linear-gradient(135deg,rgba(224,82,82,0.08),var(--navy3)); display:flex; align-items:center; justify-content:center; font-size:80px; position:relative;">
            🏢
            <div style="position:absolute; top:16px; left:16px; display:flex; gap:8px;">
              <span class="badge badge-red">🔴 LIVE AUCTION</span>
              <span class="badge badge-green">✓ Verified</span>
            </div>
          </div>
          <div style="padding:24px;">
            <h1 style="font-family:var(--font-serif); font-size:32px; font-weight:700; color:var(--white); margin-bottom:8px;">{{ optional($auction->property)->title ?? 'Auction Property' }}</h1>
            <div style="font-size:14px; color:var(--muted); margin-bottom:16px;">📍 {{ optional($auction->property)->location ?? '' }}, {{ optional($auction->property)->county ?? '' }}</div>
            <div style="display:flex; gap:20px; flex-wrap:wrap;">
              <div class="property-card-spec">🏢 {{ optional($auction->property)->type ?? 'Property' }}</div>
              <div class="property-card-spec">🔨 Starting: KES {{ number_format($auction->starting_bid) }}</div>
              <div class="property-card-spec">📈 Increment: KES {{ number_format($auction->bid_increment) }}</div>
            </div>
          </div>
        </div>

        <!-- Live Bid Feed -->
        <div class="table-card">
          <div class="table-card-header">
            <div class="table-card-title">📡 Live Bid Feed</div>
            <div class="live-badge">REAL-TIME</div>
          </div>
          <div id="liveBidFeed" style="padding:0 16px;">
            @forelse($auction->bids()->with('bidder')->latest()->take(10)->get() as $bid)
            <div style="display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-bottom:1px solid var(--border-dim);">
              <div>
                <div style="font-size:13px; color:var(--white); font-weight:{{ $bid->is_winning ? '600' : '400' }};">{{ optional($bid->bidder)->name ?? 'Anonymous' }}</div>
                <div style="font-size:11px; color:var(--muted);">{{ $bid->created_at->diffForHumans() }}</div>
              </div>
              <div style="font-family:var(--font-serif); font-size:16px; color:{{ $bid->is_winning ? 'var(--gold)' : 'var(--muted)' }};">
                KES {{ number_format($bid->amount) }} {{ $bid->is_winning ? '👑' : '' }}
              </div>
            </div>
            @empty
            <div style="padding:24px 0; text-align:center; color:var(--muted);">No bids yet. Be the first to bid!</div>
            @endforelse
          </div>
        </div>

        <!-- Property Description -->
        <div style="background:var(--navy3); border:1px solid var(--border-dim); border-radius:var(--radius); padding:24px; margin-top:24px;">
          <h3 style="font-size:16px; font-weight:600; color:var(--white); margin-bottom:14px;">Property Description</h3>
          <p style="color:var(--muted); font-size:14px; line-height:1.8;">{{ optional($auction->property)->description ?? 'Prime property available for auction. Contact the auctioneer for full details.' }}</p>
          <div style="margin-top:20px; padding-top:16px; border-top:1px solid var(--border-dim);">
            <div class="section-tag" style="margin-bottom:12px;">Documents on File</div>
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
              @foreach(['📋 Title Deed', '🔍 Valuation Report', '📐 Survey Report', '📄 Sale Agreement Draft', '🏗 Building Plans'] as $doc)
              <div style="background:var(--surface); border:1px solid var(--border-dim); border-radius:6px; padding:6px 14px; font-size:12px; color:var(--muted);">{{ $doc }}</div>
              @endforeach
            </div>
          </div>
        </div>
      </div>

      <!-- Right — Bid Widget -->
      <div style="position:sticky; top:100px;">
        <div class="auction-live" id="bidWidget">
          <div style="text-align:center; margin-bottom:20px;">
            <div style="font-size:12px; font-family:var(--font-mono); letter-spacing:2px; color:var(--red); margin-bottom:8px;">⏱ AUCTION ENDS IN</div>
            <div class="countdown-timer" data-countdown="{{ $auction->ends_at ? $auction->ends_at->timestamp : (time() + 8040) }}">
              <div class="countdown-unit"><span class="countdown-num">00</span><span class="countdown-label">Hours</span></div>
              <div class="countdown-unit"><span class="countdown-num">00</span><span class="countdown-label">Mins</span></div>
              <div class="countdown-unit"><span class="countdown-num">00</span><span class="countdown-label">Secs</span></div>
            </div>
          </div>

          <div style="background:rgba(0,0,0,0.2); border-radius:10px; padding:16px; margin-bottom:16px; text-align:center;">
            <div style="font-size:12px; color:var(--muted); margin-bottom:4px;">Current Highest Bid</div>
            <div id="currentBid" style="font-family:var(--font-serif); font-size:42px; font-weight:700; color:var(--gold); line-height:1;">
              KES {{ number_format($auction->current_bid ?? $auction->starting_bid) }}
            </div>
            <div style="font-size:12px; color:var(--muted); margin-top:4px;">Starting: KES {{ number_format($auction->starting_bid) }}</div>
          </div>

          <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:16px;">
            <div style="background:rgba(0,0,0,0.2); border-radius:8px; padding:12px; text-align:center;">
              <div id="bidCount" style="font-size:20px; font-weight:700; color:var(--white); font-family:var(--font-serif);">{{ $auction->bids()->count() }} bids</div>
              <div style="font-size:11px; color:var(--muted); font-family:var(--font-mono);">TOTAL BIDS</div>
            </div>
            <div style="background:rgba(0,0,0,0.2); border-radius:8px; padding:12px; text-align:center;">
              <div style="font-size:20px; font-weight:700; color:var(--white); font-family:var(--font-serif);">{{ ucfirst($auction->status) }}</div>
              <div style="font-size:11px; color:var(--muted); font-family:var(--font-mono);">STATUS</div>
            </div>
          </div>

          @if(session('user_id') && $auction->status === 'live')
          <form id="bidForm" style="margin-bottom:16px;">
            @csrf
            <div class="form-label" style="margin-bottom:8px;">Your Bid Amount</div>
            @php $nextMin = ($auction->current_bid ?? $auction->starting_bid) + $auction->bid_increment; @endphp
            <div style="display:flex; gap:8px; margin-bottom:8px;">
              @foreach([$nextMin, $nextMin + $auction->bid_increment, $nextMin + ($auction->bid_increment * 2)] as $val)
              <button type="button" onclick="document.getElementById('bidAmount').value='{{ $val }}'" class="btn btn-sm btn-outline" style="flex:1; justify-content:center; font-size:11px;">KES {{ number_format($val) }}</button>
              @endforeach
            </div>
            <input type="number" id="bidAmount" name="amount" class="form-control" placeholder="Min: KES {{ number_format($nextMin) }}" min="{{ $nextMin }}" step="{{ $auction->bid_increment }}">
            <div style="font-size:11px; color:var(--muted); margin-top:6px; font-family:var(--font-mono);">Min next bid: KES {{ number_format($nextMin) }} · Increment: KES {{ number_format($auction->bid_increment) }}</div>
            <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center; font-size:15px; padding:14px; margin-top:12px;">🔨 Place Bid</button>
          </form>
          @elseif(!session('user_id'))
          <a href="{{ url('/register') }}" class="btn btn-gold" style="width:100%; justify-content:center; font-size:15px; padding:14px; margin-bottom:12px;">🔨 Login to Bid</a>
          <a href="{{ url('/register') }}" class="btn btn-outline btn-sm" style="width:100%; justify-content:center;">Register to Bid (KYC Required)</a>
          @elseif($auction->status === 'ended')
          <div style="text-align:center; padding:16px; color:var(--muted);">This auction has ended.</div>
          @else
          <div style="text-align:center; padding:16px; color:var(--muted);">Auction is not yet live.</div>
          @endif

          @if(session('user_id') && session('role') === 'auctioneer' && $auction->status === 'live')
          <div style="margin-top:12px; padding-top:12px; border-top:1px solid rgba(255,255,255,0.1);">
            <button onclick="endAuction()" class="btn btn-outline btn-sm" style="width:100%; justify-content:center; color:var(--red); border-color:var(--red);">⏹ End Auction</button>
          </div>
          @endif

          <div style="margin-top:16px; padding-top:14px; border-top:1px solid rgba(255,255,255,0.1);">
            <div style="font-size:12px; color:var(--muted); text-align:center; line-height:1.6;">
              🔒 Winning bid is held in escrow until all conditions are met.<br>
              Refundable deposit required to register.
            </div>
          </div>
        </div>

        <!-- Auctioneer Info -->
        <div style="background:var(--navy3); border:1px solid var(--border-dim); border-radius:var(--radius); padding:20px; margin-top:16px;">
          <div class="section-tag" style="margin-bottom:12px;">Auctioneer</div>
          <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:48px; height:48px; border-radius:50%; background:var(--gold-dim); border:2px solid var(--gold); display:flex; align-items:center; justify-content:center; font-size:22px;">👨‍⚖️</div>
            <div>
              <div style="font-size:15px; font-weight:600; color:var(--white);">{{ optional($auction->auctioneer)->name ?? 'Licensed Auctioneer' }}</div>
              <div class="verified-badge">✓ LICENSED AUCTIONEER</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
// Live auction polling — updates every 3 seconds
let lastBidCount = {{ $auction->bids()->count() }};
let pollingActive = {{ $auction->status === 'live' ? 'true' : 'false' }};

function pollAuction() {
    if (!pollingActive) return;

    fetch('/auctions/{{ $auction->id }}/live-data')
        .then(r => r.json())
        .then(data => {
            // Update current bid
            const bidEl = document.getElementById('currentBid');
            if (bidEl && data.current_bid) {
                bidEl.textContent = 'KES ' + data.current_bid.toLocaleString();
            }

            // Update bid count
            const countEl = document.getElementById('bidCount');
            if (countEl) countEl.textContent = data.bid_count + ' bids';

            // Flash animation on new bid
            if (data.bid_count > lastBidCount && lastBidCount > 0) {
                if (bidEl) {
                    bidEl.style.color = '#2ECC8A';
                    setTimeout(() => bidEl.style.color = '', 1000);
                }
            }
            lastBidCount = data.bid_count;

            // Update min bid input
            const minBidInput = document.getElementById('bidAmount');
            if (minBidInput && data.current_bid) {
                const minBid = data.current_bid + {{ $auction->bid_increment }};
                minBidInput.min = minBid;
                minBidInput.placeholder = 'Min: KES ' + minBid.toLocaleString();
            }

            // Update bid feed
            const feedEl = document.getElementById('liveBidFeed');
            if (feedEl && data.recent_bids.length) {
                feedEl.innerHTML = data.recent_bids.map(b => `
                    <div style="display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-bottom:1px solid var(--border-dim);">
                        <div>
                            <div style="font-size:13px; color:var(--white); font-weight:${b.is_winning ? '600' : '400'};">${b.bidder}</div>
                            <div style="font-size:11px; color:var(--muted);">${b.time}</div>
                        </div>
                        <div style="font-family:var(--font-serif); font-size:16px; color:${b.is_winning ? 'var(--gold)' : 'var(--muted)'};">
                            ${b.formatted} ${b.is_winning ? '👑' : ''}
                        </div>
                    </div>
                `).join('');
            }

            // Check if ended
            if (data.status === 'ended') {
                pollingActive = false;
                const timerEl = document.querySelector('.countdown-timer');
                if (timerEl) timerEl.innerHTML = '<span style="color:var(--red);font-size:18px;font-weight:700;">AUCTION ENDED</span>';
                if (data.winner) {
                    const winnerBanner = document.createElement('div');
                    winnerBanner.style.cssText = 'background:var(--gold-dim);border:1px solid var(--gold);border-radius:12px;padding:20px;text-align:center;margin-top:16px;';
                    winnerBanner.innerHTML = '<div style="font-size:24px;margin-bottom:8px;">🏆</div><div style="color:var(--gold);font-family:var(--font-serif);font-size:20px;font-weight:700;">Winner: ' + data.winner + '</div>';
                    const bidWidget = document.getElementById('bidWidget');
                    if (bidWidget) bidWidget.replaceWith(winnerBanner);
                }
            }
        })
        .catch(() => {}); // Silent fail — keep polling
}

if (pollingActive) {
    pollAuction(); // immediate
    setInterval(pollAuction, 3000); // then every 3s
}

// Bid submission
const bidForm = document.getElementById('bidForm');
if (bidForm) {
    bidForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const amount = document.getElementById('bidAmount').value;
        const btn = bidForm.querySelector('button[type=submit]');
        const origText = btn.textContent;
        btn.textContent = 'Placing bid...';
        btn.disabled = true;

        fetch('/auctions/{{ $auction->id }}/live-bid', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ amount: parseFloat(amount) })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                btn.textContent = '✅ Bid Placed!';
                btn.style.background = 'var(--green)';
                setTimeout(() => {
                    btn.textContent = origText;
                    btn.style.background = '';
                    btn.disabled = false;
                }, 2000);
                pollAuction(); // immediate refresh
            } else {
                btn.textContent = '❌ ' + data.message;
                btn.style.background = 'var(--red)';
                setTimeout(() => {
                    btn.textContent = origText;
                    btn.style.background = '';
                    btn.disabled = false;
                }, 3000);
            }
        })
        .catch(() => { btn.textContent = origText; btn.disabled = false; });
    });
}

function endAuction() {
    if (!confirm('Are you sure you want to end this auction?')) return;
    fetch('/auctions/{{ $auction->id }}/end', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
            'Accept': 'application/json',
        },
        body: JSON.stringify({})
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            pollingActive = false;
            location.reload();
        }
    });
}
</script>
@endpush
