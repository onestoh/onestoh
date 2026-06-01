'use client';
import { useState, useEffect } from 'react';
import api from '@/lib/api';

interface YardGroupProfile { yard_id: string; linked_platforms: string[]; cross_referrals_earned: number; sso_enabled: boolean }
interface Referral { id: number; source_platform: string; target_platform: string; commission_earned: number; status: string; created_at: string }

export default function YardGroupPage() {
  const [profile, setProfile] = useState<YardGroupProfile | null>(null);
  const [referrals, setReferrals] = useState<Referral[]>([]);
  const [linking, setLinking] = useState(false);
  const [platformCode, setPlatformCode] = useState('');

  useEffect(() => {
    api.get('/yardgroup/profile').then(r => setProfile(r.data.data)).catch(() => {});
    api.get('/yardgroup/referrals').then(r => setReferrals(r.data.data)).catch(() => {});
  }, []);

  const initSso = (platform: string) => {
    api.post('/yardgroup/sso/initiate', { target_platform: platform }).then(r => {
      if (r.data.redirect_url) window.location.href = r.data.redirect_url;
    });
  };

  const platforms = [
    { id: 'estate_yard', name: 'EstateYard', icon: '🏠', desc: 'Property & real estate marketplace' },
    { id: 'motor_yard', name: 'MotorYard', icon: '🏎️', desc: 'Personal vehicle sales & auctions' },
  ];

  return (
    <div className="max-w-4xl mx-auto px-4 py-8">
      <div className="mb-8">
        <h1 className="text-2xl font-bold text-white mb-1">YardGroup — Super App Profile</h1>
        <p className="text-[#7088A8] text-sm">One identity, three marketplaces: TheOnlineYard, EstateYard, MotorYard.</p>
      </div>

      {profile && (
        <div className="bg-[#141D2B] rounded-xl p-5 mb-6 border border-[#E8922A]/20">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-[#7088A8] text-xs uppercase tracking-wider">YardGroup ID</p>
              <p className="text-white font-mono text-lg font-bold">{profile.yard_id}</p>
            </div>
            <div className="text-right">
              <p className="text-[#7088A8] text-xs">Referral Commission Earned</p>
              <p className="text-[#2ECC8A] text-xl font-bold">KES {profile.cross_referrals_earned.toLocaleString()}</p>
            </div>
          </div>
        </div>
      )}

      <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
        {platforms.map(p => (
          <div key={p.id} className="bg-[#141D2B] rounded-xl p-5 border border-[#7088A8]/10">
            <div className="flex items-center gap-3 mb-3">
              <span className="text-3xl">{p.icon}</span>
              <div><h3 className="text-white font-semibold">{p.name}</h3><p className="text-xs text-[#7088A8]">{p.desc}</p></div>
            </div>
            <button onClick={() => initSso(p.id)} className="w-full border border-[#E8922A] text-[#E8922A] hover:bg-[#E8922A] hover:text-white py-2 rounded-lg text-sm font-medium transition-colors">
              {profile?.linked_platforms?.includes(p.id) ? '✓ Linked — Open App' : `Link ${p.name}`}
            </button>
          </div>
        ))}
      </div>

      {referrals.length > 0 && (
        <div className="bg-[#141D2B] rounded-xl p-5">
          <h2 className="text-white font-semibold mb-4">Cross-Platform Referrals</h2>
          <div className="space-y-2">
            {referrals.map(r => (
              <div key={r.id} className="flex items-center justify-between py-2 border-b border-[#7088A8]/10 last:border-0">
                <div>
                  <p className="text-white text-sm">{r.source_platform} → {r.target_platform}</p>
                  <p className="text-xs text-[#7088A8]">{new Date(r.created_at).toLocaleDateString()}</p>
                </div>
                <div className="text-right">
                  <p className="text-[#2ECC8A] font-medium text-sm">+KES {parseFloat(String(r.commission_earned)).toLocaleString()}</p>
                  <span className={`text-xs px-1.5 py-0.5 rounded ${r.status === 'paid' ? 'text-[#2ECC8A] bg-[#2ECC8A]/10' : 'text-[#E8922A] bg-[#E8922A]/10'}`}>{r.status}</span>
                </div>
              </div>
            ))}
          </div>
        </div>
      )}
    </div>
  );
}
