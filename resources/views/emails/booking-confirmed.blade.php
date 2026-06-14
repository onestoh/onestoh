<!DOCTYPE html>
<html>
<body style="font-family:sans-serif;background:#f4f4f4;padding:20px">
<div style="max-width:600px;margin:0 auto;background:#fff;border-radius:8px;overflow:hidden">
  <div style="background:#E8922A;padding:20px;text-align:center">
    <h1 style="color:#fff;margin:0;font-size:1.4rem">TheOnlineYard</h1>
  </div>
  <div style="padding:30px">
    <h2>Booking Confirmed!</h2>
    <p>Hi {{ $booking->client?->name }},</p>
    <p>Your booking <strong>{{ $booking->booking_ref }}</strong> has been confirmed.</p>
    <table style="width:100%;border-collapse:collapse;margin:20px 0">
      <tr><td style="padding:8px;border-bottom:1px solid #eee;color:#666">Listing</td><td style="padding:8px;border-bottom:1px solid #eee"><strong>{{ $booking->listing?->title }}</strong></td></tr>
      <tr><td style="padding:8px;border-bottom:1px solid #eee;color:#666">Start</td><td style="padding:8px;border-bottom:1px solid #eee">{{ $booking->start_datetime ? \Carbon\Carbon::parse($booking->start_datetime)->format('d M Y') : '—' }}</td></tr>
      <tr><td style="padding:8px;border-bottom:1px solid #eee;color:#666">End</td><td style="padding:8px;border-bottom:1px solid #eee">{{ $booking->end_datetime ? \Carbon\Carbon::parse($booking->end_datetime)->format('d M Y') : '—' }}</td></tr>
      <tr><td style="padding:8px;color:#666">Total</td><td style="padding:8px"><strong>KES {{ number_format($booking->total_amount) }}</strong></td></tr>
    </table>
    <p style="color:#666;font-size:.9rem">You can view your booking details in your dashboard.</p>
  </div>
  <div style="background:#f8f8f8;padding:15px;text-align:center;color:#999;font-size:.8rem">
    &copy; {{ date('Y') }} TheOnlineYard. Kenya's Premier Rental Platform.
  </div>
</div>
</body>
</html>
