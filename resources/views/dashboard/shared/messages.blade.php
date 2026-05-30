@extends('layouts.dashboard')
@section('title', 'Messages — EstateYard')
@section('page-title', 'Messages')
@section('page-subtitle', 'Secure in-platform messaging')
@section('sidebar-nav')
<div class="dash-nav-section">
  <a href="{{ url('/dashboard') }}" class="dash-nav-item"><span class="dash-nav-icon">←</span> Back to Dashboard</a>
</div>
@endsection
@section('content')
<div class="chat-layout" style="height:calc(100vh - 200px); border:1px solid var(--border-dim); border-radius:var(--radius); overflow:hidden;">
  <div class="chat-sidebar">
    <div style="padding:16px; border-bottom:1px solid var(--border-dim);">
      <input type="text" class="form-control" placeholder="🔍 Search conversations..." style="font-size:13px; padding:8px 14px;">
    </div>
    <div class="chat-list">
      @foreach([['🏠','James Kamau (Landlord)','Re: Rent for June...','2 min ago',true],['📋','Sarah Odhiambo (Broker)','The client wants to...','1 hr ago',false],['🔑','Peter Njoroge (Tenant)','Maintenance issue with...','Yesterday',true],['🏗️','David Waweru (Developer)','When can we do...','2 days ago',false],['🔨','Auctioneer Team','Auction #24 is live...','3 days ago',false]] as $i => $c)
      <div class="chat-item {{ $i===0 ? 'active' : '' }}">
        <div class="chat-item-avatar">{{ $c[0] }}</div>
        <div style="flex:1; min-width:0;">
          <div style="display:flex; justify-content:space-between; align-items:center;">
            <div class="chat-item-name" style="{{ $c[4] ? 'color:var(--white);' : '' }}">{{ $c[1] }}</div>
            <div class="chat-item-time">{{ $c[3] }}</div>
          </div>
          <div class="chat-item-preview">{{ $c[2] }}</div>
        </div>
        @if($c[4])<div style="width:8px; height:8px; border-radius:50%; background:var(--gold); flex-shrink:0; margin-top:6px;"></div>@endif
      </div>
      @endforeach
    </div>
  </div>
  <div class="chat-main">
    <div class="chat-header">
      <div style="width:44px; height:44px; border-radius:50%; background:var(--gold-dim); border:2px solid var(--gold); display:flex; align-items:center; justify-content:center; font-size:18px;">🏠</div>
      <div><div style="font-size:15px; font-weight:600; color:var(--white);">James Kamau</div><div class="verified-badge">✓ LANDLORD</div></div>
      <div style="margin-left:auto; display:flex; gap:8px;"><button class="icon-btn">📞</button><button class="icon-btn">📹</button><button class="icon-btn">ℹ️</button></div>
    </div>
    <div class="chat-messages">
      @foreach([['🏠','Hello! I wanted to confirm that the rent payment for June has been processed successfully.', false],['You','Yes, I received the notification. KSh 95,000 confirmed on June 1st. Thank you!',true],['🏠','Perfect. I also wanted to discuss the lease renewal coming up in December. Shall we discuss the new terms?',false],['You','Yes, I would like to continue the tenancy. Could we keep the same rate for another year?',true],['🏠','I will need to review market rates but I am open to discussing. Let us meet for coffee next week.',false],['You','That works for me! How about Thursday at 10am at the property?',true]] as $m)
      <div class="message {{ $m[2] ? 'me' : '' }}">
        @if(!$m[2])<div class="chat-item-avatar" style="width:36px; height:36px; font-size:14px;">{{ $m[0] }}</div>@endif
        <div class="message-bubble">{{ $m[1] }}</div>
      </div>
      @endforeach
    </div>
    <div class="chat-input-area">
      <input type="text" class="chat-input" placeholder="Type your message...">
      <button onclick="showToast('Message sent!', 'green')" class="btn btn-gold">Send →</button>
    </div>
  </div>
</div>
@endsection
