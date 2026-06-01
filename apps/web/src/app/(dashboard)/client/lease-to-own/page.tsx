'use client';
import { useState, useEffect } from 'react';
import api from '@/lib/api';

interface Lease { id: number; asset?: { name: string }; total_asset_value_kes: number; monthly_payment_kes: number; tenure_months: number; amount_paid_kes: number; status: string; next_payment_date: string }

export default function LeaseToOwnPage() {
  const [leases, setLeases] = useState<Lease[]>([]);

  useEffect(() => { api.get('/financing/my-leases').then(r => setLeases(r.data.data)); }, []);

  const pct = (l: Lease) => Math.min(100, (l.amount_paid_kes / l.total_asset_value_kes) * 100);

  return (
    <div className="max-w-4xl mx-auto px-4 py-8">
      <h1 className="text-2xl font-bold text-white mb-6">Lease-to-Own Agreements</h1>
      {leases.map(l => (
        <div key={l.id} className="bg-[#141D2B] rounded-xl p-6 mb-4">
          <div className="flex items-start justify-between mb-4">
            <div><h3 className="text-white font-semibold text-lg">{l.asset?.name || 'Asset'}</h3><p className="text-xs text-[#7088A8] capitalize">{l.status}</p></div>
            <div className="text-right"><p className="text-[#7088A8] text-xs">Next Payment</p><p className="text-white text-sm">{new Date(l.next_payment_date).toLocaleDateString()}</p></div>
          </div>
          <div className="mb-4">
            <div className="flex justify-between text-xs text-[#7088A8] mb-1"><span>Progress</span><span>{pct(l).toFixed(1)}%</span></div>
            <div className="bg-[#080C12] rounded-full h-3"><div className="bg-[#E8922A] h-3 rounded-full transition-all" style={{width:`${pct(l)}%`}}/></div>
            <div className="flex justify-between text-xs text-[#7088A8] mt-1"><span>KES {l.amount_paid_kes.toLocaleString()} paid</span><span>KES {l.total_asset_value_kes.toLocaleString()} total</span></div>
          </div>
          <div className="grid grid-cols-3 gap-3 text-sm">
            <div className="bg-[#080C12] rounded-lg p-3"><p className="text-[#7088A8] text-xs">Monthly</p><p className="text-white font-medium">KES {l.monthly_payment_kes.toLocaleString()}</p></div>
            <div className="bg-[#080C12] rounded-lg p-3"><p className="text-[#7088A8] text-xs">Tenure</p><p className="text-white font-medium">{l.tenure_months} months</p></div>
            <div className="bg-[#080C12] rounded-lg p-3"><p className="text-[#7088A8] text-xs">Remaining</p><p className="text-white font-medium">KES {(l.total_asset_value_kes - l.amount_paid_kes).toLocaleString()}</p></div>
          </div>
        </div>
      ))}
      {leases.length === 0 && <p className="text-center text-[#7088A8] py-12">No lease-to-own agreements.</p>}
    </div>
  );
}
