'use client';
import { useState, useEffect } from 'react';
import api from '@/lib/api';

interface DataProduct { id: number; name: string; description: string; product_type: string; price_kes_monthly: number; is_active: boolean; subscriptions_count?: number }

export default function DataMarketplacePage() {
  const [products, setProducts] = useState<DataProduct[]>([]);
  const [showCreate, setShowCreate] = useState(false);
  const [form, setForm] = useState({ name: '', description: '', product_type: 'pricing_index', price_kes_monthly: '' });
  const [saving, setSaving] = useState(false);

  useEffect(() => { api.get('/superadmin/data-marketplace/products').then(r => setProducts(r.data.data)); }, []);

  const create = async () => {
    setSaving(true);
    try {
      const r = await api.post('/superadmin/data-marketplace/products', { ...form, price_kes_monthly: parseFloat(form.price_kes_monthly) });
      setProducts(p => [r.data.data, ...p]);
      setShowCreate(false);
    } finally { setSaving(false); }
  };

  return (
    <div className="max-w-5xl mx-auto px-4 py-8">
      <div className="flex items-center justify-between mb-6">
        <div><h1 className="text-2xl font-bold text-white">Data Marketplace</h1><p className="text-[#7088A8] text-sm">Monetise anonymised platform data through API subscriptions.</p></div>
        <button onClick={() => setShowCreate(true)} className="bg-[#E8922A] hover:bg-[#E8922A]/90 text-white px-4 py-2 rounded-lg text-sm font-medium">+ New Product</button>
      </div>

      {showCreate && (
        <div className="bg-[#141D2B] rounded-xl p-6 mb-6">
          <h2 className="text-white font-semibold mb-4">Create Data Product</h2>
          <div className="grid grid-cols-2 gap-4">
            <div className="col-span-2"><label className="text-[#7088A8] text-xs">Product Name</label><input value={form.name} onChange={e => setForm(f=>({...f, name: e.target.value}))} className="w-full mt-1 bg-[#080C12] border border-[#7088A8]/20 rounded-lg px-3 py-2 text-white text-sm"/></div>
            <div className="col-span-2"><label className="text-[#7088A8] text-xs">Description</label><textarea value={form.description} onChange={e => setForm(f=>({...f, description: e.target.value}))} rows={2} className="w-full mt-1 bg-[#080C12] border border-[#7088A8]/20 rounded-lg px-3 py-2 text-white text-sm"/></div>
            <div><label className="text-[#7088A8] text-xs">Type</label>
              <select value={form.product_type} onChange={e => setForm(f=>({...f, product_type: e.target.value}))} className="w-full mt-1 bg-[#080C12] border border-[#7088A8]/20 rounded-lg px-3 py-2 text-white text-sm">
                <option value="pricing_index">Pricing Index</option><option value="demand_analytics">Demand Analytics</option><option value="market_report">Market Report</option><option value="raw_api">Raw API Feed</option>
              </select>
            </div>
            <div><label className="text-[#7088A8] text-xs">Monthly Price (KES)</label><input type="number" value={form.price_kes_monthly} onChange={e => setForm(f=>({...f, price_kes_monthly: e.target.value}))} className="w-full mt-1 bg-[#080C12] border border-[#7088A8]/20 rounded-lg px-3 py-2 text-white text-sm"/></div>
          </div>
          <div className="flex gap-3 mt-4">
            <button onClick={create} disabled={saving} className="bg-[#E8922A] hover:bg-[#E8922A]/90 disabled:opacity-50 text-white px-6 py-2 rounded-lg text-sm font-medium">{saving ? 'Creating…' : 'Create'}</button>
            <button onClick={() => setShowCreate(false)} className="text-[#7088A8] hover:text-white px-4 py-2 text-sm">Cancel</button>
          </div>
        </div>
      )}

      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
        {products.map(p => (
          <div key={p.id} className="bg-[#141D2B] rounded-xl p-5 border border-[#7088A8]/10">
            <div className="flex items-start justify-between mb-2">
              <h3 className="text-white font-semibold">{p.name}</h3>
              <span className={`px-2 py-0.5 rounded text-xs ${p.is_active ? 'text-[#2ECC8A] bg-[#2ECC8A]/10' : 'text-[#E05252] bg-[#E05252]/10'}`}>{p.is_active ? 'Active' : 'Inactive'}</span>
            </div>
            <p className="text-[#7088A8] text-sm mb-3">{p.description}</p>
            <div className="flex items-center justify-between">
              <span className="text-[#E8922A] font-semibold">KES {parseFloat(String(p.price_kes_monthly)).toLocaleString()}<span className="text-[#7088A8] font-normal text-xs">/mo</span></span>
              <span className="text-xs text-[#7088A8]">{p.subscriptions_count || 0} subscribers</span>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
