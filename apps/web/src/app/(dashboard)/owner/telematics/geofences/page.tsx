'use client';
import { useState, useEffect } from 'react';
import api from '@/lib/api';

interface Geofence { id: number; name: string; fence_type: string; radius_meters?: number; is_active: boolean; created_at: string }

export default function GeofencesPage() {
  const [fences, setFences] = useState<Geofence[]>([]);
  const [showForm, setShowForm] = useState(false);
  const [form, setForm] = useState({ name: '', fence_type: 'circle', center_lat: '', center_lng: '', radius_meters: '500' });
  const [saving, setSaving] = useState(false);

  useEffect(() => { api.get('/owner/geofences').then(r => setFences(r.data.data)); }, []);

  const save = async () => {
    setSaving(true);
    try {
      const payload: Record<string, unknown> = { name: form.name, fence_type: form.fence_type, is_active: true };
      if (form.fence_type === 'circle') {
        payload.center_lat = parseFloat(form.center_lat);
        payload.center_lng = parseFloat(form.center_lng);
        payload.radius_meters = parseInt(form.radius_meters);
      }
      const r = await api.post('/owner/geofences', payload);
      setFences(f => [r.data.data, ...f]);
      setShowForm(false);
    } finally { setSaving(false); }
  };

  return (
    <div className="max-w-4xl mx-auto px-4 py-8">
      <div className="flex items-center justify-between mb-6">
        <h1 className="text-2xl font-bold text-white">Geofence Management</h1>
        <button onClick={() => setShowForm(true)} className="bg-[#E8922A] hover:bg-[#E8922A]/90 text-white px-4 py-2 rounded-lg text-sm font-medium">+ New Geofence</button>
      </div>

      {showForm && (
        <div className="bg-[#141D2B] rounded-xl p-6 mb-6">
          <h2 className="text-white font-semibold mb-4">Create Geofence</h2>
          <div className="grid grid-cols-2 gap-4">
            <div className="col-span-2"><label className="text-[#7088A8] text-xs">Name</label><input value={form.name} onChange={e => setForm(f => ({...f, name: e.target.value}))} className="w-full mt-1 bg-[#080C12] border border-[#7088A8]/20 rounded-lg px-3 py-2 text-white text-sm"/></div>
            <div><label className="text-[#7088A8] text-xs">Type</label>
              <select value={form.fence_type} onChange={e => setForm(f => ({...f, fence_type: e.target.value}))} className="w-full mt-1 bg-[#080C12] border border-[#7088A8]/20 rounded-lg px-3 py-2 text-white text-sm">
                <option value="circle">Circle</option><option value="polygon">Polygon</option>
              </select>
            </div>
            {form.fence_type === 'circle' && <>
              <div><label className="text-[#7088A8] text-xs">Radius (m)</label><input type="number" value={form.radius_meters} onChange={e => setForm(f => ({...f, radius_meters: e.target.value}))} className="w-full mt-1 bg-[#080C12] border border-[#7088A8]/20 rounded-lg px-3 py-2 text-white text-sm"/></div>
              <div><label className="text-[#7088A8] text-xs">Center Latitude</label><input value={form.center_lat} onChange={e => setForm(f => ({...f, center_lat: e.target.value}))} className="w-full mt-1 bg-[#080C12] border border-[#7088A8]/20 rounded-lg px-3 py-2 text-white text-sm" placeholder="-1.286389"/></div>
              <div><label className="text-[#7088A8] text-xs">Center Longitude</label><input value={form.center_lng} onChange={e => setForm(f => ({...f, center_lng: e.target.value}))} className="w-full mt-1 bg-[#080C12] border border-[#7088A8]/20 rounded-lg px-3 py-2 text-white text-sm" placeholder="36.817223"/></div>
            </>}
          </div>
          <div className="flex gap-3 mt-4">
            <button onClick={save} disabled={saving} className="bg-[#E8922A] hover:bg-[#E8922A]/90 disabled:opacity-50 text-white px-6 py-2 rounded-lg text-sm font-medium">{saving ? 'Saving…' : 'Create'}</button>
            <button onClick={() => setShowForm(false)} className="text-[#7088A8] hover:text-white px-4 py-2 text-sm">Cancel</button>
          </div>
        </div>
      )}

      <div className="space-y-3">
        {fences.map(f => (
          <div key={f.id} className="bg-[#141D2B] rounded-xl p-4 flex items-center justify-between">
            <div>
              <p className="text-white font-medium">{f.name}</p>
              <p className="text-xs text-[#7088A8] mt-1">{f.fence_type} · {f.radius_meters ? `${f.radius_meters}m radius` : 'polygon'} · Created {new Date(f.created_at).toLocaleDateString()}</p>
            </div>
            <span className={`px-2 py-1 rounded text-xs font-medium ${f.is_active ? 'bg-[#2ECC8A]/10 text-[#2ECC8A]' : 'bg-[#E05252]/10 text-[#E05252]'}`}>{f.is_active ? 'Active' : 'Inactive'}</span>
          </div>
        ))}
        {fences.length === 0 && <p className="text-center text-[#7088A8] py-12">No geofences configured yet.</p>}
      </div>
    </div>
  );
}
