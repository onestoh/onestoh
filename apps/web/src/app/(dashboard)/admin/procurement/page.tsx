'use client';
import { useState, useEffect } from 'react';
import api from '@/lib/api';

interface Tender { id: number; title: string; tender_number: string; status: string; bids_count?: number; submission_deadline: string; budget_max_kes: number }

export default function AdminProcurementPage() {
  const [tenders, setTenders] = useState<Tender[]>([]);
  const [showCreate, setShowCreate] = useState(false);
  const [form, setForm] = useState({ title: '', description: '', category: 'vehicles', budget_min_kes: '', budget_max_kes: '', submission_deadline: '', delivery_location: '' });
  const [saving, setSaving] = useState(false);

  useEffect(() => { api.get('/admin/procurement/tenders').then(r => setTenders(r.data.data)); }, []);

  const create = async () => {
    setSaving(true);
    try {
      const r = await api.post('/admin/procurement/tenders', { ...form, budget_min_kes: parseFloat(form.budget_min_kes), budget_max_kes: parseFloat(form.budget_max_kes) });
      setTenders(t => [r.data.data, ...t]);
      setShowCreate(false);
    } finally { setSaving(false); }
  };

  const statusColor = (s: string) => ({ published: 'text-[#2ECC8A] bg-[#2ECC8A]/10', draft: 'text-[#7088A8] bg-[#7088A8]/10', closed: 'text-[#E05252] bg-[#E05252]/10', awarded: 'text-[#E8922A] bg-[#E8922A]/10' }[s] || 'text-[#7088A8] bg-[#7088A8]/10');

  return (
    <div className="max-w-6xl mx-auto px-4 py-8">
      <div className="flex items-center justify-between mb-6">
        <h1 className="text-2xl font-bold text-white">Procurement Management</h1>
        <button onClick={() => setShowCreate(true)} className="bg-[#E8922A] hover:bg-[#E8922A]/90 text-white px-4 py-2 rounded-lg text-sm font-medium">+ Create Tender</button>
      </div>

      {showCreate && (
        <div className="bg-[#141D2B] rounded-xl p-6 mb-6">
          <h2 className="text-white font-semibold mb-4">New Tender</h2>
          <div className="grid grid-cols-2 gap-4">
            <div className="col-span-2"><label className="text-[#7088A8] text-xs">Title</label><input value={form.title} onChange={e => setForm(f=>({...f, title: e.target.value}))} className="w-full mt-1 bg-[#080C12] border border-[#7088A8]/20 rounded-lg px-3 py-2 text-white text-sm"/></div>
            <div className="col-span-2"><label className="text-[#7088A8] text-xs">Description</label><textarea value={form.description} onChange={e => setForm(f=>({...f, description: e.target.value}))} rows={3} className="w-full mt-1 bg-[#080C12] border border-[#7088A8]/20 rounded-lg px-3 py-2 text-white text-sm"/></div>
            <div><label className="text-[#7088A8] text-xs">Category</label><select value={form.category} onChange={e => setForm(f=>({...f, category: e.target.value}))} className="w-full mt-1 bg-[#080C12] border border-[#7088A8]/20 rounded-lg px-3 py-2 text-white text-sm"><option value="vehicles">Vehicles</option><option value="heavy_equipment">Heavy Equipment</option><option value="transport_services">Transport Services</option></select></div>
            <div><label className="text-[#7088A8] text-xs">Submission Deadline</label><input type="datetime-local" value={form.submission_deadline} onChange={e => setForm(f=>({...f, submission_deadline: e.target.value}))} className="w-full mt-1 bg-[#080C12] border border-[#7088A8]/20 rounded-lg px-3 py-2 text-white text-sm"/></div>
            <div><label className="text-[#7088A8] text-xs">Budget Min (KES)</label><input type="number" value={form.budget_min_kes} onChange={e => setForm(f=>({...f, budget_min_kes: e.target.value}))} className="w-full mt-1 bg-[#080C12] border border-[#7088A8]/20 rounded-lg px-3 py-2 text-white text-sm"/></div>
            <div><label className="text-[#7088A8] text-xs">Budget Max (KES)</label><input type="number" value={form.budget_max_kes} onChange={e => setForm(f=>({...f, budget_max_kes: e.target.value}))} className="w-full mt-1 bg-[#080C12] border border-[#7088A8]/20 rounded-lg px-3 py-2 text-white text-sm"/></div>
          </div>
          <div className="flex gap-3 mt-4">
            <button onClick={create} disabled={saving} className="bg-[#E8922A] hover:bg-[#E8922A]/90 disabled:opacity-50 text-white px-6 py-2 rounded-lg text-sm font-medium">{saving ? 'Creating…' : 'Create Tender'}</button>
            <button onClick={() => setShowCreate(false)} className="text-[#7088A8] hover:text-white px-4 py-2 text-sm">Cancel</button>
          </div>
        </div>
      )}

      <div className="space-y-3">
        {tenders.map(t => (
          <div key={t.id} className="bg-[#141D2B] rounded-xl p-4 flex items-center justify-between">
            <div>
              <div className="flex items-center gap-2 mb-1"><span className="font-mono text-xs text-[#E8922A]">{t.tender_number}</span><span className={`px-2 py-0.5 rounded text-xs font-medium capitalize ${statusColor(t.status)}`}>{t.status}</span></div>
              <p className="text-white font-medium">{t.title}</p>
              <p className="text-xs text-[#7088A8] mt-0.5">Deadline: {new Date(t.submission_deadline).toLocaleDateString()} · {t.bids_count || 0} bids · KES {t.budget_max_kes.toLocaleString()}</p>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
