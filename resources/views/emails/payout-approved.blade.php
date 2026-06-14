<!DOCTYPE html>
<html>
<body style="font-family:sans-serif;background:#f4f4f4;padding:20px">
<div style="max-width:600px;margin:0 auto;background:#fff;border-radius:8px;overflow:hidden">
  <div style="background:#2ECC8A;padding:20px;text-align:center">
    <h1 style="color:#fff;margin:0;font-size:1.4rem">TheOnlineYard</h1>
  </div>
  <div style="padding:30px">
    <h2>Payout Approved!</h2>
    <p>Hi {{ $payout->user?->name }},</p>
    <p>Your payout of <strong>KES {{ number_format($payout->amount) }}</strong> has been approved.</p>
    <table style="width:100%;border-collapse:collapse;margin:20px 0">
      <tr><td style="padding:8px;border-bottom:1px solid #eee;color:#666">Amount</td><td style="padding:8px;border-bottom:1px solid #eee"><strong style="color:#2ECC8A">KES {{ number_format($payout->amount) }}</strong></td></tr>
      <tr><td style="padding:8px;border-bottom:1px solid #eee;color:#666">M-Pesa Number</td><td style="padding:8px;border-bottom:1px solid #eee"><strong>{{ $payout->mpesa_number }}</strong></td></tr>
      <tr><td style="padding:8px;color:#666">Processed At</td><td style="padding:8px">{{ $payout->processed_at ? \Carbon\Carbon::parse($payout->processed_at)->format('d M Y, H:i') : now()->format('d M Y, H:i') }}</td></tr>
    </table>
    <p style="color:#666;font-size:.9rem">Transfer to M-Pesa: <strong>{{ $payout->mpesa_number }}</strong>. The funds will be transferred within 24 hours.</p>
  </div>
  <div style="background:#f8f8f8;padding:15px;text-align:center;color:#999;font-size:.8rem">
    &copy; {{ date('Y') }} TheOnlineYard. Kenya's Premier Rental Platform.
  </div>
</div>
</body>
</html>
