@extends('layouts.app')
@section('title', 'Create Account — EstateYard')
@section('content')
<div style="min-height:100vh; background:var(--navy); padding:100px 20px 60px; position:relative; overflow:hidden;">
  <div style="position:absolute; inset:0; background:radial-gradient(ellipse 80% 60% at 50% 30%, rgba(212,168,67,0.04) 0%, transparent 70%);"></div>
  <div class="container" style="position:relative; z-index:1; max-width:1100px;">

    <div style="text-align:center; margin-bottom:48px;">
      <a href="{{ url('/') }}" style="font-family:var(--font-serif); font-size:36px; font-weight:700; color:var(--gold); text-decoration:none;">Estate<span style="color:var(--white);">Yard</span></a>
      <h1 style="font-size:32px; font-weight:600; color:var(--white); margin:16px 0 8px;">Choose Your Role</h1>
      <p style="color:var(--muted); font-size:15px;">Select the role that best describes you to get started with the right dashboard</p>
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:16px; margin-bottom:48px;">
      @foreach([
        ['🏛️','Super Admin','Platform Operator','Full platform GMV, user management, escrow oversight','admin','var(--gold)'],
        ['🏠','Landlord','Property Owner','List properties, collect rent automatically, manage tenants','landlord','var(--blue)'],
        ['📋','Licensed Broker','Certified Agent','Create listings, manage CRM pipeline, earn commissions','broker','var(--green)'],
        ['📲','Promoter','Social Media Affiliate','Earn referral commissions by sharing property links','promoter','var(--teal)'],
        ['🔑','Tenant / Buyer','End Consumer','Browse properties, pay rent, sign digital leases','tenant','var(--gold2)'],
        ['🗂️','Property Manager','3rd Party Manager','Manage properties on behalf of landlord clients','property-manager','var(--blue)'],
        ['🏗️','Developer','Construction & Dev','Manage projects, off-plan sales, seek construction financing','developer','var(--purple)'],
        ['📐','Valuer','Certified Appraiser','Digital valuation reports and job queue management','valuer','var(--orange)'],
        ['🗺️','Surveyor','Land Surveyor','GPS site inspections, boundary surveys, structural reports','surveyor','var(--teal)'],
        ['🔨','Auctioneer','Auction Specialist','Run live property auctions with escrow-backed winning bids','auctioneer','var(--red)'],
        ['📊','Investor / REIT','Portfolio Manager','Track ROI, cap rate, yield across your property portfolio','investor','var(--purple)'],
        ['🏢','Corporate Partner','Agency / Company','Sub-accounts, bulk listings, white-label tools, API access','corporate','var(--gold)'],
        ['💰','Finance / Accountant','Financial Controller','General ledger, escrow reconciliation, tax reports','finance','var(--gold)'],
      ] as $role)
      <div onclick="selectRole('{{ $role[4] }}')" id="role-{{ $role[4] }}"
           style="background:var(--navy3); border:1px solid var(--border-dim); border-radius:var(--radius); padding:24px; cursor:pointer; transition:all .25s; position:relative; overflow:hidden;"
           onmouseover="this.style.borderColor='{{ $role[5] }}'; this.style.transform='translateY(-2px)'"
           onmouseout="if(selectedRole!=='{{ $role[4] }}'){this.style.borderColor='var(--border-dim)'; this.style.transform='none'}">
        <div style="position:absolute; top:0; left:0; right:0; height:2px; background:{{ $role[5] }}; transform:scaleX(0); transition:.3s;" class="role-accent"></div>
        <div style="font-size:32px; margin-bottom:12px;">{{ $role[0] }}</div>
        <div style="font-size:16px; font-weight:600; color:var(--white); margin-bottom:4px;">{{ $role[1] }}</div>
        <div style="font-size:11px; font-family:var(--font-mono); color:{{ $role[5] }}; letter-spacing:1px; margin-bottom:10px;">{{ strtoupper($role[2]) }}</div>
        <div style="font-size:13px; color:var(--muted);">{{ $role[3] }}</div>
        <div class="role-check" style="display:none; position:absolute; top:12px; right:12px; width:24px; height:24px; border-radius:50%; background:var(--green); display:none; align-items:center; justify-content:center; font-size:12px;">✓</div>
      </div>
      @endforeach
    </div>

    <div id="registerForm" style="display:none; max-width:520px; margin:0 auto;">
      <div style="background:var(--navy2); border:1px solid var(--border); border-radius:20px; padding:40px;">
        <h2 style="font-size:22px; color:var(--white); font-weight:600; margin-bottom:24px;">Create Your Account</h2>
        <form action="{{ url('/register') }}" method="POST" data-validate>
          @csrf
          <input type="hidden" name="role" id="roleInput" value="{{ request('role', '') }}">
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">First Name</label>
              <input type="text" name="first_name" class="form-control" placeholder="John" required>
            </div>
            <div class="form-group">
              <label class="form-label">Last Name</label>
              <input type="text" name="last_name" class="form-control" placeholder="Kamau" required>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" placeholder="you@example.com" required>
          </div>
          <div class="form-group">
            <label class="form-label">Phone Number</label>
            <input type="tel" name="phone" class="form-control" placeholder="+254 700 000 000" required>
          </div>
          <div class="form-group">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Min 8 characters" required>
          </div>
          <div class="form-group">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat password" required>
          </div>
          <div style="margin-bottom:24px;">
            <label style="display:flex; align-items:flex-start; gap:10px; font-size:13px; color:var(--muted); cursor:pointer;">
              <input type="checkbox" name="terms" required style="accent-color:var(--gold); margin-top:3px;">
              I agree to the <a href="#" style="color:var(--gold);">Terms of Service</a> and <a href="#" style="color:var(--gold);">Privacy Policy</a>
            </label>
          </div>
          <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center; font-size:16px; padding:15px;">Create Account →</button>
        </form>
        <p style="text-align:center; margin-top:20px; font-size:14px; color:var(--muted);">
          Already have an account? <a href="{{ url('/login') }}">Sign in</a>
        </p>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
let selectedRole = '{{ request("role", "") }}';

function selectRole(role) {
  selectedRole = role;
  document.querySelectorAll('[id^="role-"]').forEach(el => {
    el.style.borderColor = 'var(--border-dim)';
    el.style.transform = 'none';
    el.querySelector('.role-accent').style.transform = 'scaleX(0)';
    const check = el.querySelector('.role-check');
    if (check) check.style.display = 'none';
  });
  const el = document.getElementById('role-' + role);
  if (el) {
    el.style.borderColor = 'var(--gold)';
    el.style.transform = 'translateY(-2px)';
    el.querySelector('.role-accent').style.transform = 'scaleX(1)';
    const check = el.querySelector('.role-check');
    if (check) check.style.display = 'flex';
  }
  document.getElementById('roleInput').value = role;
  const form = document.getElementById('registerForm');
  form.style.display = 'block';
  setTimeout(() => form.scrollIntoView({ behavior: 'smooth', block: 'start' }), 100);
}

// Auto-select if role param present
if (selectedRole) { selectRole(selectedRole); }
</script>
@endpush
@endsection
