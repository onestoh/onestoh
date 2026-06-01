'use client';
import { useState, useEffect } from 'react';
import { useParams } from 'next/navigation';
import api from '@/lib/api';

interface Tender { id: number; title: string; description: string; entity?: { name: string }; tender_number: string; category: string; budget_min_kes: number; budget_max_kes: number; submission_deadline: string; evaluation_criteria?: Record<string, number>; required_documents?: string[]; status: string }

export default function TenderDetailPage() {
  const { tenderId } = useParams();
  const [tender, setTender] = useState<Tender | null>(null);
  const [form, setForm] = useState({ bid_amount_kes: '', technical_proposal: '', delivery_timeline_days: '' });
  const [submitting, setSubmitting] = useState(false);
  const [submitted, setSubmitted] = useState(false);

  useEffect(() => { api.get(`/procurement/tenders/${tenderId}`).then(r => setTender(r.data.data)); }, [tenderId]);

  const submit = async () => {
    setSubmitting(true);
    try {
      await api.post(`/procurement/tenders/${tenderId}/bid`, { bid_amount_kes: parseFloat(form.bid_amount_kes), technical_proposal: form.technical_proposal, delivery_timeline_days: parseInt(form.delivery_timeline_days) });
      setSubmitted(true);
    } finally { setSubmitting(false); }
  };

  if (!tender) return <div className="flex items-center justify-center h-64"><div className="animate-spin w-8 h-8 border-2 border-[#E8922A] border-t-transparent rounded-full"/></div>;

  return (
    <div className="max-w-4xl mx-auto px-4 py-8">
      <div className="bg-[#141D2B] rounded-xl p-6 mb-6">
        <div className="flex items-center gap-2 mb-3"><span className="text-xs bg-[#E8922A]/10 text-[#E8922A] px-2 py-0.5 rounded font-mono">{tender.tender_number}</span></div>
        <h1 className="text-2xl font-bold text-white mb-2">{tender.title}</h1>
        <p className="text-[#7088A8] mb-4">{tender.entity?.name}</p>
        <p className="text-white/80 leading-relaxed">{tender.description}</p>
        <div className="grid grid-cols-2 sm:grid-cols-3 gap-4 mt-6">
          <div className="bg-[#080C12] rounded-lg p-3"><p className="text-[#7088A8] text-xs">Min Budget</p><p className="text-white font-medium">KES {tender.budget_min_kes.toLocaleString()}</p></div>
          <div className="bg-[#080C12] rounded-lg p-3"><p className="text-[#7088A8] text-xs">Max Budget</p><p className="text-white font-medium">KES {tender.budget_max_kes.toLocaleString()}</p></div>
          <div className="bg-[#080C12] rounded-lg p-3"><p className="text-[#7088A8] text-xs">Deadline</p><p className="text-white font-medium">{new Date(tender.submission_deadline).toLocaleDateString()}</p></div>
        </div>
        {tender.evaluation_criteria && (
          <div className="mt-4">
            <p className="text-[#7088A8] text-xs uppercase tracking-wider mb-2">Evaluation Criteria</p>
            {Object.entries(tender.evaluation_criteria).map(([k,v]) => (
              <div key={k} className="flex items-center justify-between py-1.5 border-b border-[#7088A8]/10 last:border-0">
                <span className="text-white/80 text-sm capitalize">{k.replace(/_/g,' ')}</span>
                <div className="flex items-center gap-3"><div className="w-24 bg-[#080C12] rounded-full h-2"><div className="bg-[#E8922A] h-2 rounded-full" style={{width:`${v}%`}}/></div><span className="text-[#E8922A] text-sm font-medium w-8 text-right">{v}%</span></div>
              </div>
            ))}
          </div>
        )}
      </div>

      {submitted ? (
        <div className="bg-[#2ECC8A]/10 border border-[#2ECC8A]/30 rounded-xl p-6 text-center">
          <p className="text-[#2ECC8A] text-lg font-semibold">Bid Submitted Successfully!</p>
          <p className="text-[#7088A8] text-sm mt-2">You will be notified of the evaluation outcome.</p>
        </div>
      ) : (
        <div className="bg-[#141D2B] rounded-xl p-6">
          <h2 className="text-white font-semibold text-lg mb-4">Submit Your Bid</h2>
          <div className="space-y-4">
            <div><label className="text-[#7088A8] text-xs">Bid Amount (KES)</label><input type="number" value={form.bid_amount_kes} onChange={e => setForm(f=>({...f, bid_amount_kes: e.target.value}))} className="w-full mt-1 bg-[#080C12] border border-[#7088A8]/20 rounded-lg px-3 py-2 text-white text-sm"/></div>
            <div><label className="text-[#7088A8] text-xs">Delivery Timeline (days)</label><input type="number" value={form.delivery_timeline_days} onChange={e => setForm(f=>({...f, delivery_timeline_days: e.target.value}))} className="w-full mt-1 bg-[#080C12] border border-[#7088A8]/20 rounded-lg px-3 py-2 text-white text-sm"/></div>
            <div><label className="text-[#7088A8] text-xs">Technical Proposal</label><textarea value={form.technical_proposal} onChange={e => setForm(f=>({...f, technical_proposal: e.target.value}))} rows={5} className="w-full mt-1 bg-[#080C12] border border-[#7088A8]/20 rounded-lg px-3 py-2 text-white text-sm" placeholder="Describe your approach, qualifications, and how you meet the tender requirements…"/></div>
            <button onClick={submit} disabled={submitting} className="w-full bg-[#E8922A] hover:bg-[#E8922A]/90 disabled:opacity-50 text-white py-3 rounded-lg font-medium">{submitting ? 'Submitting…' : 'Submit Bid'}</button>
          </div>
        </div>
      )}
    </div>
  );
}
