<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Identity — TheOnlineYard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --black:#080C12; --dark:#0B1018; --surface:#141D2B; --amber:#E8922A; --text:#DCE5F2; --muted:#7088A8; --border:#1E2D42; --danger:#E84040; }
        body { background: var(--dark); color: var(--text); font-family: 'Segoe UI', sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .mfa-card { background: var(--black); border: 1px solid var(--border); border-radius: 16px; width: 100%; max-width: 420px; padding: 2.5rem; }
        .mfa-icon { width: 64px; height: 64px; border-radius: 50%; background: rgba(232,146,42,.12); display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; }
        .code-input { background: var(--surface); border: 2px solid var(--border); color: var(--text); font-size: 2rem; font-weight: 700; letter-spacing: .5rem; text-align: center; border-radius: 10px; padding: .75rem; transition: border-color .2s; }
        .code-input:focus { outline: none; border-color: var(--amber); background: var(--surface); color: var(--text); box-shadow: 0 0 0 3px rgba(232,146,42,.15); }
        .btn-amber { background: var(--amber); color: #fff; border: none; font-weight: 600; padding: .75rem; border-radius: 8px; }
        .btn-amber:hover { background: #d07a20; color: #fff; }
        .alert-info-custom { background: rgba(99,179,237,.1); border: 1px solid rgba(99,179,237,.3); color: #63b3ed; border-radius: 8px; padding: .75rem 1rem; font-size: .875rem; }
        .alert-danger-custom { background: rgba(232,64,64,.1); border: 1px solid rgba(232,64,64,.3); color: var(--danger); border-radius: 8px; padding: .75rem 1rem; font-size: .875rem; }
        .masked-email { background: rgba(232,146,42,.1); border: 1px solid rgba(232,146,42,.2); color: var(--amber); border-radius: 6px; padding: .3rem .7rem; font-size: .875rem; font-family: monospace; }
        a { color: var(--amber); }
        a:hover { color: #d07a20; }
    </style>
</head>
<body>
<div class="mfa-card">
    <div class="mfa-icon">
        <i class="fas fa-shield-alt fa-2x" style="color:var(--amber)"></i>
    </div>
    <h4 class="text-center fw-bold mb-1">Two-Factor Verification</h4>
    <p class="text-center mb-4" style="color:var(--muted);font-size:.875rem">
        A 6-digit code was sent to<br>
        @if($user)
        <span class="masked-email mt-1 d-inline-block">
            {{ substr($user->email, 0, 2) . str_repeat('*', max(strlen(explode('@',$user->email)[0]) - 2, 3)) . '@' . explode('@',$user->email)[1] }}
        </span>
        @endif
    </p>

    @if(session('info'))
    <div class="alert-info-custom mb-3"><i class="fas fa-info-circle me-2"></i>{{ session('info') }}</div>
    @endif

    @if($errors->any())
    <div class="alert-danger-custom mb-3"><i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('mfa.verify.post') }}">
        @csrf
        <div class="mb-4">
            <input type="text" name="code" class="form-control code-input" placeholder="000000"
                maxlength="6" inputmode="numeric" pattern="[0-9]{6}" autocomplete="one-time-code"
                autofocus required>
        </div>
        <button type="submit" class="btn btn-amber w-100 mb-3">
            <i class="fas fa-check me-2"></i>Verify Identity
        </button>
    </form>

    <div class="text-center" style="font-size:.875rem;color:var(--muted)">
        Didn't receive the code?
        <form method="POST" action="{{ route('mfa.send') }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-link p-0" style="color:var(--amber);font-size:.875rem;vertical-align:baseline">Resend code</button>
        </form>
    </div>

    <div class="text-center mt-3">
        <a href="{{ route('login') }}" style="font-size:.8rem;color:var(--muted)">
            <i class="fas fa-arrow-left me-1"></i>Back to login
        </a>
    </div>
</div>
</body>
</html>
