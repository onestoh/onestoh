'use client';
import { useState, useEffect } from 'react';
import api from '@/lib/api';
import Link from 'next/link';

interface Tender { id: number; title: string; entity?: { name: string }; tender_number: string; category: string; budget_min_kes: number; budget_max_kes: number; submission_deadline: string; status: string; bids_count?: number }

export default function ProcurementPage() {
  const [tenders, setTenders] = useState<Tender[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [category, setCategory] = useState('all');
  const categories = ['all','vehicles','heavy_equipment','construction','transport_services'];

  useEffect(() => {
    api.get('/procurement/tenders', { params: { search: search||undefined, category: category==='all'?undefined:category } })
      .then(r => { setTenders(r.data.data); setLoading(false); });
  }, [search, category]);

  const daysLeft = (d: string) => Math.max(0, Math.ceil((new Date(d).getTime() - Date.now()) / 86400000));
  const urgency = (d: string) => { const days = daysLeft(d); return days <= 3 ? 'text-[#E05252]' : days <= 7 ? 'text-[#E8922A]' : 'text-[#2ECC8A]'; };

  if (loading) return <div className="flex items-center justify-center h-64"><div className="animate-spin w-8 h-8 border-2 border-[#E8922A] border-t-transparent rounded-full"/></div>;

  return (
    <div className="max-w-6xl mx-auto px-4 py-8">
      <div className="mb-8">
        <h1 className="text-2xl font-bold text-white mb-2">Government Procurement Tenders</h1>
        <p className="text-[#7088A8]">Browse and bid on government vehicle and equipment procurement tenders.</p>
      </div>

      <div className="flex flex-col sm:flex-row gap-4 mb-6">
        <input value={search} onChange={e => setSearch(e.target.value)} placeholder="Search tenders…" className="flex-1 bg-[#141D2B] border border-[#7088A8]/20 rounded-lg px-4 py-2 text-white text-sm placeholder-[#7088A8]"/>
        <select value={category} onChange={e => setCategory(e.target.value)} className="bg-[#141D2B] border border-[#7088A8]/20 rounded-lg px-3 py-2 text-white text-sm">
          {categories.map(c => <option key={c} value={c}>{c === 'all' ? 'All Categories' : c.replace(/_/g,' ').replace(/\b\w/g,l=>l.toUpperCase())}</option>)}
        </select>
      </div>

      <div className="space-y-4">
        {tenders.map(t => (
          <div key={t.id} className="bg-[#141D2B] rounded-xl p-6 border border-[#7088A8]/10 hover:border-[#E8922A]/30 transition-colors">
            <div className="flex items-start justify-between mb-3">
              <div className="flex-1">
                <div className="flex items-center gap-2 mb-1">
                  <span className="text-xs bg-[#E8922A]/10 text-[#E8922A] px-2 py-0.5 rounded font-mono">{t.tender_number}</span>
                  <span className="text-xs text-[#7088A8] capitalize">{t.category.replace(/_/g,' ')}</span>
                </div>
                <h3 className="text-white font-semibold text-lg">{t.title}</h3>
                <p className="text-[#7088A8] text-sm mt-1">{t.entity?.name}</p>
              </div>
              <span className={`text-sm font-medium ${urgency(t.submission_deadline)}`}>{daysLeft(t.submission_deadline)}d left</span>
            </div>
            <div className="flex items-center justify-between">
              <div className="flex gap-4 text-sm">
                <span className="text-[#7088A8]">Budget: <span className="text-white">KES {t.budget_min_kes.toLocaleString()} – {t.budget_max_kes.toLocaleString()}</span></span>
                <span className="text-[#7088A8]">Bids: <span className="text-white">{t.bids_count || 0}</span></span>
              </div>
              <Link href={`/procurement/${t.id}`} className="bg-[#E8922A] hover:bg-[#E8922A]/90 text-white px-4 py-2 rounded-lg text-sm font-medium">View & Bid</Link>
            </div>
          </div>
        ))}
        {tenders.length === 0 && <p className="text-center text-[#7088A8] py-12">No tenders found.</p>}
      </div>
    </div>
  );
}
