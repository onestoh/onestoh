@extends('layouts.dashboard')

@section('title', 'Getting Started — EstateYard')

@section('content')
<div class="dash-content" style="max-width:760px;">

  <div style="margin-bottom:32px;">
    <span class="section-tag">WELCOME</span>
    <h1 style="font-family:var(--font-serif); font-size:32px; color:var(--white); margin:12px 0 8px;">Welcome to EstateYard</h1>
    <p style="color:var(--muted);">Complete these steps to unlock all platform features and build trust with clients.</p>
  </div>

  @php
    $steps = $onboardingSteps ?? [
      ['title'=>'Complete Your Profile','desc'=>'Add your full name, phone number, and a profile photo.','done'=>false,'url'=>'/dashboard/profile','icon'=>'👤'],
      ['title'=>'Verify Your Identity','desc'=>'Upload a government-issued ID to get your verified badge.','done'=>false,'url'=>'/verification','icon'=>'✅'],
      ['title'=>'Add Your First Property','desc'=>'List a property for sale, rent, or short-stay.','done'=>false,'url'=>'/properties/create','icon'=>'🏠'],
    ];
    $completed = collect($steps)->where('done', true)->count();
    $pct = count($steps) > 0 ? round($completed / count($steps) * 100) : 0;
  @endphp

  {{-- Progress bar --}}
  <div class="form-card" style="margin-bottom:28px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
      <div style="color:var(--white); font-weight:600;">Setup Progress</div>
      <div style="color:var(--gold); font-weight:700;">{{ $pct }}%</div>
    </div>
    <div style="background:var(--navy); border-radius:100px; height:8px; overflow:hidden;">
      <div style="height:100%; width:{{ $pct }}%; background:linear-gradient(90deg,var(--gold),var(--gold2)); border-radius:100px; transition:width .6s ease;"></div>
    </div>
    <div style="color:var(--muted); font-size:13px; margin-top:8px;">{{ $completed }} of {{ count($steps) }} steps complete</div>
  </div>

  {{-- Steps --}}
  <div style="display:flex; flex-direction:column; gap:16px;">
    @foreach($steps as $i => $step)
    <div style="background:var(--navy2); border:1px solid {{ $step['done'] ? 'var(--green)' : 'var(--border)' }}; border-radius:var(--radius); padding:24px; display:flex; gap:20px; align-items:center;">
      <div style="width:52px; height:52px; border-radius:50%; background:{{ $step['done'] ? 'rgba(46,204,138,0.15)' : 'var(--navy3)' }}; display:flex; align-items:center; justify-content:center; font-size:24px; flex-shrink:0;">
        {{ $step['done'] ? '✅' : $step['icon'] }}
      </div>
      <div style="flex:1;">
        <div style="color:var(--white); font-weight:600; margin-bottom:4px;">Step {{ $i+1 }}: {{ $step['title'] }}</div>
        <div style="color:var(--muted); font-size:14px;">{{ $step['desc'] }}</div>
      </div>
      @if(!$step['done'])
      <a href="{{ url($step['url']) }}" class="btn btn-outline btn-sm" style="flex-shrink:0;">Start →</a>
      @else
      <span style="color:var(--green); font-size:13px; font-weight:600; flex-shrink:0;">Done ✓</span>
      @endif
    </div>
    @endforeach
  </div>

  @if($pct === 100)
  <div style="margin-top:32px; background:rgba(46,204,138,0.08); border:1px solid var(--green); border-radius:var(--radius); padding:32px; text-align:center;">
    <div style="font-size:48px; margin-bottom:12px;">🎉</div>
    <h2 style="color:var(--green); font-family:var(--font-serif); font-size:24px; margin-bottom:8px;">You're All Set!</h2>
    <p style="color:var(--muted); margin-bottom:20px;">Your profile is complete. Explore the full platform now.</p>
    <a href="{{ url('/dashboard') }}" class="btn btn-gold">Go to Dashboard</a>
  </div>
  @endif

</div>
@endsection
