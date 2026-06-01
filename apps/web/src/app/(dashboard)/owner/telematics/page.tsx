'use client';
import { useState, useEffect } from 'react';
import api from '@/lib/api';

interface Device { id: number; asset_id: number; device_id: string; is_active: boolean; last_ping_at: string; asset?: { name: string } }
interface Ping { id: number; latitude: number; longitude: number; speed_kmh: number; heading: number; recorded_at: string; ignition_on: boolean }
interface Trip { id: number; started_at: string; ended_at?: string; distance_km: number; duration_minutes: number; driving_score?: number; status: string }

export default function TelematicsPage() {
  const [devices, setDevices] = useState<Device[]>([]);
  const [selectedDevice, setSelectedDevice] = useState<Device | null>(null);
  const [latestPing, setLatestPing] = useState<Ping | null>(null);
  const [trips, setTrips] = useState<Trip[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    api.get('/owner/telematics/devices').then(r => { setDevices(r.data.data); setLoading(false); });
  }, []);

  useEffect(() => {
    if (!selectedDevice) return;
    api.get(`/owner/telematics/devices/${selectedDevice.id}/latest-ping`).then(r => setLatestPing(r.data.data));
    api.get(`/owner/telematics/devices/${selectedDevice.id}/trips`).then(r => setTrips(r.data.data));
    const interval = setInterval(() => {
      api.get(`/owner/telematics/devices/${selectedDevice.id}/latest-ping`).then(r => setLatestPing(r.data.data));
    }, 10000);
    return () => clearInterval(interval);
  }, [selectedDevice]);

  const scoreColor = (s?: number) => !s ? 'text-[#7088A8]' : s >= 80 ? 'text-[#2ECC8A]' : s >= 60 ? 'text-[#E8922A]' : 'text-[#E05252]';

  if (loading) return <div className="flex items-center justify-center h-64"><div className="animate-spin w-8 h-8 border-2 border-[#E8922A] border-t-transparent rounded-full"/></div>;

  return (
    <div className="max-w-7xl mx-auto px-4 py-8">
      <h1 className="text-2xl font-bold text-white mb-6">Live Fleet Tracking</h1>
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Device list */}
        <div className="bg-[#141D2B] rounded-xl p-4 space-y-3">
          <h2 className="text-sm font-semibold text-[#7088A8] uppercase tracking-wider">GPS Devices</h2>
          {devices.map(d => (
            <button key={d.id} onClick={() => setSelectedDevice(d)}
              className={`w-full text-left p-3 rounded-lg border transition-colors ${selectedDevice?.id === d.id ? 'border-[#E8922A] bg-[#E8922A]/10' : 'border-[#7088A8]/20 hover:border-[#E8922A]/50'}`}>
              <div className="flex items-center justify-between">
                <span className="text-white text-sm font-medium">{d.asset?.name || d.device_id}</span>
                <span className={`w-2 h-2 rounded-full ${d.is_active ? 'bg-[#2ECC8A]' : 'bg-[#E05252]'}`}/>
              </div>
              <p className="text-xs text-[#7088A8] mt-1">ID: {d.device_id}</p>
              {d.last_ping_at && <p className="text-xs text-[#7088A8]">Last ping: {new Date(d.last_ping_at).toLocaleTimeString()}</p>}
            </button>
          ))}
          {devices.length === 0 && <p className="text-[#7088A8] text-sm text-center py-4">No GPS devices registered</p>}
        </div>

        {/* Map placeholder + live data */}
        <div className="lg:col-span-2 space-y-4">
          <div className="bg-[#141D2B] rounded-xl overflow-hidden" style={{height: 320}}>
            {latestPing ? (
              <div className="w-full h-full flex flex-col items-center justify-center bg-[#0d1520] relative">
                <div className="absolute inset-0 opacity-20" style={{backgroundImage: 'radial-gradient(circle at 50% 50%, #E8922A 1px, transparent 1px)', backgroundSize: '40px 40px'}}/>
                <div className="relative z-10 text-center">
                  <div className="w-12 h-12 bg-[#E8922A] rounded-full flex items-center justify-center mx-auto mb-3 animate-pulse">
                    <svg className="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                  </div>
                  <p className="text-white font-semibold">{latestPing.latitude.toFixed(6)}, {latestPing.longitude.toFixed(6)}</p>
                  <p className="text-[#7088A8] text-sm mt-1">Speed: {latestPing.speed_kmh} km/h · Heading: {latestPing.heading}°</p>
                  <p className="text-xs text-[#7088A8] mt-1">Ignition: {latestPing.ignition_on ? '🟢 ON' : '🔴 OFF'}</p>
                </div>
              </div>
            ) : (
              <div className="w-full h-full flex items-center justify-center text-[#7088A8]">
                {selectedDevice ? 'No ping data yet' : 'Select a device to view location'}
              </div>
            )}
          </div>

          {/* Trip history */}
          {trips.length > 0 && (
            <div className="bg-[#141D2B] rounded-xl p-4">
              <h3 className="text-sm font-semibold text-[#7088A8] uppercase tracking-wider mb-3">Recent Trips</h3>
              <div className="space-y-2">
                {trips.slice(0, 5).map(t => (
                  <div key={t.id} className="flex items-center justify-between py-2 border-b border-[#7088A8]/10 last:border-0">
                    <div>
                      <p className="text-white text-sm">{new Date(t.started_at).toLocaleDateString()} — {t.distance_km.toFixed(1)} km</p>
                      <p className="text-xs text-[#7088A8]">{t.duration_minutes} min · {t.status}</p>
                    </div>
                    {t.driving_score !== undefined && (
                      <span className={`text-lg font-bold ${scoreColor(t.driving_score)}`}>{t.driving_score}</span>
                    )}
                  </div>
                ))}
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
