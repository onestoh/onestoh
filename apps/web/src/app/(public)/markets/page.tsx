import Link from 'next/link';

const markets = [
  { code: 'KE', flag: '🇰🇪', name: 'Kenya', currency: 'KES', status: 'live', payment: 'M-Pesa', users: '12,000+', registerPath: '/register' },
  { code: 'UG', flag: '🇺🇬', name: 'Uganda', currency: 'UGX', status: 'live', payment: 'MTN MoMo', users: '3,200+', registerPath: '/register/uganda' },
  { code: 'TZ', flag: '🇹🇿', name: 'Tanzania', currency: 'TZS', status: 'live', payment: 'Airtel Money', users: '1,800+', registerPath: '/register/tanzania' },
  { code: 'NG', flag: '🇳🇬', name: 'Nigeria', currency: 'NGN', status: 'live', payment: 'Paystack', users: '8,500+', registerPath: '/register/nigeria' },
  { code: 'GH', flag: '🇬🇭', name: 'Ghana', currency: 'GHS', status: 'live', payment: 'Paystack', users: '2,100+', registerPath: '/register/ghana' },
  { code: 'ZA', flag: '🇿🇦', name: 'South Africa', currency: 'ZAR', status: 'live', payment: 'PayFast / Stripe', users: '4,700+', registerPath: '/register/south-africa' },
  { code: 'RW', flag: '🇷🇼', name: 'Rwanda', currency: 'RWF', status: 'coming_soon', payment: 'MTN MoMo', users: null, registerPath: '/register' },
  { code: 'ET', flag: '🇪🇹', name: 'Ethiopia', currency: 'ETB', status: 'coming_soon', payment: 'Telebirr', users: null, registerPath: '/register' },
];

export default function MarketsPage() {
  return (
    <div className="min-h-screen bg-[#080C12]">
      <div className="max-w-6xl mx-auto px-4 py-16">
        <div className="text-center mb-12">
          <h1 className="text-4xl font-bold text-white mb-4">Pan-African Marketplace</h1>
          <p className="text-[#7088A8] text-lg max-w-2xl mx-auto">TheOnlineYard is live across 6 African countries with localised payments, languages, and support.</p>
        </div>
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          {markets.map(m => (
            <div key={m.code} className={`bg-[#141D2B] rounded-2xl p-6 border ${m.status === 'live' ? 'border-[#E8922A]/20' : 'border-[#7088A8]/10'}`}>
              <div className="flex items-center gap-3 mb-4">
                <span className="text-4xl">{m.flag}</span>
                <div>
                  <h3 className="text-white font-semibold text-lg">{m.name}</h3>
                  <span className={`text-xs px-2 py-0.5 rounded font-medium ${m.status === 'live' ? 'bg-[#2ECC8A]/10 text-[#2ECC8A]' : 'bg-[#7088A8]/10 text-[#7088A8]'}`}>{m.status === 'live' ? '● Live' : '○ Coming Soon'}</span>
                </div>
              </div>
              <div className="space-y-2 mb-4 text-sm">
                <div className="flex justify-between"><span className="text-[#7088A8]">Currency</span><span className="text-white">{m.currency}</span></div>
                <div className="flex justify-between"><span className="text-[#7088A8]">Payment</span><span className="text-white">{m.payment}</span></div>
                {m.users && <div className="flex justify-between"><span className="text-[#7088A8]">Users</span><span className="text-[#2ECC8A] font-medium">{m.users}</span></div>}
              </div>
              {m.status === 'live' ? (
                <Link href={m.registerPath} className="block w-full text-center bg-[#E8922A] hover:bg-[#E8922A]/90 text-white py-2.5 rounded-xl text-sm font-medium">Get Started</Link>
              ) : (
                <button disabled className="w-full bg-[#7088A8]/10 text-[#7088A8] py-2.5 rounded-xl text-sm cursor-not-allowed">Notify Me</button>
              )}
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}
