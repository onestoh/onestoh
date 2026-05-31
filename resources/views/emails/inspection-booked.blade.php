<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Inspection Scheduled</title>
</head>
<body style="margin:0;padding:0;background:#f5f7fa;font-family:'Helvetica Neue',Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f7fa;padding:40px 20px;">
  <tr><td align="center">
    <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

      <!-- HEADER -->
      <tr>
        <td style="background:#0a1628;border-radius:12px 12px 0 0;padding:28px 40px;text-align:center;">
          <h1 style="margin:0;font-size:28px;font-weight:700;color:#c9a84c;letter-spacing:2px;font-family:Georgia,serif;">Estate<span style="color:#ffffff;">Yard</span></h1>
          <p style="margin:6px 0 0;font-size:12px;color:#8899aa;letter-spacing:1px;text-transform:uppercase;">Premium Real Estate Platform</p>
        </td>
      </tr>

      <!-- BODY -->
      <tr>
        <td style="background:#ffffff;padding:40px;">
          <div style="text-align:center;margin-bottom:32px;">
            <div style="font-size:48px;margin-bottom:16px;">🏠</div>
            <h2 style="margin:0;font-size:22px;color:#0a1628;font-weight:700;">Inspection Scheduled</h2>
            <p style="margin:8px 0 0;color:#6b7a8d;font-size:14px;">A property inspection has been booked</p>
          </div>

          <div style="background:#f8f9fb;border-radius:8px;padding:24px;margin-bottom:24px;border-left:4px solid #c9a84c;">
            <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td style="padding:8px 0;color:#6b7a8d;font-size:13px;">Property</td>
                <td style="padding:8px 0;color:#0a1628;font-size:13px;font-weight:600;text-align:right;">{{ $propertyTitle }}</td>
              </tr>
              <tr>
                <td style="padding:8px 0;color:#6b7a8d;font-size:13px;">Requested By</td>
                <td style="padding:8px 0;color:#0a1628;font-size:13px;font-weight:600;text-align:right;">{{ $requesterName }}</td>
              </tr>
              <tr>
                <td style="padding:8px 0;color:#6b7a8d;font-size:13px;">Scheduled Date</td>
                <td style="padding:8px 0;color:#c9a84c;font-size:13px;font-weight:700;text-align:right;">{{ $scheduledAt }}</td>
              </tr>
              @if($notes)
              <tr>
                <td style="padding:8px 0;color:#6b7a8d;font-size:13px;vertical-align:top;">Notes</td>
                <td style="padding:8px 0;color:#0a1628;font-size:13px;text-align:right;">{{ $notes }}</td>
              </tr>
              @endif
            </table>
          </div>

          <p style="color:#6b7a8d;font-size:14px;line-height:1.6;margin:0 0 24px;">
            Please ensure the property is accessible at the scheduled time. If you need to reschedule or have questions, log in to your dashboard or contact the requester directly.
          </p>

          <div style="text-align:center;">
            <a href="{{ config('app.url') }}/dashboard" style="display:inline-block;background:#c9a84c;color:#0a1628;padding:14px 32px;border-radius:8px;text-decoration:none;font-weight:700;font-size:14px;letter-spacing:0.5px;">View Dashboard</a>
          </div>
        </td>
      </tr>

      <!-- FOOTER -->
      <tr>
        <td style="background:#0a1628;border-radius:0 0 12px 12px;padding:24px 40px;text-align:center;">
          <p style="margin:0 0 8px;color:#8899aa;font-size:12px;">© {{ date('Y') }} EstateYard. All rights reserved.</p>
          <p style="margin:0;color:#8899aa;font-size:11px;">You received this because you are registered as a property stakeholder on EstateYard. <a href="#" style="color:#c9a84c;text-decoration:none;">Manage preferences</a></p>
        </td>
      </tr>

    </table>
  </td></tr>
</table>
</body>
</html>
