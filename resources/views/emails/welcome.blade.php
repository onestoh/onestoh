<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Welcome to EstateYard</title>
</head>
<body style="margin:0;padding:0;background:#f5f7fa;font-family:'Helvetica Neue',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f7fa;padding:40px 20px;">
  <tr><td align="center">
    <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">
      <tr>
        <td style="background:#0a1628;border-radius:12px 12px 0 0;padding:28px 40px;text-align:center;">
          <h1 style="margin:0;font-size:28px;font-weight:700;color:#c9a84c;letter-spacing:2px;font-family:Georgia,serif;">Estate<span style="color:#ffffff;">Yard</span></h1>
          <p style="margin:6px 0 0;font-size:12px;color:#8899aa;letter-spacing:1px;text-transform:uppercase;">Premium Real Estate Platform</p>
        </td>
      </tr>
      <tr>
        <td style="background:#ffffff;padding:40px;">
          <div style="text-align:center;margin-bottom:32px;">
            <div style="display:inline-block;background:#e8f0fe;border-radius:50%;width:64px;height:64px;line-height:64px;font-size:32px;margin-bottom:16px;">🏡</div>
            <h2 style="margin:0;font-size:24px;color:#0a1628;font-weight:700;">Welcome, {{ $userName }}!</h2>
            <p style="margin:8px 0 0;color:#6b7a8d;font-size:14px;">Your EstateYard account is ready. Let's get started.</p>
          </div>
          <div style="background:#f8f9fb;border-radius:8px;padding:24px;margin-bottom:24px;border-left:4px solid #c9a84c;">
            <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td style="padding:8px 0;color:#6b7a8d;font-size:13px;">Account Type</td>
                <td style="padding:8px 0;color:#0a1628;font-size:13px;font-weight:600;text-align:right;">{{ $userRole }}</td>
              </tr>
              <tr>
                <td style="padding:8px 0;color:#6b7a8d;font-size:13px;">Email</td>
                <td style="padding:8px 0;color:#0a1628;font-size:13px;font-weight:600;text-align:right;">{{ $userEmail }}</td>
              </tr>
            </table>
          </div>
          <h3 style="margin:0 0 16px;color:#0a1628;font-size:16px;">What you can do on EstateYard:</h3>
          <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
            @foreach([['🏠','Browse Properties','Search thousands of verified listings across Kenya'],['🔒','Secure Escrow','Buy & sell with protected escrow transactions'],['🔨','Live Auctions','Bid on exclusive properties in real-time'],['✅','Get Verified','Build trust with KYC verification badges']] as $f)
            <tr>
              <td style="padding:8px 0;vertical-align:top;width:40px;font-size:20px;">{{ $f[0] }}</td>
              <td style="padding:8px 0 8px 8px;vertical-align:top;">
                <div style="font-size:13px;font-weight:600;color:#0a1628;">{{ $f[1] }}</div>
                <div style="font-size:12px;color:#6b7a8d;margin-top:2px;">{{ $f[2] }}</div>
              </td>
            </tr>
            @endforeach
          </table>
          <div style="text-align:center;">
            <a href="{{ config('app.url') }}/dashboard" style="display:inline-block;background:#c9a84c;color:#0a1628;padding:14px 32px;border-radius:8px;text-decoration:none;font-weight:700;font-size:14px;letter-spacing:0.5px;">Go to My Dashboard</a>
          </div>
        </td>
      </tr>
      <tr>
        <td style="background:#0a1628;border-radius:0 0 12px 12px;padding:24px 40px;text-align:center;">
          <p style="margin:0 0 8px;color:#8899aa;font-size:12px;">© {{ date('Y') }} EstateYard. All rights reserved.</p>
          <p style="margin:0;color:#8899aa;font-size:11px;">You received this because you created an account on EstateYard. <a href="#" style="color:#c9a84c;text-decoration:none;">Manage preferences</a></p>
        </td>
      </tr>
    </table>
  </td></tr>
</table>
</body>
</html>
