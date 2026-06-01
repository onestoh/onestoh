'use client';
import { useState, useEffect } from 'react';
import api from '@/lib/api';

interface ErpIntegration { id: number; platform: string; status: string; last_sync_at?: string; sync_errors?: string[]; created_at: string }

export default function ErpIntegrationsPage() {
  const [integrations, setIntegrations] = useState<ErpIntegration[]>([]);
  const [syncing, setSyncing] = useState<number|null>(null);

  useEffect(() => { api.get('/erp/integrations').then(r => setIntegrations(r.data.data)); }, []);

  const connect = (platform: string) => {
    api.post('/erp/integrations', { platform }).then(r => {
      if (r.data.redirect_url) window.location.href = r.data.redirect_url;
      else setIntegrations(i => [...i, r.data.data]);
    });
  };

  const sync = async (id: number) => {
    setSyncing(id);
    try { await api.post(`/erp/integrations/${id}/sync`); } finally { setSyncing(null); }
  };

  const statusColor = (s: string) => ({ connected: 'text-[#2ECC8A] bg-[#2ECC8A]/10', error: 'text-[#E05252] bg-[#E05252]/10', pending: 'text-[#E8922A] bg-[#E8922A]/10' }[s] || 'text-[#7088A8] bg-[#7088A8]/10');

  const platforms = [
    { id: 'quickbooks', name: 'QuickBooks', logo: '📊', desc: 'Sync invoices and expenses with QuickBooks Online' },
    { id: 'xero', name: 'Xero', logo: '🔵', desc: 'Connect your Xero account for automatic bookkeeping' },
  ];

  const connected = (p: string) => integrations.find(i => i.platform === p);

  return (
    <div className="max-w-4xl mx-auto px-4 py-8">
      <h1 className="text-2xl font-bold text-white mb-2">ERP Integrations</h1>
      <p className="text-[#7088A8] mb-8">Connect your accounting software to automatically sync invoices, payments, and expenses.</p>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        {platforms.map(p => {
          const conn = connected(p.id);
          return (
            <div key={p.id} className="bg-[#141D2B] rounded-xl p-6 border border-[#7088A8]/10">
              <div className="flex items-center gap-3 mb-3"><span className="text-3xl">{p.logo}</span><div><h3 className="text-white font-semibold">{p.name}</h3><p className="text-xs text-[#7088A8]">{p.desc}</p></div></div>
              {conn ? (
                <div>
                  <div className="flex items-center justify-between mb-3">
                    <span className={`px-2 py-1 rounded text-xs font-medium ${statusColor(conn.status)}`}>{conn.status}</span>
                    {conn.last_sync_at && <span className="text-xs text-[#7088A8]">Last sync: {new Date(conn.last_sync_at).toLocaleString()}</span>}
                  </div>
                  <button onClick={() => sync(conn.id)} disabled={syncing===conn.id} className="w-full border border-[#E8922A] text-[#E8922A] hover:bg-[#E8922A] hover:text-white py-2 rounded-lg text-sm font-medium transition-colors disabled:opacity-50">{syncing===conn.id ? 'Syncing…' : 'Sync Now'}</button>
                </div>
              ) : (
                <button onClick={() => connect(p.id)} className="w-full bg-[#E8922A] hover:bg-[#E8922A]/90 text-white py-2 rounded-lg text-sm font-medium">Connect {p.name}</button>
              )}
            </div>
          );
        })}
      </div>

      {integrations.length > 0 && (
        <div className="bg-[#141D2B] rounded-xl p-6">
          <h2 className="text-white font-semibold mb-4">Sync History</h2>
          <div className="space-y-2">
            {integrations.map(i => (
              <div key={i.id} className="flex items-center justify-between py-2 border-b border-[#7088A8]/10 last:border-0">
                <span className="text-white text-sm capitalize">{i.platform}</span>
                <span className={`px-2 py-0.5 rounded text-xs ${statusColor(i.status)}`}>{i.status}</span>
              </div>
            ))}
          </div>
        </div>
      )}
    </div>
  );
}
