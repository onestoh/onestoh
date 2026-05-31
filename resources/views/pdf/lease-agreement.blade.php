<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Lease Agreement — {{ optional($lease->property)->title }}</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color: #1a1a1a; background: #fff; }
  .header { border-bottom: 3px solid #1a1a1a; padding-bottom: 16px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: flex-end; }
  .brand { font-size: 24px; font-weight: 700; letter-spacing: 2px; }
  .brand span { color: #888; }
  .doc-title { font-size: 18px; font-weight: 700; text-align: center; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 1px; }
  .doc-subtitle { text-align: center; color: #555; font-size: 10px; margin-bottom: 24px; }
  .section { margin-bottom: 20px; }
  .section-title { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #ccc; padding-bottom: 4px; margin-bottom: 12px; }
  table { width: 100%; border-collapse: collapse; }
  td { padding: 6px 8px; vertical-align: top; }
  .label { color: #555; width: 40%; font-weight: 600; }
  .value { color: #1a1a1a; }
  .clause { margin-bottom: 10px; }
  .clause-num { font-weight: 700; }
  .sig-block { margin-top: 40px; }
  .sig-row { display: flex; gap: 40px; }
  .sig-col { flex: 1; border-top: 1px solid #1a1a1a; padding-top: 8px; }
  .sig-label { font-size: 10px; color: #555; }
  .sig-name { font-weight: 600; margin-top: 4px; }
  .footer { margin-top: 32px; border-top: 1px solid #ccc; padding-top: 8px; text-align: center; font-size: 9px; color: #888; }
  .highlight { background: #f8f8f8; padding: 12px; border-left: 3px solid #1a1a1a; margin-bottom: 16px; }
</style>
</head>
<body>

<div class="header">
  <div class="brand">Estate<span>Yard</span></div>
  <div style="text-align:right; font-size:10px; color:#555;">
    <div>Agreement #LA-{{ str_pad($lease->id, 6, '0', STR_PAD_LEFT) }}</div>
    <div>Generated: {{ now()->format('d F Y') }}</div>
  </div>
</div>

<div class="doc-title">Residential / Commercial Lease Agreement</div>
<div class="doc-subtitle">This agreement is legally binding. Please read all clauses carefully before signing.</div>

<div class="highlight">
  <table>
    <tr>
      <td class="label">Property</td>
      <td class="value">{{ optional($lease->property)->title ?? 'N/A' }}</td>
      <td class="label">Location</td>
      <td class="value">{{ optional($lease->property)->location ?? '' }}, {{ optional($lease->property)->county ?? '' }}</td>
    </tr>
    <tr>
      <td class="label">Monthly Rent</td>
      <td class="value" style="font-weight:700; font-size:13px;">KES {{ number_format($lease->monthly_rent ?? 0, 0) }}</td>
      <td class="label">Deposit</td>
      <td class="value">KES {{ number_format($lease->deposit_amount ?? 0, 0) }}</td>
    </tr>
  </table>
</div>

<div class="section">
  <div class="section-title">Parties</div>
  <table>
    <tr>
      <td class="label">Landlord</td>
      <td class="value">{{ optional($lease->landlord)->name ?? 'N/A' }}</td>
      <td class="label">Tenant</td>
      <td class="value">{{ optional($lease->tenant)->name ?? 'N/A' }}</td>
    </tr>
    <tr>
      <td class="label">Landlord Email</td>
      <td class="value">{{ optional($lease->landlord)->email ?? '' }}</td>
      <td class="label">Tenant Email</td>
      <td class="value">{{ optional($lease->tenant)->email ?? '' }}</td>
    </tr>
    <tr>
      <td class="label">Landlord Phone</td>
      <td class="value">{{ optional($lease->landlord)->phone ?? '' }}</td>
      <td class="label">Tenant Phone</td>
      <td class="value">{{ optional($lease->tenant)->phone ?? '' }}</td>
    </tr>
  </table>
</div>

<div class="section">
  <div class="section-title">Lease Term</div>
  <table>
    <tr>
      <td class="label">Start Date</td>
      <td class="value">{{ $lease->start_date ? \Carbon\Carbon::parse($lease->start_date)->format('d F Y') : 'N/A' }}</td>
      <td class="label">End Date</td>
      <td class="value">{{ $lease->end_date ? \Carbon\Carbon::parse($lease->end_date)->format('d F Y') : 'N/A' }}</td>
    </tr>
    <tr>
      <td class="label">Status</td>
      <td class="value">{{ ucfirst($lease->status ?? 'active') }}</td>
      <td class="label">Payment Day</td>
      <td class="value">{{ $lease->payment_day ?? '1' }}st of each month</td>
    </tr>
  </table>
</div>

<div class="section">
  <div class="section-title">Terms and Conditions</div>
  @foreach([
    ['Payment Obligation', 'The Tenant agrees to pay the monthly rent of KES '.number_format($lease->monthly_rent ?? 0, 0).' on or before the '.($lease->payment_day ?? '1').'st day of each month. Late payments attract a penalty of 5% per month.'],
    ['Security Deposit', 'A refundable security deposit of KES '.number_format($lease->deposit_amount ?? 0, 0).' shall be held by the Landlord and returned within 14 days of lease termination, subject to property condition.'],
    ['Property Use', 'The property shall be used solely for lawful residential/commercial purposes as agreed. Sub-letting requires written consent from the Landlord.'],
    ['Maintenance', 'The Tenant shall keep the property in good condition and report any damage or required repairs to the Landlord within 48 hours of discovery.'],
    ['Termination', 'Either party may terminate this agreement with 30 days written notice. Breach of terms may result in immediate termination with forfeiture of deposit.'],
    ['Dispute Resolution', 'Any disputes arising from this agreement shall first be resolved through mediation. If unresolved, disputes shall be referred to the Business Premises Rent Tribunal, Kenya.'],
  ] as $i => [$title, $text])
  <div class="clause">
    <span class="clause-num">{{ $i + 1 }}. {{ $title }}:</span>
    {{ $text }}
  </div>
  @endforeach
</div>

<div class="sig-block">
  <div class="section-title">Signatures</div>
  <table style="margin-top:16px;">
    <tr>
      <td style="width:45%; padding:0 16px 0 0;">
        <div style="border-top:1px solid #1a1a1a; padding-top:8px; margin-top:40px;">
          <div style="font-weight:600;">{{ optional($lease->landlord)->name ?? 'Landlord' }}</div>
          <div style="font-size:9px; color:#555;">Landlord Signature &amp; Date</div>
        </div>
      </td>
      <td style="width:10%;"></td>
      <td style="width:45%; padding:0 0 0 16px;">
        <div style="border-top:1px solid #1a1a1a; padding-top:8px; margin-top:40px;">
          <div style="font-weight:600;">{{ optional($lease->tenant)->name ?? 'Tenant' }}</div>
          <div style="font-size:9px; color:#555;">Tenant Signature &amp; Date</div>
        </div>
      </td>
    </tr>
    <tr>
      <td colspan="3" style="padding-top:24px; text-align:center;">
        <div style="border-top:1px solid #1a1a1a; padding-top:8px; margin-top:40px; display:inline-block; min-width:200px;">
          <div style="font-size:9px; color:#555;">Witness Signature &amp; Date</div>
        </div>
      </td>
    </tr>
  </table>
</div>

<div class="footer">
  This document was generated by EstateYard ERP Platform on {{ now()->format('d F Y \a\t H:i') }}. Agreement reference: LA-{{ str_pad($lease->id, 6, '0', STR_PAD_LEFT) }}
</div>

</body>
</html>
