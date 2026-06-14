<!DOCTYPE html>
<html>
<body style="font-family:sans-serif;background:#f4f4f4;padding:20px">
<div style="max-width:600px;margin:0 auto;background:#fff;border-radius:8px;overflow:hidden">
  <div style="background:#E84040;padding:20px;text-align:center">
    <h1 style="color:#fff;margin:0;font-size:1.4rem">TheOnlineYard</h1>
  </div>
  <div style="padding:30px">
    <h2>Booking Cancelled</h2>
    <p>Hi {{ $booking->client?->name }},</p>
    <p>We're sorry your booking was cancelled.</p>
    <p>Your booking <strong>{{ $booking->booking_ref }}</strong> for <strong>{{ $booking->listing?->title }}</strong> has been cancelled.</p>
    <table style="width:100%;border-collapse:collapse;margin:20px 0">
      <tr><td style="padding:8px;border-bottom:1px solid #eee;color:#666">Booking Ref</td><td style="padding:8px;border-bottom:1px solid #eee"><strong>{{ $booking->booking_ref }}</strong></td></tr>
      <tr><td style="padding:8px;border-bottom:1px solid #eee;color:#666">Listing</td><td style="padding:8px;border-bottom:1px solid #eee">{{ $booking->listing?->title }}</td></tr>
      <tr><td style="padding:8px;color:#666">Amount</td><td style="padding:8px"><strong>KES {{ number_format($booking->total_amount) }}</strong></td></tr>
    </table>
    <p style="color:#666;font-size:.9rem">If you have any questions, please contact our support team.</p>
  </div>
  <div style="background:#f8f8f8;padding:15px;text-align:center;color:#999;font-size:.8rem">
    &copy; {{ date('Y') }} TheOnlineYard. Kenya's Premier Rental Platform.
  </div>
</div>
</body>
</html>
