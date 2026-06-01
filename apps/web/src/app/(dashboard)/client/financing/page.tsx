'use client';
import { useState, useEffect } from 'react';
import api from '@/lib/api';
import { useRouter } from 'next/navigation';

interface Partner { id: number; name: string; partner_type: string; min_loan_kes: number; max_loan_kes: number; interest_rate_annual: number; max_tenure_months: number; logo_url?: string }
interface Application { id: number; partner?: Partner; loan_amount_kes: number; tenure_months: number; status: string; monthly_payment_kes: number; created_at: string }

export default function FinancingPage() {
  const router = useRouter();
  const [partners, setPartners] = useState<Partner[]>([]);
  const [applications, setApplications] = useState<Application[]>([]);
  const [selected, setSelected] = useState<Partner | null>(null);
  const [form, setForm] = useState({ asset_id: '', loan_amount_kes: '', tenure_months: '36', purpose: '', monthly_income_kes: '' });
  const [monthly, setMonthly] = useState(0);
  const [submitting, setSubmitting] = useState(false);
  const [tab, setTab] = useState<'browse'|'apply'|'applications'>('browse');

  useEffect(() => {
    api.get('/financing/partners').then(r => setPartners(r.data.data));
    api.get('/financing/my-applications').then(r => setApplications(r.data.data));
  }, []);

  useEffect(() => {
    if (!selected || !form.loan_amount_kes) return;
    const P = parseFloat(form.loan_amount_kes);
    const r = selected.interest_rate_annual / 100 / 12;
    const n = parseInt(form.tenure_months);
    if (r === 0) { setMonthly(P / n); return; }
    setMonthly(P * r * Math.pow(1+r,n) / (Math.pow(1+r,n)-1));
  }, [form.loan_amount_kes, form.tenure_months, selected]);

  const submit = async () => {
    if (!selected) return;
    setSubmitting(true);
    try {
      await api.post('/financing/apply', { ...form, financing_partner_id: selected.id, loan_amount_kes: parseFloat(form.loan_amount_kes), tenure_months: parseInt(form.tenure_months), monthly_income_kes: parseFloat(form.monthly_income_kes) });
      setTab('applications');
      api.get('/financing/my-applications').then(r => setApplications(r.data.data));
    } finally { setSubmitting(false); }
  };

  const statusColor = (s: string) => ({ approved: 'text-[#2ECC8A] bg-[#2ECC8A]/10', rejected: 'text-[#E05252] bg-[#E05252]/10', pending: 'text-[#E8922A] bg-[#E8922A]/10' }[s] || 'text-[#7088A8] bg-[#7088A8]/10');

  return (
    <div className="max-w-5xl mx-auto px-4 py-8">
      <h1 className="text-2xl font-bold text-white mb-2">Fleet Financing</h1>
      <p className="text-[#7088A8] mb-6">Get financing from our partner banks and SACCOs to purchase or lease-to-own vehicles.</p>

      <div className="flex gap-2 mb-6">
        {(['browse','apply','applications'] as const).map(t => (
          <button key={t} onClick={() => setTab(t)} className={`px-4 py-2 rounded-lg text-sm font-medium capitalize transition-colors ${tab===t ? 'bg-[#E8922A] text-white' : 'text-[#7088A8] hover:text-white'}`}>{t}</button>
        ))}
      </div>

      {tab === 'browse' && (
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          {partners.map(p => (
            <div key={p.id} className="bg-[#141D2B] rounded-xl p-6 border border-[#7088A8]/10">
              <div className="flex items-start justify-between mb-4">
                <div>
                  <h3 className="text-white font-semibold text-lg">{p.name}</h3>
                  <span className="text-xs text-[#7088A8] capitalize">{p.partner_type.replace('_',' ')}</span>
                </div>
                <span className="text-2xl font-bold text-[#E8922A]">{p.interest_rate_annual}%<span className="text-sm font-normal text-[#7088A8]">/yr</span></span>
              </div>
              <div className="grid grid-cols-2 gap-3 text-sm mb-4">
                <div className="bg-[#080C12] rounded-lg p-3"><p className="text-[#7088A8] text-xs">Min Loan</p><p className="text-white font-medium">KES {p.min_loan_kes.toLocaleString()}</p></div>
                <div className="bg-[#080C12] rounded-lg p-3"><p className="text-[#7088A8] text-xs">Max Loan</p><p className="text-white font-medium">KES {p.max_loan_kes.toLocaleString()}</p></div>
                <div className="bg-[#080C12] rounded-lg p-3 col-span-2"><p className="text-[#7088A8] text-xs">Max Tenure</p><p className="text-white font-medium">{p.max_tenure_months} months</p></div>
              </div>
              <button onClick={() => { setSelected(p); setTab('apply'); }} className="w-full bg-[#E8922A] hover:bg-[#E8922A]/90 text-white py-2 rounded-lg text-sm font-medium">Apply Now</button>
            </div>
          ))}
        </div>
      )}

      {tab === 'apply' && (
        <div className="max-w-lg">
          {selected && <div className="bg-[#141D2B] rounded-xl p-4 mb-6 flex items-center gap-3"><div className="flex-1"><p className="text-white font-medium">{selected.name}</p><p className="text-xs text-[#7088A8]">{selected.interest_rate_annual}% p.a. · up to {selected.max_tenure_months} months</p></div><button onClick={() => setSelected(null)} className="text-[#7088A8] hover:text-white text-xs">Change</button></div>}
          {!selected && <button onClick={() => setTab('browse')} className="text-[#E8922A] text-sm mb-4">← Select a partner first</button>}
          <div className="bg-[#141D2B] rounded-xl p-6 space-y-4">
            <div><label className="text-[#7088A8] text-xs">Loan Amount (KES)</label><input type="number" value={form.loan_amount_kes} onChange={e => setForm(f=>({...f, loan_amount_kes: e.target.value}))} className="w-full mt-1 bg-[#080C12] border border-[#7088A8]/20 rounded-lg px-3 py-2 text-white text-sm"/></div>
            <div><label className="text-[#7088A8] text-xs">Tenure (months)</label>
              <select value={form.tenure_months} onChange={e => setForm(f=>({...f, tenure_months: e.target.value}))} className="w-full mt-1 bg-[#080C12] border border-[#7088A8]/20 rounded-lg px-3 py-2 text-white text-sm">
                {[12,24,36,48,60].map(m => <option key={m} value={m}>{m} months</option>)}
              </select>
            </div>
            <div><label className="text-[#7088A8] text-xs">Monthly Income (KES)</label><input type="number" value={form.monthly_income_kes} onChange={e => setForm(f=>({...f, monthly_income_kes: e.target.value}))} className="w-full mt-1 bg-[#080C12] border border-[#7088A8]/20 rounded-lg px-3 py-2 text-white text-sm"/></div>
            <div><label className="text-[#7088A8] text-xs">Purpose</label><textarea value={form.purpose} onChange={e => setForm(f=>({...f, purpose: e.target.value}))} rows={3} className="w-full mt-1 bg-[#080C12] border border-[#7088A8]/20 rounded-lg px-3 py-2 text-white text-sm"/></div>
            {monthly > 0 && <div className="bg-[#E8922A]/10 border border-[#E8922A]/30 rounded-lg p-4 text-center"><p className="text-[#7088A8] text-xs">Estimated Monthly Payment</p><p className="text-[#E8922A] text-2xl font-bold">KES {monthly.toLocaleString(undefined,{maximumFractionDigits:0})}</p></div>}
            <button onClick={submit} disabled={submitting||!selected} className="w-full bg-[#E8922A] hover:bg-[#E8922A]/90 disabled:opacity-50 text-white py-3 rounded-lg font-medium">{submitting ? 'Submitting…' : 'Submit Application'}</button>
          </div>
        </div>
      )}

      {tab === 'applications' && (
        <div className="space-y-4">
          {applications.map(a => (
            <div key={a.id} className="bg-[#141D2B] rounded-xl p-5">
              <div className="flex items-start justify-between mb-3">
                <div><p className="text-white font-medium">{a.partner?.name || 'Partner'}</p><p className="text-xs text-[#7088A8]">Applied {new Date(a.created_at).toLocaleDateString()}</p></div>
                <span className={`px-2 py-1 rounded text-xs font-medium capitalize ${statusColor(a.status)}`}>{a.status}</span>
              </div>
              <div className="grid grid-cols-3 gap-3 text-sm">
                <div><p className="text-[#7088A8] text-xs">Loan Amount</p><p className="text-white">KES {a.loan_amount_kes.toLocaleString()}</p></div>
                <div><p className="text-[#7088A8] text-xs">Tenure</p><p className="text-white">{a.tenure_months} months</p></div>
                <div><p className="text-[#7088A8] text-xs">Monthly</p><p className="text-white">KES {a.monthly_payment_kes.toLocaleString()}</p></div>
              </div>
            </div>
          ))}
          {applications.length === 0 && <p className="text-center text-[#7088A8] py-12">No financing applications yet.</p>}
        </div>
      )}
    </div>
  );
}
