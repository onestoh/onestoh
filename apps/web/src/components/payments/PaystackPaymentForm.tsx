'use client';
import { useState } from 'react';
import api from '@/lib/api';

interface Props { bookingId: string; amountNgn: number; email: string; onSuccess: () => void; onCancel: () => void }

export default function PaystackPaymentForm({ bookingId, amountNgn, email, onSuccess, onCancel }: Props) {
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  const initPayment = async () => {
    setLoading(true);
    setError('');
    try {
      const r = await api.post('/payments/paystack/initialize', { booking_id: bookingId, email, amount_ngn: amountNgn });
      if (r.data.authorization_url) {
        window.location.href = r.data.authorization_url;
      }
    } catch (err: unknown) {
      const axiosErr = err as { response?: { data?: { message?: string } } };
      setError(axiosErr.response?.data?.message || 'Payment initialization failed');
      setLoading(false);
    }
  };

  return (
    <div className="bg-[#141D2B] rounded-xl p-6">
      <div className="flex items-center gap-2 mb-4">
        <span className="text-xl">💳</span>
        <h3 className="text-white font-semibold">Pay with Paystack</h3>
      </div>
      <div className="bg-[#080C12] rounded-lg p-4 mb-4">
        <p className="text-[#7088A8] text-xs mb-1">Amount to pay</p>
        <p className="text-white text-2xl font-bold">₦{amountNgn.toLocaleString()}</p>
        <p className="text-xs text-[#7088A8] mt-1">Charged to: {email}</p>
      </div>
      <div className="bg-[#E8922A]/10 rounded-lg p-3 mb-4 text-xs text-[#7088A8]">
        Accepts Nigerian bank cards, bank transfer, USSD (*737#, *919#), and mobile wallets.
      </div>
      {error && <div className="bg-[#E05252]/10 border border-[#E05252]/30 rounded-lg p-3 text-[#E05252] text-sm mb-4">{error}</div>}
      <div className="flex gap-3">
        <button onClick={initPayment} disabled={loading} className="flex-1 bg-[#E8922A] hover:bg-[#E8922A]/90 disabled:opacity-50 text-white py-3 rounded-xl font-semibold">{loading ? 'Redirecting…' : 'Pay Now'}</button>
        <button onClick={onCancel} className="px-4 py-3 text-[#7088A8] hover:text-white text-sm">Cancel</button>
      </div>
    </div>
  );
}
