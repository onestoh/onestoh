<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Verification Status Update</title>
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
            @if($status === 'approved')
            <div style="display:inline-block;background:#e8f5e9;border-radius:50%;width:64px;height:64px;line-height:64px;font-size:32px;margin-bottom:16px;">✅</div>
            <h2 style="margin:0;font-size:22px;color:#0a1628;font-weight:700;">Verification Approved!</h2>
            <p style="margin:8px 0 0;color:#6b7a8d;font-size:14px;">Your identity has been verified on EstateYard</p>
            @else
            <div style="display:inline-block;background:#fde8e8;border-radius:50%;width:64px;height:64px;line-height:64px;font-size:32px;margin-bottom:16px;">❌</div>
            <h2 style="margin:0;font-size:22px;color:#0a1628;font-weight:700;">Verification Rejected</h2>
            <p style="margin:8px 0 0;color:#6b7a8d;font-size:14px;">Your verification could not be completed at this time</p>
            @endif
          </div>
          <div style="background:#f8f9fb;border-radius:8px;padding:24px;margin-bottom:24px;border-left:4px solid #c9a84c;">
            <p style="margin:0 0 8px;color:#6b7a8d;font-size:13px;">Dear <strong style="color:#0a1628;">{{ $userName }}</strong>,</p>
            @if($status === 'approved')
            <p style="margin:0;color:#6b7a8d;font-size:14px;line-height:1.6;">
              Congratulations! Your <strong>{{ ucfirst($tierType) }}</strong> verification has been approved. You now have a verified badge on your profile, which increases trust with landlords, tenants, and other users on EstateYard.
            </p>
            @else
            <p style="margin:0;color:#6b7a8d;font-size:14px;line-height:1.6;">
              Unfortunately, your verification submission did not meet our requirements. Please review your documents and resubmit with clear, valid identification documents. Contact support if you need assistance.
            </p>
            @endif
          </div>
          <div style="text-align:center;">
            <a href="{{ config('app.url') }}/dashboard/verification" style="display:inline-block;background:#c9a84c;color:#0a1628;padding:14px 32px;border-radius:8px;text-decoration:none;font-weight:700;font-size:14px;letter-spacing:0.5px;">
              @if($status === 'approved') View Verified Badge @else Resubmit Documents @endif
            </a>
          </div>
        </td>
      </tr>
      <tr>
        <td style="background:#0a1628;border-radius:0 0 12px 12px;padding:24px 40px;text-align:center;">
          <p style="margin:0 0 8px;color:#8899aa;font-size:12px;">© {{ date('Y') }} EstateYard. All rights reserved.</p>
          <p style="margin:0;color:#8899aa;font-size:11px;">You received this because you submitted a verification request. <a href="#" style="color:#c9a84c;text-decoration:none;">Manage preferences</a></p>
        </td>
      </tr>
    </table>
  </td></tr>
</table>
</body>
</html>
