'use client';
import { useState } from 'react';
import { useRouter } from 'next/navigation';
import Link from 'next/link';
import api from '@/lib/api';
import { useAuthStore } from '@/lib/store';

export default function SouthAfricaRegisterPage() {
  const router = useRouter();
  const setAuth = useAuthStore(s => s.setAuth);
  const [form, setForm] = useState({ name: '', email: '', phone: '', password: '', password_confirmation: '', country: 'ZA', role: 'client', business_name: '' });
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  const submit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setError('');
    try {
      const r = await api.post('/auth/register', form);
      setAuth(r.data.user, r.data.token);
      router.push('/client/dashboard');
    } catch (err: unknown) {
      const axiosErr = err as { response?: { data?: { message?: string } } };
      setError(axiosErr.response?.data?.message || 'Registration failed');
    } finally { setLoading(false); }
  };

  return (
    <div className="min-h-screen bg-[#080C12] flex items-center justify-center px-4">
      <div className="w-full max-w-md">
        <div className="text-center mb-8">
          <span className="text-3xl">🇿🇦</span>
          <h1 className="text-2xl font-bold text-white mt-2">Join TheOnlineYard South Africa</h1>
          <p className="text-[#7088A8] text-sm mt-1">Rent and buy vehicles across South Africa</p>
        </div>
        <form onSubmit={submit} className="bg-[#141D2B] rounded-2xl p-8 space-y-4">
          {error && <div className="bg-[#E05252]/10 border border-[#E05252]/30 rounded-lg px-4 py-3 text-[#E05252] text-sm">{error}</div>}
          <div><label className="text-[#7088A8] text-xs">Full Name</label><input required value={form.name} onChange={e => setForm(f=>({...f, name: e.target.value}))} className="w-full mt-1 bg-[#080C12] border border-[#7088A8]/20 rounded-lg px-3 py-2.5 text-white"/></div>
          <div><label className="text-[#7088A8] text-xs">Email</label><input type="email" required value={form.email} onChange={e => setForm(f=>({...f, email: e.target.value}))} className="w-full mt-1 bg-[#080C12] border border-[#7088A8]/20 rounded-lg px-3 py-2.5 text-white"/></div>
          <div><label className="text-[#7088A8] text-xs">Phone (SA)</label><div className="flex mt-1"><span className="bg-[#7088A8]/10 border border-r-0 border-[#7088A8]/20 rounded-l-lg px-3 py-2.5 text-[#7088A8] text-sm">+27</span><input value={form.phone} onChange={e => setForm(f=>({...f, phone: e.target.value}))} placeholder="712345678" className="flex-1 bg-[#080C12] border border-[#7088A8]/20 rounded-r-lg px-3 py-2.5 text-white"/></div></div>
          <div><label className="text-[#7088A8] text-xs">Business Name (optional)</label><input value={form.business_name} onChange={e => setForm(f=>({...f, business_name: e.target.value}))} className="w-full mt-1 bg-[#080C12] border border-[#7088A8]/20 rounded-lg px-3 py-2.5 text-white"/></div>
          <div><label className="text-[#7088A8] text-xs">I want to</label>
            <select value={form.role} onChange={e => setForm(f=>({...f, role: e.target.value}))} className="w-full mt-1 bg-[#080C12] border border-[#7088A8]/20 rounded-lg px-3 py-2.5 text-white">
              <option value="client">Rent / Buy vehicles</option><option value="owner">List my vehicles</option><option value="broker">Work as a broker</option>
            </select>
          </div>
          <div><label className="text-[#7088A8] text-xs">Password</label><input type="password" required value={form.password} onChange={e => setForm(f=>({...f, password: e.target.value}))} className="w-full mt-1 bg-[#080C12] border border-[#7088A8]/20 rounded-lg px-3 py-2.5 text-white"/></div>
          <div><label className="text-[#7088A8] text-xs">Confirm Password</label><input type="password" required value={form.password_confirmation} onChange={e => setForm(f=>({...f, password_confirmation: e.target.value}))} className="w-full mt-1 bg-[#080C12] border border-[#7088A8]/20 rounded-lg px-3 py-2.5 text-white"/></div>
          <div className="bg-[#080C12] rounded-lg p-3 text-xs text-[#7088A8]">💳 Payments processed via <span className="text-white">PayFast / Stripe</span> (ZAR cards, EFT, instant EFT)</div>
          <button type="submit" disabled={loading} className="w-full bg-[#E8922A] hover:bg-[#E8922A]/90 disabled:opacity-50 text-white py-3 rounded-xl font-semibold">{loading ? 'Creating account…' : 'Create Account'}</button>
          <p className="text-center text-[#7088A8] text-sm">Already have an account? <Link href="/login" className="text-[#E8922A] hover:underline">Sign in</Link></p>
        </form>
      </div>
    </div>
  );
}
